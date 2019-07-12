<?require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

//this
class Model_Apitm {
	
	public $ip;
	public $port;
	public $api_name = 'common_api';
	public $api_version = '1.0';
	public $api_key = '';
	public $timeout = 1;

	public function setIp($ip='0.0.0.0') {
		$this->ip = $ip;
		return $this;
	}
	
	public function setPort($port=8080) {
		$this->port = $port;
		return $this;
	}
	
	public function setKey($key='') {
		$this->api_key = $key;
	return $this;
	}
	
	
	public function get($method, $params = '') {
		if (is_array($params)) $params = http_build_query($params);
		$sign = $this->getSignature($params);
	// $params .= ( strlen($params) ? '&' : '')."signature=$sign";
		$url = $this->getUrl($method) . "?" . $params;
	
		$context = stream_context_create(array(
			'https'=>array(
			'method'=>'GET',
			'header'=>"Signature: $sign",
			'timeout'=>$this->timeout,
			)
		));
		try {
		ini_set('default_socket_timeout', 2);
			$response = file_get_contents($url, false, $context);
			$response = json_decode($response);
		} catch (Exception $e) {
			$response = false;
		}
			return $response;
	}
	
	public function post($method, $params = '') {
		if (is_array($params)) $params = http_build_query($params);
	//    return $this->oldPost($method, $params);
	return  $this->sendPOSTRequest($method, $params);
	}
	
	public function oldPost($method, $params)
	{
		$url = $this->getUrl($method);
		$sign = $this->getSignature($params);
		$context = stream_context_create(array(
			'https'=>array(
			'method'=>'POST',
			'header'=>"Signature: $sign\r\n".
				"Content-Type: application/x-www-form-urlencoded\r\n",
			"Content-Length: " . strlen($params),
			'content'=>$params,
			'timeout'=>$this->timeout,
			)
	));
	
	try {
		$response = file_get_contents($url, false, $context);
		$response = json_decode($response);
	} catch (Exception $e) {
		$response = false;
	}
	return $response;
	}
	
	private function getUrl($method) {
		return "https://{$this->ip}:{$this->port}/{$this->api_name}/{$this->api_version}/{$method}";
	}
	
	private function getSignature($params) {
		return MD5($params . $this->api_key);
	}
	
	public function sendPOSTRequest($apiMethod, $requestData)
	{
		$ch = curl_init($this->getUrl($apiMethod));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Signature: ' . $this->getSignature($requestData),
			'Content-Type: application/x-www-form-urlencode'
		));
	
		$result = curl_exec($ch);
		$errorCode = curl_errno($ch);
		curl_close($ch);
		//print_r($result);
		return ($errorCode == CURLE_OK)	? json_decode($result) : false;
	}
}


CModule::IncludeModule ('iblock');
$send = new Model_Apitm();

$db_element = CIBlockElement::GetList(false, array('NAME'=>'taxi_master', 
													'IBLOCK_CODE'=>'connect'), false, false, array(
													'ID', 
													'IBLOCK_ID', 
													'PROPERTY_HOST', 
													'PROPERTY_KEY', 
													'PROPERTY_PORT', 
													'PROPERTY_SECRET'));
if ($element = $db_element->GetNext()){
	$send->setIp($element['PROPERTY_HOST_VALUE']);
	$send->setPort($element['PROPERTY_PORT_VALUE']);
	$send->setKey($element['PROPERTY_KEY_VALUE']);
	
	$method = 'create_order HTTP/1.1';
	
	$data = 'phone=134543678901&source=red-square&source_time=19990515112233&dest=default';
	
	$result = $send->sendPOSTRequest($method, $data);
	echo '<pre>'; print_r($result); echo'</pre>';
}

?>