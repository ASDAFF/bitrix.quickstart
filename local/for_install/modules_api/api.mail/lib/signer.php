<?php

namespace Api\Mail;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Signer
{
	private $storage = null;

	private $privatekey = null;
	private $publickey  = null;

	private $mail = null;
	private $dkim = null;


	public function __construct(array $params)
	{
		//$this->pkid = openssl_pkey_get_private($private_key);
		$this->privatekey = openssl_pkey_get_private($params['PRIVATE_KEY']);

		if($params['PUBLIC_KEY'])
			$this->publickey = openssl_pkey_get_public($params['PUBLIC_KEY']);

		//var_dump($res);
		//echo "<pre>"; print_r(openssl_pkey_get_details($this->publickey));echo "</pre>";

		$this->dkim = (array)$params['DKIM'];

		//$this->mail = \Bitrix\Main\Mail\Mail::createInstance((array)$params['MAIL']);
		$this->mail = (array)$params['MAIL'];

		$this->storage = (array)$params;
	}

	/**
	 * The "relaxed" Header RFC4871
	 *
	 * @link   http://tools.ietf.org/html/rfc4871#page-14
	 *
	 * array('mime-version' => 'mime-version:1.0')
	 */
	private function headers_relaxed($sHeaders)
	{
		$aHeaders = $lines = array();

		$sHeaders = preg_replace('/(?<!\r)\n/', "\r\n", $sHeaders);

		$sHeaders = preg_replace("/\n\s+/", " ", $sHeaders);
		//$sHeaders = preg_replace("/\r\n\s+/", " ", $sHeaders);

		$lines = explode("\r\n", $sHeaders);

		if($lines) {
			foreach($lines as $key => $line) {

				$line = preg_replace("/\s+/", ' ', $line);

				if(!empty($line)) {

					$line = explode(':', $line, 2);

					$header_type  = trim(strtolower($line[0]));
					$header_value = trim($line[1]);

					if(in_array($header_type, $this->dkim['h']) || $header_type == 'dkim-signature') {
						$aHeaders[ $header_type ] = $header_type . ':' . $header_value;
					}
				}
			}
		}

		return $aHeaders;
	}

	/**
	 * @link   http://tools.ietf.org/html/rfc4871#page-15
	 */
	private function body_relaxed($body)
	{
		$body = preg_replace('/(?<!\r)\n/', "\r\n", $body);

		$lines = explode("\r\n", $body);

		foreach($lines as $key => $value) {

			$value = rtrim($value);

			$lines[ $key ] = preg_replace('/\s+/', ' ', $value);
		}

		$body = implode("\r\n", $lines);

		$body = $this->body_simple($body);

		return $body;
	}

	/**
	 * Apply RFC 4871 requirements before body signature
	 */
	private function body_simple($body)
	{
		while(mb_substr($body, mb_strlen($body, 'UTF-8') - 4, 4, 'UTF-8') == "\r\n\r\n") {
			$body = mb_substr($body, 0, mb_strlen($body, 'UTF-8') - 2, 'UTF-8');
		}

		if(mb_substr($body, mb_strlen($body, 'UTF-8') - 2, 2, 'UTF-8') != "\r\n") {
			$body .= "\r\n";
		}

		return $body;
	}


	/**
	 * DKIM-Signature Header RFC4871
	 *
	 * @link   http://tools.ietf.org/html/rfc4871
	 *
	 */
	public function getDKIM()
	{
		$d = $this->dkim['d'];
		$s = $this->dkim['s'];
		$i = $this->dkim['i'];

		$mail = $this->mail;
		/*$mailResult = array(
			 $mail->getTo(),
			 $mail->getSubject(),
			 $mail->getBody(),
			 $mail->getHeaders(),
			 $mail->getAdditionalParameters(),
			 $mail->getContext(),
		);*/

		$body = $this->body_relaxed($mail['MESSAGE']);

		$l = strlen($body);
		$t = time();

		//Base64 of packed binary SHA-256 hash
		//$bh = base64_encode(sha1($body, true));
		//$bh = base64_encode(pack('H*', hash('sha256', $body)));
		//$bh = rtrim(chunk_split(base64_encode(pack("H*", sha1($body))), 64, "\r\n\t"));
		//for PHP < 5.3 $bh = base64_encode(pack("H*",sha1($body)));
		$bh = rtrim(chunk_split(base64_encode(pack('H*', hash('sha256', $body))), 60, "\r\n\t"));

		//$headers  = explode($mail->getMailEol(), $mail->getHeaders());
		$headers = $mail['HEADERS'] . "\r\n";
		$to      = $mail['TO'];
		$subject = $mail['SUBJECT'];

		if(!empty($to))
			$headers .= 'To: ' . $to . "\r\n";
		if(!empty($subject))
			$headers .= 'Subject: ' . $subject . "\r\n";

		$canonical_headers = $this->headers_relaxed($headers);


		// Creating DKIM-Signature
		//"v=", "a=", "b=", "bh=", "d=", "h=", and "s=" - Required tags
		/*$_dkim = "" .
			 "v=1; " .
			 "a=rsa-sha256; " .
			 "q=dns/txt; " .
			 "l={$_l}; " .
			 "s={$s};\r\n" .
			 "t={$_t}; " .
			 "c=relaxed/relaxed;\r\n" .
			 "h={$h};\r\n" .
			 "d={$d};\r\n" .
			 "z=$to_header\r\n" .
			 "|$subject_header\r\n" .
			 "|$from_header\r\n" .
			 "|$date_header;\r\n" .
			 "bh={$_bh};\r\n" .
			 "b=";
		*/

		//All DKIM keys are stored in a subdomain named "_domainkey".  Given a
		//DKIM-Signature field with a "d=" tag of "example.com" and an "s=" tag
		//of "foo.bar", the DNS query will be for
		//"foo.bar._domainkey.example.com".
		$dkim =
			 'DKIM-Signature: ' .
			 'v=1; ' .
			 'a=rsa-sha256; ' .
			 'c=relaxed/relaxed; ' .
			 'd=' . $d . '; ' .
			 'q=dns/txt;' . "\r\n\t" .
			 'l=' . $l . '; ' .
			 's=' . $s . '; ' .
			 't=' . $t . '; ' .
			 'i=' . $i . '; ' .
			 'h=' . implode(':', array_keys($canonical_headers)) . '; ' . "\r\n\t" .
			 'bh=' . $bh . ';' . "\r\n\t" .
			 'b=';


		$canonical_dkim = $this->headers_relaxed($dkim);
		$toSign         = implode("\r\n", $canonical_headers) . "\r\n" . $canonical_dkim['dkim-signature'];


		//Sign Canonicalization Header Data with Private Key
		//openssl_sign($toSign, $signature, $this->privatekey, 'sha256WithRSAEncryption');
		$signature = '';
		if(openssl_sign($toSign, $signature, $this->privatekey, OPENSSL_ALGO_SHA256)) {
			openssl_pkey_free($this->privatekey);

			// Base64 encoded signed data + Chunk Split it + Append $dkim
			$dkim .= rtrim(chunk_split(base64_encode($signature), 60, "\r\n\t")) . "\r\n";


			//DEBUG_DATA
			if($this->storage['DEBUG_DATA']) {

				//verify signature
				$verify = null;
				if($this->publickey) {
					$verify = openssl_verify($toSign, $signature, $this->publickey, OPENSSL_ALGO_SHA256);
					openssl_pkey_free($this->publickey);
				}

				$arResult = array(
					 'headers' => $headers,
					 'dkim'    => $dkim,
					 //'publickey' => $this->publickey,
					 //'privatekey' => $this->privatekey,
					 'verify'  => $verify,
				);

				$ttfile = dirname(__FILE__) . '/1_toSign.php';
				file_put_contents($ttfile, print_r($toSign, 1));

				$ttfile = dirname(__FILE__) . '/1_body.php';
				file_put_contents($ttfile, print_r($body, 1));

				$ttfile = dirname(__FILE__) . '/1_storage.php';
				file_put_contents($ttfile, print_r($this->storage, 1));

				$ttfile = dirname(__FILE__) . '/1_result.php';
				file_put_contents($ttfile, print_r($arResult, 1));
			}

			return $dkim;
		}

		return '';
	}
}