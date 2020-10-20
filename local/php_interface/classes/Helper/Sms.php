<?php

namespace Helper;

/**
 * Class Sms
 * @package Helper
 */
class Sms
	{
		protected $login      = 'moshoztorg'; // логин сервиса
		protected $password   = 'pinyleny';   // пароль сервиса
		protected $maxParts   = 2;            // максимальное количество частей, на которое будет разбиваться длинное сообщение
		protected $isTranslit = true;         // транслитерировать текст сообщения
		protected $sender     = 'МосХозТорг';        // отправитель
		protected $udh        = '';           // какие-то специфичные для СМС данные (User Data Header)
		protected $text       = '';           // текст сообщения
		protected $phone      = '';           // телефон пользователя в формате 7ХХХХХХХ

		static function getInstance(){
			return new static;
		}

		function send($phone, $message)
		{
			$host = "api.smstraffic.ru";
			$failover_host = "server2.smstraffic.ru";
			$path = "/multi.php";
			$params = $this->getParams();

			$response=$this->httpPost($host, $path, $params);
			if ($response==null){
				$response=$this->httpPost($failover_host, $path, $params);
				if ($response==null)
					return array(0, "failed to send sms");
			}

			// interpret response
			if (strpos($response, '<result>OK</result>')){
				if (preg_match('|<sms_id>(\d+)</sms_id>|s', $response, $regs)){
					$sms_id=$regs[1];
					return array($sms_id, 'OK');
				}
				else // impossible
					return array(-1, 'failed to find sms_id');
			}

			elseif (preg_match('|<description>(.+?)</description>|s', $response, $regs)){
				$error=$regs[1];
				return array(0, $error);
			}

			else{
				return array(0, 'failed to send sms '.$response);
			}
		}

		protected function getParams(){
			$params = array(
				'login'        => $this->getLogin(),
				'password'     => $this->getPassword(),
				'want_sms_ids' => '1',
				'phones'       => $this->getPhone(),
				'message'      => $this->getText(),
				'max_parts'    => $this->getMaxParts(),
				'rus'          => $this->getIsTranslit() ? 1 : 0,
				'originator'   => $this->getSender()
			);

			if($udh = $this->getUdh()){
				$params['udh'] = $udh;
			}

			foreach($params as $i => $param){
				$params[$i] = mb_convert_encoding($param, 'Windows-1251', 'UTF-8');
			}

			return http_build_query($params);
		}

		protected function httpPost($host, $path, $params){
			return $this->httpPostSock($host, $path, $params);
			// return $this->httpPostCurl($host, $path, $params);
		}

		protected function httpPostSock($host, $path, $params){
			$params_len=strlen($params);
			$fp = @fsockopen($host, 80);
			if (!$fp)
				return null;
			fputs($fp, "POST $path HTTP/1.0\nHost: $host\nContent-Type: application/x-www-form-urlencoded\nUser-Agent: sms.php class 1.0 (fsockopen)\nContent-Length: $params_len\nConnection: Close\n\n$params\n");
			$response = fread($fp, 8000);
			fclose($fp);
			if (preg_match('|^HTTP/1\.[01] (\d\d\d)|', $response, $regs))
				$http_result_code=$regs[1];
			return ($http_result_code==200) ? $response : null;
		}

		protected function httpPostCurl($host, $path, $params){
			$protocol='http'; // alternatively, use https
			$ch = curl_init($protocol.'://'.$host.$path);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // do not verify that ssl cert is valid (it is not the case for failover server)
			curl_setopt($ch, CURLOPT_USERAGENT, "sms.php class 1.0 (curl $protocol)");
			curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 seconds
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

			ob_start();
			$bSuccess=curl_exec($ch);
			$response=ob_get_contents();
			ob_end_clean();
			$http_result_code=curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			return ($bSuccess && $http_result_code==200) ? $response : null;
		}


		// getters and setters:

		public function getLogin(){
		    return $this->login;
		}
		
		public function setLogin($login){
		    $this->login = $login;
		    return $this;
		}

		public function getPassword(){
		    return $this->password;
		}
		
		public function setPassword($password){
		    $this->password = $password;
		    return $this;
		}

		public function getMaxParts(){
		    return $this->maxParts;
		}
		
		public function setMaxParts($maxParts){
		    $this->maxParts = $maxParts;
		    return $this;
		}

		public function getIsTranslit(){
		    return $this->isTranslit;
		}
		
		public function setIsTranslit($isTranslit){
		    $this->isTranslit = $isTranslit;
		    return $this;
		}

		public function getSender(){
		    return $this->sender;
		}
		
		public function setSender($sender){
		    $this->sender = $sender;
		    return $this;
		}

		public function getUdh(){
		    return $this->udh;
		}
		
		public function setUdh($udh){
		    $this->udh = $udh;
		    return $this;
		}

		public function getText(){
		    return $this->text;
		}
		
		public function setText($text){
		    $this->text = $text;
		    return $this;
		}

		public function getPhone(){
		    return $this->phone;
		}

		public function setPhone($phone){
			$phone = trim($phone);
			$phone = preg_replace('/^(\+7|8)/', '7', $phone);
			$phone = preg_replace('/[^\d]+/', '', $phone);
		    $this->phone = $phone;
		    return $this;
		}


	}
