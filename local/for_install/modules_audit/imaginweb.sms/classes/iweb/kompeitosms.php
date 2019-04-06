<?php

class KompeitoSms {

	const STATE_QUEUED = 0;
	const STATE_INVALID_DST = 2;
	const STATE_INVALID_SRC = 3;
	const STATE_NO_MONEY = 4;
	const STATE_DIR_BLOCKED = 5;
	const STATE_LIMIT = 7;
	const STATE_MESSAGE_EMPTY = 50;
	const STATE_SERVICE_UNAVAILABLE = 100;

	const DELIVERY_WAITING = 1;
	const DELIVERY_SENT = 2;
	const DELIVERY_DELIVERED = 4;
	const DELIVERY_REJECTED = 5;
	const DELIVERY_FAILED = 6;

	const ADDR = "https://cabinet.kompeito.ru:443/api/xml";
	private $user;
	private $pass;
	private $from;


	public function __construct($user, $pass, $from=null) {
		$this->from = $from;
		$this->user = $user;
		$this->pass = $pass;
	}

	public function send($to, $message) {
		return $this->sendEx($this->from, $to, $message);
	}

	public function sendSingle($to, $message) {
		$tmp = $this->sendEx($this->from, $to, $message);
		$result = array();
		if (array_key_exists('error', $tmp)) {
			$result['error'] = $tmp['error'];
		}
		if (array_key_exists('count', $tmp)) {
			$result['count'] = $tmp['count'];
		}
		if (array_key_exists('data', $tmp) && is_array($tmp['data']) && count($tmp['data']) > 0) {
			return array_merge($result, $tmp['data'][0]);
		}
		return $result;
	}

	public function getStatusSingle($id) {
		while(is_array($id)) {
			$id = $id[0];
		}
		$result = $this->getStatus($id);
		if (array_key_exists('error', $result)) {
			return $result;
		} else {
			if (count($result) > 0) {
				return $result[0];
			} else {
				return null;
			}
		}
	}

	public function getStatus($ids) {
		$xml = new SimpleXMLElement("<getStatus></getStatus>");
		$this->addAuth($xml);
		if (is_array($ids)) {
			foreach ($ids as $i => $id) {
				$xml->sms[$i]['id'] = $id;
			}
		} else {
			$xml->sms['id'] = $ids;
		}
		$response = $this->doSend($xml);
		$result = array();
		if ($this->starts_with("<?xml", $response)) {
			$xmlRes = new SimpleXMLElement($response);
			foreach ($xmlRes->sms as $tmp) {
				$tmpResult = array();
				$tmpResult['id'] = (string)$tmp['id'];
				$tmpResult['req'] = date_create($tmp->requestTime);
				$parts = array();
				foreach ($tmp->part as $j => $p) {
					$partData = array();
					$partData['id'] = (string)$p["id"];
					$partData['status'] = (int)$p["status"];
					if ($p->completionTime) {
						$partData['fin'] = date_create($p->completionTime);
					}
					$parts[$j] = $partData;
				}
				$tmpResult['parts'] = $parts;
				array_push($result, $tmpResult);
			}
		} else {
			$result['error'] = (string)$response;
		}
		return $result;
	}

	public function getBalance() {
		$xml = new SimpleXMLElement("<getBalance></getBalance>");
		$this->addAuth($xml);
		$response = $this->doSend($xml);
		$result = array();
		if ($this->starts_with("<?xml", $response)) {
			$xmlResult = new SimpleXMLElement($response);
			$result['money']=(double)$xmlResult->money;
			$result['credits']=(double)$xmlResult->credits;
			$result['holdMoney']=(double)$xmlResult->holdMoney;
			$result['holdCredits']=(double)$xmlResult->holdCredits;
			$result['overdraft']=(double)$xmlResult->overdraft;
		} else {
			$result['error'] = (string)$response;
		}
		return $result;
	}

	public function sendEx($from, $to, $message) {
		$xmlResult = new SimpleXMLElement("<sendSms></sendSms>");
		$this->addAuth($xmlResult);

		$xmlResult->from=$from;

		if (is_array($to)) {
			foreach ($to as $i => $t) {
				$xmlResult->to[$i] = $t;
			}
		} else {
			$xmlResult->to = $to;
		}

		$xmlResult->message = $message;


		$response = $this->doSend($xmlResult);
		$result = array();
		if ($this->starts_with("<?xml", $response)) {
			$xml = new SimpleXMLElement($response);
			$result['count'] = (int)$xml->count;
			$result['data'] = array();
			foreach ($xml->to as $tmp) {
				$a = $tmp->attributes();
				$c = $tmp->children();
				$rep = array(
					"id" => (string)($a["id"]),
					'to' => (string)$a['phone'],
					'status' => (int)$a['status'],
					'credits' => (double)$c['credits'],
					'money' => (double)$c['money']
				);
				array_push($result['data'], $rep);
			}
		} else {
			$result['error'] = (string)$response;
		}
		return $result;
	}

	private function starts_with($str, $src){ 
		return substr($src, 0, strlen($str))==$str;
	}

	private function addAuth($xml) {
		$xml->auth->login = $this->user;
		$xml->auth->pass = $this->pass;
	}

	private function doSend($xml) {
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, KompeitoSms::ADDR);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());  

		$result = curl_exec($ch);

		$info = curl_getinfo($ch);
		curl_close($ch);

		if ($info['http_code'] != 200) {
			return "HTTP: ".$info['http_code'];
		}

		return $result;
	}
}


