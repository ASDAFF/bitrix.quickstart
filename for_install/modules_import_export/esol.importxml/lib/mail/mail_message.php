<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MailMessage
{
	function ParseHeader($message_header, $charset)
	{
		$h = new \Bitrix\EsolImportxml\MailHeader();
		$h->Parse($message_header, $charset);
		return $h;
	}

	private static function decodeMessageBody($header, $body, $charset)
	{
		$encoding = strtolower($header->GetHeader('CONTENT-TRANSFER-ENCODING'));

		if ($encoding == 'base64')
			$body = base64_decode($body);
		elseif ($encoding == 'quoted-printable')
			$body = quoted_printable_decode($body);
		elseif ($encoding == 'x-uue')
			$body = \Bitrix\EsolImportxml\MailUtil::uue_decode($body);

		$content_type = strtolower($header->content_type);
		if (
			preg_match('/plain|html|text/', $content_type)
			&& strpos($content_type, 'x-vcard') === false
			&& strpos($content_type, 'csv') === false
		)
		{
			$body = \Bitrix\EsolImportxml\MailUtil::convertCharset($body, $header->charset, $charset);
		}

		return array(
			'CONTENT-TYPE' => $content_type,
			'CONTENT-ID'   => $header->content_id,
			'BODY'         => $body,
			'FILENAME'     => $header->filename
		);
	}

	static function parseMessage($message, $charset)
	{
		$headerP = \CUtil::binStrpos($message, "\r\n\r\n");

		$rawHeader = \CUtil::binSubstr($message, 0, $headerP);
		$body      = \CUtil::binSubstr($message, $headerP+4);

		$header = self::ParseHeader($rawHeader, $charset);

		$htmlBody = '';
		$textBody = '';

		$parts = array();

		if ($header->IsMultipart())
		{
			if (!preg_match('/\r\n$/', $message))
				$message .= "\r\n";

			$startB = "\r\n--" . $header->GetBoundary() . "\r\n";
			$endB   = "\r\n--" . $header->GetBoundary() . "--\r\n";

			$startP = \CUtil::binStrpos($message, $startB)+\CUtil::binStrlen($startB);
			$endP   = \CUtil::binStrpos($message, $endB);

			$data = \CUtil::binSubstr($message, $startP, $endP-$startP);

			$isHtml = false;
			$rawParts = preg_split("/\r\n--".preg_quote($header->GetBoundary(), '/')."\r\n/s", $data);
			$tmpParts = array();
			foreach ($rawParts as $part)
			{
				if (\CUtil::binSubstr($part, 0, 2) == "\r\n")
					$part = "\r\n" . $part;

				list(, $subHtml, $subText, $subParts) = static::parseMessage($part, $charset);

				if ($subHtml)
					$isHtml = true;

				if ($subText)
					$tmpParts[] = array($subHtml, $subText);

				$parts = array_merge($parts, $subParts);
			}

			if (strtolower($header->MultipartType()) == 'alternative')
			{
				$candidate = '';

				foreach ($tmpParts as $part)
				{
					if ($part[0])
					{
						if (!$htmlBody || (strlen($htmlBody) < strlen($part[0])))
						{
							$htmlBody  = $part[0];
							$candidate = $part[1];
						}
					}
					else
					{
						if (!$textBody || strlen($textBody) < strlen($part[1]))
							$textBody = $part[1];
					}
				}

				if (!$textBody)
					$textBody = $candidate;
			}
			else
			{
				foreach ($tmpParts as $part)
				{
					if ($textBody)
						$textBody .= "\r\n\r\n";
					$textBody .= $part[1];

					if ($isHtml)
					{
						if ($htmlBody)
							$htmlBody .= "\r\n\r\n";

						$htmlBody .= $part[0] ?: $part[1];
					}
				}
			}
		}
		else
		{
			$bodyPart = static::decodeMessageBody($header, $body, $charset);

			if (!$bodyPart['FILENAME'] && strpos(strtolower($bodyPart['CONTENT-TYPE']), 'text/') === 0)
			{
				if (strtolower($bodyPart['CONTENT-TYPE']) == 'text/html')
				{
					$htmlBody = $bodyPart['BODY'];
					$textBody = html_entity_decode(htmlToTxt($bodyPart['BODY']), ENT_QUOTES | ENT_HTML401, $charset);
				}
				else
				{
					$textBody = $bodyPart['BODY'];
				}
			}
			else
			{
				$parts[] = $bodyPart;
			}
		}

		return array($header, $htmlBody, $textBody, $parts);
	}
}