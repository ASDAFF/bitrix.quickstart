<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices;

class Sender {
	
	protected $transport = null; //главный шлюз
	protected $transport_r = null; //резервный шлюз
	protected $transport_app = null; //app шлюз
	protected $transport_name = null; //название главного шлюза
	protected $transport_r_name = null; //название резервного шлюза
	protected $transport_app_name = null; //название app шлюза
	public $reserve = false; //устанавливает флаг использования резервного шлюза при отправке
	public $app = false; //устанавливает флаг использования app шлюза при отправке
	public $translit = null;
	public $event = false;
	public $eventName = false;
	
	//конструктор
	//$defaultTransportInterface (true - установка настроек из параметров модуля, false - использовать свою логику установки);
	//$params - пока не задействован
	function __construct($defaultTransportInterface=true,$params=array()) {
		
		if($defaultTransportInterface){
			
			if($this->translit===null) $this->translit = (\Bitrix\Main\Config\Option::get("mlife.smsservices","translit","N","")=="Y") ? true : false;
			
			//основной шлюз
			$classname = \Bitrix\Main\Config\Option::get("mlife.smsservices","transport","","");
			if($classname){
				if(strpos($classname,'.php')!==false) $classname = str_replace(".php","",$classname);
				$classname = ToUpper(substr($classname,0,1)).substr($classname,1);
				$classname = "\\Mlife\\Smsservices\\Transport\\".$classname;
				if(class_exists($classname)){
				$paramsAr = array(
					'login' => \Bitrix\Main\Config\Option::get("mlife.smsservices","login",""),
					'passw' => \Bitrix\Main\Config\Option::get("mlife.smsservices","passw",""),
					'sender' => \Bitrix\Main\Config\Option::get("mlife.smsservices","sender",""),
					'charset' => ToLower(SITE_CHARSET),
				);
				$this->setTransport($classname,$paramsAr);
				}
			}
			
			//резервный шлюз
			$classname = \Bitrix\Main\Config\Option::get("mlife.smsservices","transport_r","","");
			if($classname){
				if(strpos($classname,'.php')!==false) $classname = str_replace(".php","",$classname);
				$classname = ToUpper(substr($classname,0,1)).substr($classname,1);
				$classname = "\\Mlife\\Smsservices\\Transport\\".$classname;
				if(class_exists($classname)){
				//$this->transport_r = new $classname;
				$paramsAr = array(
					'login' => \Bitrix\Main\Config\Option::get("mlife.smsservices","login_r",""),
					'passw' => \Bitrix\Main\Config\Option::get("mlife.smsservices","passw_r",""),
					'sender' => \Bitrix\Main\Config\Option::get("mlife.smsservices","sender_r",""),
					'charset' => ToLower(SITE_CHARSET),
				);
				$this->setTransport_r($classname,$paramsAr);
				}
			}
			
			//app шлюз
			$classname = \Bitrix\Main\Config\Option::get("mlife.smsservices","transport_app","","");
			
			if($classname){
				if(strpos($classname,'.php')!==false) $classname = str_replace(".php","",$classname);
				$classname = ToUpper(substr($classname,0,1)).substr($classname,1);
				$classname = "\\Mlife\\Smsservices\\Transport\\".$classname;
				
				if(class_exists($classname)){
				
				//$this->transport_app = new $classname;
				$paramsAr = array(
					'login' => \Bitrix\Main\Config\Option::get("mlife.smsservices","login_app",""),
					'passw' => \Bitrix\Main\Config\Option::get("mlife.smsservices","passw_app",""),
					'sender' => \Bitrix\Main\Config\Option::get("mlife.smsservices","sender_app",""),
					'charset' => ToLower(SITE_CHARSET),
				);
				$this->setTransport_app($classname,$paramsAr);
				
				}
			}
		
		}
	
	}
	
	public function setTransport($transport,$params){
		if(!class_exists($transport)) return false;
		$this->transport = new $transport($params);
		$this->transport_name = ToLower(str_replace("\\Mlife\\Smsservices\\Transport\\","",$transport));
	}
	
	public function setTransport_r($transport,$params){
		if(!class_exists($transport)) return false;
		$this->transport_r = new $transport($params);
		$this->transport_r_name = ToLower(str_replace("\\Mlife\\Smsservices\\Transport\\","",$transport));
	}
	
	public function setTransport_app($transport,$params){
		if(!class_exists($transport)) return false;
		$this->transport_app = new $transport($params);
		$this->transport_app_name = ToLower(str_replace("\\Mlife\\Smsservices\\Transport\\","",$transport));
	}
	
	/**
	* Метод для проверки номера телефона
	* @param string      $phone    номер телефона для проверки
	* @param boolean     $all      необязательный параметр по умолчанию true (весь мир), false (только снг)
	* @return array                phone - номер без мусора, check - результат проверки(boolean)
	*/
	public function checkPhoneNumber ($phone,$all=true) {
		
		//очистка от лишнего мусора
		$phoneFormat = '+'.preg_replace("/[^0-9A-Za-z]/", "", $phone);
		
		//проверка номера мир
		$pattern_world = "/^\+?([87](?!95[4-79]|99[08]|907|94[^0]|336)([348]\d|9[0-6789]|7[0247])\d{8}|[1246]\d{9,13}|68\d{7}|5[1-46-9]\d{8,12}|55[1-9]\d{9}|55[12]19\d{8}|500[56]\d{4}|5016\d{6}|5068\d{7}|502[45]\d{7}|5037\d{7}|50[4567]\d{8}|50855\d{4}|509[34]\d{7}|376\d{6}|855\d{8}|856\d{10}|85[0-4789]\d{8,10}|8[68]\d{10,11}|8[14]\d{10}|82\d{9,10}|852\d{8}|90\d{10}|96(0[79]|17[01]|13)\d{6}|96[23]\d{9}|964\d{10}|96(5[69]|89)\d{7}|96(65|77)\d{8}|92[023]\d{9}|91[1879]\d{9}|9[34]7\d{8}|959\d{7}|989\d{9}|97\d{8,12}|99[^4568]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|9989\d{8}|380[3-79]\d{8}|381\d{9}|385\d{8,9}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}|37[6-9]\d{7,11}|30[69]\d{9}|34[67]\d{8}|3[12359]\d{8,12}|36\d{9}|38[1679]\d{8}|382\d{8,9}|46719\d{10})$/";
		//проверка номера снг
		$pattern_sng = "/^((\+?7|8)(?!95[4-79]|99[08]|907|94[^0]|336)([348]\d|9[0-6789]|7[0247])\d{8}|\+?(99[^4568]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|9989\d{8}|380[34569]\d{8}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}))$/";
		
		if($all) {
			$patt = $pattern_world;
		}
		else {
			$patt = $pattern_sng;
		}
		
		if(!preg_match($patt, $phoneFormat)) {
			return array('phone'=>$phoneFormat,'check'=>false);
		}
		
		return array('phone'=>$phoneFormat,'check'=>true);
	
	}
	
	public function sendApp($phones, $MEWSS, $time=0, $sender=false, $prim='', $addHistory=true, $update=false, $error=false) {
		
		$appMess = $MEWSS;
		$smsMess = '';
		if(strpos($MEWSS,'|||')!==false){
			$t = explode('|||',$MEWSS);
			$appMess = $t[0];
			$smsMess = $t[1];
		}
		
		if($this->transport_app===null && !$error){
			$send = new \stdClass();
			$send->error = 'Transports not found';
			$send->error_code = '9998';
			return $this->sendApp($phones, $MEWSS, time(), $sender, $prim.', '.$send->error, $addHistory, $update, array('status'=>12, 'send'=>$send));
		}
		
		if(($time==0 || $time<time()) && !$error) {
			$time = 0;
			if($this->app){
				if($this->transport_app===null){
					$send = new \stdClass();
					$send->error = 'Transport reserve not found';
					$send->error_code = '9998';
				}else{
					try{
						$send = $this->transport_app->_sendSms($phones, $appMess, $time, $sender);
					}
					catch(\Exception $ex){
						$send = new \stdClass();
						$send->error = 'Transport class error';
						$send->error_code = '9998';
					}
				}
			}
			if($send->error_code){
				return $this->sendApp($phones, $MEWSS, time(), $sender, $prim.', '.$send->error, $addHistory, $update, array('status'=>12, 'send'=>$send));
			}
		}
		
		if($addHistory) {
			if(!$sender) $sender = \Bitrix\Main\Config\Option::get("mlife.smsservices","sender_app","","");
			if($time==0) {
				$time = time();
				$id = $send->id;
				$status = 2;
			}
			else{
				$id = '-';
				$status = 1;
				if($error) $status = $error['status'];
			}
				$arFields = array(
					'PROVIDER' => str_replace('.php','',\Bitrix\Main\Config\Option::get("mlife.smsservices","transport_app","","")),
					'SMSID' => $id,
					'SENDER' => $sender,
					'PHONE' => $phones,
					'TIME' => $time,
					'TIME_ST' => 0,
					'MEWSS' => $MEWSS,
					'PRIM' => $prim,
					'STATUS'=> $status
				);
				if($this->event) {
					$arFields['EVENT'] = $this->event;
				}else{
					$arFields['EVENT'] = 'DEFAULT';
				}
				if($this->eventName) $arFields['EVENT_NAME'] = $this->eventName;
				\Mlife\Smsservices\ListTable::add($arFields);
				if($status!=2 && $status!=1 && $smsMess){
					$this->reserve = false;
					$this->app = false;
					$res = $this->sendSms($phones, $smsMess);
					
					return $res;
				}
			if(!$error) {
				return $send;
			}
			else{
				return $error['send'];
			}
		}
		
		if ($update) {
			
			if($error) {
				$status = $error['status'];
				$sendid = '-';
			}else{
				$status=2;
				$sendid = $send->id;
			}
			$arData = array(
				"STATUS" => $status,
				"SMSID" => $sendid
			);
			\Mlife\Smsservices\ListTable::update(array("ID"=>$update['id']),$arData);
		}
		
		if(!$error) {
			return $send;
		}
		else{
			return $error['send'];
		}
		
	}
	
	//отправка смс, пост отправка, добавление записи в историю смс
	public function sendSms($phones, $MEWSS, $time=0, $sender=false, $prim='', $addHistory=true, $update=false, $error=false) {
			
			if($this->app === true){
				return $this->sendApp($phones, $MEWSS, $time, $sender, $prim, $addHistory, $update, $error);
			}
			
			$arParamsTranslit = array("max_len"=>"1000","change_case"=>"false","replace_space"=>" ","replace_other"=>"","","delete_repeat_replace"=>false,
			"safe_chars"=>'$%&*()_+=-#@!\'"./\\,<>?;:|~`№');
			if($this->translit===true) $MEWSS = \CUtil::translit($MEWSS,"ru",$arParamsTranslit);
			
			if($this->transport_r===null && $this->transport===null && !$error){
				$send = new \stdClass();
				$send->error = 'Transports not found';
				$send->error_code = '9998';
				return $this->sendSms($phones, $MEWSS, time(), $sender, $prim.', '.$send->error, $addHistory, $update, array('status'=>12, 'send'=>$send));
			}
			
			if(($time==0 || $time<time()) && !$error) {
				$time = 0;
				if($this->reserve){
					if($this->transport_r===null){
						$send = new \stdClass();
						$send->error = 'Transport reserve not found';
						$send->error_code = '9998';
					}else{
						try{
							$send = $this->transport_r->_sendSms($phones, $MEWSS, $time, $sender);
						}
						catch(\Exception $ex){
							$send = new \stdClass();
							$send->error = 'Transport class error';
							$send->error_code = '9998';
						}
					}
				}else{
					if($this->transport===null){
						$send = new \stdClass();
						$send->error = 'Transport not found';
						$send->error_code = '9998';
					}else{
						try{
							$send = $this->transport->_sendSms($phones, $MEWSS, $time, $sender);
						}
						catch(\Exception $ex){
							$send = new \stdClass();
							$send->error = 'Transport class error';
							$send->error_code = '9998';
						}
					}
				}
				if($send->error_code){
					//TODO надо хорошо потестировать, чтоб не зациклить
					if(!$this->reserve && $this->transport_r!==null){
						$this->app = false;
						$this->reserve = true;
						$res = $this->sendSms($phones, $MEWSS, $time, $sender, $prim, $addHistory, $update, $error);
						$this->reserve = false;
						return $res;
					}else{
						return $this->sendSms($phones, $MEWSS, time(), $sender, $prim.', '.$send->error, $addHistory, $update, array('status'=>12, 'send'=>$send));
					}
				}
			}

			if($addHistory) {
				if(!$sender) $sender = \Bitrix\Main\Config\Option::get("mlife.smsservices","sender".(($this->reserve) ? "_r" : ""),"","");
				if($time==0) {
					$time = time();
					$id = $send->id;
					$status = 2;
				}
				else{
					$id = '-';
					$status = 1;
					if($error) $status = $error['status'];
				}
					$arFields = array(
						'PROVIDER' => str_replace('.php','',\Bitrix\Main\Config\Option::get("mlife.smsservices","transport".(($this->reserve) ? "_r" : ""),"","")),
						'SMSID' => $id,
						'SENDER' => $sender,
						'PHONE' => $phones,
						'TIME' => $time,
						'TIME_ST' => 0,
						'MEWSS' => $MEWSS,
						'PRIM' => $prim,
						'STATUS'=> $status
					);
					if($this->event) {
						$arFields['EVENT'] = $this->event;
					}else{
						$arFields['EVENT'] = 'DEFAULT';
					}
					if($this->eventName) $arFields['EVENT_NAME'] = $this->eventName;
					\Mlife\Smsservices\ListTable::add($arFields);
				if(!$error) {
					return $send;
				}
				else{
					return $error['send'];
				}
			}
			
			if ($update) {
				
				if($error) {
					$status = $error['status'];
					$sendid = '-';
				}else{
					$status=2;
					$sendid = $send->id;
				}
				$arData = array(
					"STATUS" => $status,
					"SMSID" => $sendid
				);
				\Mlife\Smsservices\ListTable::update(array("ID"=>$update['id']),$arData);
			}
			
			if(!$error) {
				return $send;
			}
			else{
				return $error['send'];
			}
				
	}
	
	public function getBalance(){
		
		$arBalance = array(
			'main' => $this->getBalanceTransport(true),
			'reserve' => $this->getBalanceTransport(false),
			'app' => $this->getBalanceTransport('app'),
		);
		
		return $arBalance;
	
	}
	
	//метод получает отправителей + кеширует ответ
	public function getAllSender($main=true) {
		
		if($this->transport===null && $main===true) return false;
		if($this->transport_r===null && $main===false) return false;
		if($this->transport_app===null && $main==='app') return false;
		
		$obCache = \Bitrix\Main\Data\Cache::createInstance();
		if($main===true){
			$cache_time = \Bitrix\Main\Config\Option::get("mlife.smsservices","cacheotp","86400","");
			$cache_id = 'senders.'.$this->transport_name;
		}elseif($main===false){
			$cache_time = \Bitrix\Main\Config\Option::get("mlife.smsservices","cacheotp_r","86400","");
			$cache_id = 'senders.'.$this->transport_r_name;
		}elseif($main==='app'){
			$cache_time = \Bitrix\Main\Config\Option::get("mlife.smsservices","cacheotp_r","86400","");
			$cache_id = 'senders.'.$this->transport_app_name;
		}
		
		if( $obCache->initCache($cache_time,$cache_id,"/mlife/smsservices/admin/") )
		{
			$vars = $obCache->GetVars();
		}
		elseif( $obCache->startDataCache()  )
		{
			if($main){
			$vars = $this->transport->_getAllSender();
			}else{
			$vars = $this->transport_r->_getAllSender();
			}
			if(!$vars->error){
				$obCache->endDataCache($vars);
			}else{
				$obCache->abortDataCache();
			}
		}
		return $vars;
		
	}
	
	//хтмл списка отправителей (options)
	public function getAllSenderOptions($main=true) {
	
		if(\Bitrix\Main\Config\Option::get("mlife.smsservices", "listotp","","")!='Y' && $main===true) return '';
		if(\Bitrix\Main\Config\Option::get("mlife.smsservices", "listotp_r","","")!='Y' && $main===false) return '';
		if(\Bitrix\Main\Config\Option::get("mlife.smsservices", "listotp_app","","")!='Y' && $main=='app') return '';
	
		$data = $this->getAllSender($main);
		if($data->error){
			return '';
		}
		else {
		
			$html = '';
			if($main===true){
				$val = \Bitrix\Main\Config\Option::get("mlife.smsservices", "sender", ".","");
			}elseif($main===false){
				$val = \Bitrix\Main\Config\Option::get("mlife.smsservices", "sender_r", ".","");
			}elseif($main==='app'){
				$val = \Bitrix\Main\Config\Option::get("mlife.smsservices", "sender_app", ".","");
			}
			foreach ($data as $value){
				
				$selected = '';
				if($val==$value->sender){
				$selected = ' selected';
				}
				
				$html .= '<option value="'.$value->sender.'"'.$selected.'>'.$value->sender.'</option>';
				
			}
			
			return $html;
			
		}
		
	}
	
	//метод для получения баланса + кеширование ответа
	private function getBalanceTransport($main=true){
		
		if($this->transport===null && $main===true) return false;
		if($this->transport_r===null && $main===false) return false;
		if($this->transport_app===null && $main==='app') return false;
		
		$obCache = \Bitrix\Main\Data\Cache::createInstance();
		if($main===true){
			$cache_time = \Bitrix\Main\Config\Option::get("mlife.smsservices","cachebalance","3600","");
			$cache_id = 'balance.'.$this->transport_name;
		}elseif($main===false){
			$cache_time = \Bitrix\Main\Config\Option::get("mlife.smsservices","cachebalance_r","3600","");
			$cache_id = 'balance.'.$this->transport_r_name;
		}elseif($main==='app'){
			$cache_time = \Bitrix\Main\Config\Option::get("mlife.smsservices","cachebalance_app","3600","");
			$cache_id = 'balance.'.$this->transport_app_name;
		}
		
		if( $obCache->initCache($cache_time,$cache_id,"/mlife/smsservices/admin/") )
		{
			$vars = $obCache->GetVars();
		}
		elseif( $obCache->startDataCache()  )
		{
			if($main===true){
				$vars = $this->transport->_getBalance();
			}elseif($main===false){
				$vars = $this->transport_r->_getBalance();
			}elseif($main==='app'){
				$vars = $this->transport_app->_getBalance();
			}
			
			if(!$vars->error){
				$obCache->endDataCache($vars);
			}
			else{
				$obCache->abortDataCache();
			}
		}
		return $vars;
	}
	
	//получаем список неотправленных смс и отправляем их
	public function getTurnSms() {
		
		$arFilter = array();
		if($this->transport_name!==null) $arFilter["PROVIDER"][] = $this->transport_name;
		if($this->transport_r_name!==null) $arFilter["PROVIDER"][] = $this->transport_r_name;
		if($this->transport_app_name!==null) $arFilter["PROVIDER"][] = $this->transport_app_name;
		$arFilter["STATUS"] = 1;
		$arFilter["<TIME"] = time();
		
		$res = \Mlife\Smsservices\ListTable::getList(
			array(
				'select' => array('ID','SENDER','PHONE','MEWSS','PROVIDER'),
				'filter' => array($arFilter),
				'limit' => \Bitrix\Main\Config\Option::get("mlife.smsservices","limitsms",10,"")
			)
		);
		while ($data = $res->fetch()){
			usleep(100000); //на всякий случай не более 10 запросов в секунду (некоторые шлюзы могут блокировать ip)
			if($data["PROVIDER"]==$this->transport_r_name) {
				$this->reserve = true;
				$this->app = false;
			}elseif($data["PROVIDER"]==$this->transport_name){
				$this->reserve = false;
				$this->app = false;
			}elseif($data["PROVIDER"]==$this->transport_app_name){
				$this->app = true;
				$this->reserve = false;
			}
			$send = $this->sendSms($data['PHONE'], $data['MEWSS'], 0, $data['SENDER'], '', false, array('id'=>$data['ID']));
		}
	
	}
	
	//получаем необновленные статусы и обновляем
	public function getStatusSms() {
		
		$arFilter = array();
		if($this->transport_name!==null) $arFilter["PROVIDER"][] = $this->transport_name;
		if($this->transport_r_name!==null) $arFilter["PROVIDER"][] = $this->transport_r_name;
		
		if(!empty($arFilter)){
		
			$arFilter["STATUS"] = array(0,2,3,6);
			
			
			$res = \Mlife\Smsservices\ListTable::getList(
				array(
					'select' => array('*'),
					'filter' => $arFilter,
					'limit' => (\Bitrix\Main\Config\Option::get("mlife.smsservices","limitsms",10,"") * 2)
				)
			);
			
			while ($data = $res->fetch()){
				usleep(100000); //на всякий случай не более 10 запросов в секунду (некоторые шлюзы могут блокировать ip)
				//получаем статус со шлюза
				try{
					if($data["PROVIDER"]==$this->transport_r_name) {
						$resp = $this->transport_r->_getStatusSms($data['SMSID'],$data['PHONE']);
					}elseif($data["PROVIDER"]==$this->transport_name){
						$resp = $this->transport->_getStatusSms($data['SMSID'],$data['PHONE']);
					}else{
						$resp = new \stdClass();
						$data->error = 'Service not active. Params error.';
						$data->error_code = '9998';
						//return $data;
					}
				}
				catch(\Exception $ex){
					$resp = new \stdClass();
					$data->error = 'Transport Class ERROR. Params error.';
					$data->error_code = '9998';
				}
				//если нет ошибок обновляем в базе
				if(!$resp->error_code && in_array(intval($resp->status),array(1,2,3,4,5,6,7,8,9,10,11,12,14,15))) {
					if(!$resp->last_timestamp) $resp->last_timestamp = time();
					\Mlife\Smsservices\ListTable::update(array("ID"=>$data["ID"]),array("STATUS"=>$resp->status,"TIME_ST"=>$resp->last_timestamp));
				}else{
					if(!$resp->last_timestamp) $resp->last_timestamp = time();
					\Mlife\Smsservices\ListTable::update(array("ID"=>$data["ID"]),array("STATUS"=>12,"TIME_ST"=>$resp->last_timestamp));
				}
			}
		
		}
		
		$arFilter = array();
		
		if($this->transport_app_name!==null) {
			$arFilter["PROVIDER"][] = $this->transport_app_name;
			$arFilter["STATUS"] = array(0,2,3,6,4);
			
			$res = \Mlife\Smsservices\ListTable::getList(
				array(
					'select' => array('*'),
					'filter' => $arFilter,
					'limit' => (\Bitrix\Main\Config\Option::get("mlife.smsservices","limitsms",10,"") * 2)
				)
			);
			
			while ($data = $res->fetch()){
				usleep(100000); //на всякий случай не более 10 запросов в секунду (некоторые шлюзы могут блокировать ip)
				//получаем статус со шлюза
				try{
					if($data["PROVIDER"]==$this->transport_app_name){
						$resp = $this->transport_app->_getStatusSms($data['SMSID'],$data['PHONE']);
						
						/*\CEventLog::Add(array(
							"SEVERITY" => "SECURITY",
							"AUDIT_TYPE_ID" => $this->transport_app_name,
							"MODULE_ID" => "mlife.smsservices",
							"ITEM_ID" => $data['ID'],
							"DESCRIPTION" => print_r($resp,true),
						));
						*/
						
						if(!$data['TIME_ST']) $data['TIME_ST'] = time();
						if(!$data['TIME']) $data['TIME'] = time();
						$timeInterval = intval($data['TIME_ST']) - intval($data['TIME']);
						
						$sendSmsn = false;
						
						$smsMess = '';
						if(strpos($data['MEWSS'],'|||')!==false){
							$t = explode('|||',$data['MEWSS']);
							$smsMess = $t[1];
						}
						if($smsMess){
							if($resp->error_code){
								//ошибка получения статуса, отправить смс
								$sendSmsn = true;
							}elseif($timeInterval > intval(\Bitrix\Main\Config\Option::get("mlife.smsservices","limittimesms",600,""))){
								//время статуса больше n секунд
								if(in_array($resp->status,array(0,2,3,6,4))) {
									if($resp->status == 4 && ($timeInterval > (intval(\Bitrix\Main\Config\Option::get("mlife.smsservices","limittimesms",600,""))*2))){
										$resp->status = 15;
										$sendSmsn = true;
									}elseif($resp->status == 4){
										
									}else{
										$resp->status = 5;
										$sendSmsn = true;
									}
									//просрочено, отправить смс
								}elseif($resp->status != 14){
									$sendSmsn = true;
								}
							}else{
								if(($resp->status != 14) && ($resp->status != 4) && ($resp->status != 0) && ($resp->status != 2) && ($resp->status != 3) && ($resp->status != 6)) {
									//не доставлено, отправить смс
									$sendSmsn = true;
								}
							}
							if($sendSmsn) {
								$this->app = false;
								$this->reserve = false;
								$this->event = $data['EVENT'] ? $data['EVENT'] : false;
								$this->eventName = $data['EVENT_NAME'] ? $data['EVENT_NAME'] : false;
								$this->sendSms($data['PHONE'], $smsMess);
								$this->event = false;
								$this->eventName = false;
							}
						}else{
							if(in_array($resp->status,array(0,2,3,6,4))) {
								if($resp->status == 4 && ($timeInterval > (intval(\Bitrix\Main\Config\Option::get("mlife.smsservices","limittimesms",600,""))*2))){
									$resp->status = 15;
								}elseif($resp->status == 4){
									
								}else{
									$resp->status = 5;
								}
								//просрочено, отправить смс
							}
						}
					}else{
						$resp = new \stdClass();
						$data->error = 'Service not active. Params error.';
						$data->error_code = '9998';
						//return $data;
					}
					/*
					\CEventLog::Add(array(
						"SEVERITY" => "SECURITY",
						"AUDIT_TYPE_ID" => $this->transport_app_name,
						"MODULE_ID" => "mlife.smsservices",
						"ITEM_ID" => $data['ID'],
						"DESCRIPTION" => print_r($resp,true),
					));
					*/
					
				}
				catch(\Exception $ex){
					$resp = new \stdClass();
					$data->error = 'Transport Class ERROR. Params error.';
					$data->error_code = '9998';
				}
				//если нет ошибок обновляем в базе
				if(!$resp->error_code && in_array(intval($resp->status),array(1,2,3,4,5,6,7,8,9,10,11,12,14,15))) {
					if(!$resp->last_timestamp) $resp->last_timestamp = time();
					\Mlife\Smsservices\ListTable::update(array("ID"=>$data["ID"]),array("STATUS"=>$resp->status,"TIME_ST"=>$resp->last_timestamp));
				}else{
					if(!$resp->last_timestamp) $resp->last_timestamp = time();
					\Mlife\Smsservices\ListTable::update(array("ID"=>$data["ID"]),array("STATUS"=>12,"TIME_ST"=>$resp->last_timestamp));
				}
			}
			
		}
	
	}
	
	public function getStatusSmsForTransport($smsId,$reserve=false,$app=false) {
		if($app){
			$resp = $this->transport_app->_getStatusSms($smsId);
		}elseif($reserve){
			$resp = $this->transport_r->_getStatusSms($smsId);
		}else{
			$resp = $this->transport->_getStatusSms($smsId);
		}
		return $resp;
	}
	
	public function getStatusSmsForTransportUpdateHistory($smsId,$reserve=false,$app=false) {
		
		$arFin = array();
		
		$res = \Mlife\Smsservices\ListTable::getList(
			array(
				'select' => array('*'),
				'filter' => array('=SMSID'=>$smsId),
				//'filter' => array('>ID'=>$smsId,'PROVIDER'=>$this->transport_app_name),
				'limit' => (\Bitrix\Main\Config\Option::get("mlife.smsservices","limitsms",10,"") * 2)
			)
		);
		while($data = $res->fetch()){
		
			$resp = $this->getStatusSmsForTransport($data['SMSID'],$reserve,$app);
			$resp->last_id = $data['ID'];
			if(!$data['TIME_ST']) $data['TIME_ST'] = time();
			if(!$data['TIME']) $data['TIME'] = time();
			$timeInterval = intval($data['TIME_ST']) - intval($data['TIME']);
			
			if($timeInterval > intval(\Bitrix\Main\Config\Option::get("mlife.smsservices","limittimesms",600,""))){
				//время статуса больше n секунд
				if(in_array($resp->status,array(0,2,3,6,4))) {
					if($resp->status == 4 && ($timeInterval > (intval(\Bitrix\Main\Config\Option::get("mlife.smsservices","limittimesms",600,""))*2))){
						$resp->status = 15;
					}elseif($resp->status == 4){
						
					}else{
						$resp->status = 5;
					}
					//просрочено, отправить смс
				}
			}
			
			if(!$resp->error_code && in_array(intval($resp->status),array(1,2,3,4,5,6,7,8,9,10,11,12,14,15))) {
				if(!$resp->last_timestamp) $resp->last_timestamp = time();
				\Mlife\Smsservices\ListTable::update(array("ID"=>$data["ID"]),array("STATUS"=>$resp->status,"TIME_ST"=>$resp->last_timestamp));
			}else{
				if(!$resp->last_timestamp) $resp->last_timestamp = time();
				\Mlife\Smsservices\ListTable::update(array("ID"=>$data["ID"]),array("STATUS"=>12,"TIME_ST"=>$resp->last_timestamp));
			}
			$arFin[] = $resp;
		}
		
		if(count($arFin) == 1) return $arFin;
		
		return $arFin;
	}
	
}