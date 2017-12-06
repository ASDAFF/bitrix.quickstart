<?
class smsru {
	
	private $get_token_url='http://sms.ru/auth/get_token';
	private $send_url='http://sms.ru/sms/send';
	private $status_url='http://sms.ru/sms/status';
	private $cost_url='http://sms.ru/sms/cost';
	private $balance_url='http://sms.ru/my/balance';
	private $limit_url='http://sms.ru/my/limit';
	private $senders_url='http://sms.ru/my/senders';
	
	private $partner_id='';
	private $requisites=array();
	
	//осуществляет передаче http запроса
	private function sms_request($URL, $PRM){
		if (empty($URL)) {return false;}
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		if (!empty($PRM)){curl_setopt($ch, CURLOPT_POSTFIELDS, $PRM);}
		$sms_response = curl_exec($ch);
		curl_close($ch);
		return $sms_response;
	}
	
	//авториазция в сервисе
	function login($PRM){
		$this->requisites=array();
		$this->requisites['token']=$this->sms_request($this->get_token_url);
		if (!empty($PRM['login']) and !empty($PRM['password'])){
			$this->requisites['sha512']=hash("sha512",$PRM['password'].$this->requisites['token']);
			$this->requisites['login']=$PRM['login'];
		} else {
			if (!empty($PRM['api_id'])){
				$this->requisites['api_id']=$PRM['api_id'];
			}
		}
		return $this;
	}
	
	//Совершает отправку СМС сообщения одному или нескольким получателям.
	function send($PRM){
		$arSMS = $this->requisites;
		if (!empty($PRM['to'])){$arSMS['to']=$PRM['to'];}
		if (!empty($PRM['text'])){$arSMS['text']=iconv(mb_detect_encoding($PRM['text']),"utf-8",$PRM['text']);}
		if (!empty($PRM['from'])){$arSMS['from']=$PRM['from'];}
		if (!empty($PRM['time'])){$arSMS['time']=$PRM['time'];}
		if (!empty($PRM['translit'])){$arSMS['translit']=$PRM['translit'];}
		if (!empty($PRM['test'])){$arSMS['test']=$PRM['test'];}
		if (!empty($this->partner_id)){$arSMS['partner_id']=$this->partner_id;}
		
		$sms_response=$this->sms_request($this->send_url,$arSMS);
		if ($c=preg_match_all ("/(\\d+).*?(\\d+)([-+]\\d+).*?(\\d+)/is", $sms_response, $e)){
			return array('response'=>$e[1][0],'id'=>$e[2][0].$e[3][0],'balance'=>$e[4][0]);
		} else {
			return array('response'=>mb_substr($sms_response, 0, 3, 'utf-8'),'id'=>0,'balance'=>0);
		}
	}
	
	//Проверка статуса отправленного сообщения.
	function status($SMS_ID){
		$arSMS = $this->requisites;
		$arSMS['id']=$SMS_ID;
		return mb_substr($this->sms_request($this->status_url,$arSMS), 0, 3, 'utf-8');
	}
	
	//Возвращает стоимость сообщения на указанный номер и количество сообщений, необходимых для его отправки.
	function cost($PRM){
		$arSMS = $this->requisites;
		if (!empty($PRM['to'])){$arSMS['to']=$PRM['to'];}
		if (!empty($PRM['text'])){$arSMS['text']=iconv(mb_detect_encoding($PRM['text']),"utf-8",$PRM['text']);}
		if (!empty($PRM['translit'])){$arSMS['translit']=$PRM['translit'];}
		
		$sms_response=explode("\n",$this->sms_request($this->cost_url,$arSMS));
		return array('response'=>$sms_response[0],'price'=>$sms_response[1],'length'=>$sms_response[2]);
	}
	
	//Получение состояния баланса
	function balance(){
		$arSMS = $this->requisites;
		$sms_response=explode("\n",$this->sms_request($this->balance_url,$arSMS));
		return array('response'=>$sms_response[0],'balance'=>$sms_response[1]);
	}
	
	//Получение текущего состояния вашего дневного лимита.
	function limit(){
		$arSMS = $this->requisites;
		$sms_response=explode("\n",$this->sms_request($this->limit_url,$arSMS));
		return array('response'=>$sms_response[0],'residue'=>$sms_response[1],'sent'=>$sms_response[2]);
	}

	//Получение списка отправителей.
	function senders(){
		$arSMS = $this->requisites;
		$sms_response=explode("\n",$this->sms_request($this->senders_url,$arSMS));
		foreach ($sms_response as $k=>$v){
			if ($k==0){
				$sms_resul['response']=$v;
			} else {
				if (!empty($v)){
					$sms_resul['senders'][$k-1]=$v;
				}
			}
		}
		return $sms_resul;
	}
}
?>
