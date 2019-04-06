<?
//обявляем класс для работы с смс шлюзом
$classname = COption::GetOptionString("mlife.smsservices","transport","smsc.php");
if(strpos($classname,'php')===false) $classname .= '.php';
require_once("transport/".$classname);


class CMlifeSmsServices extends CMlifeSmsTransport {
	
	//конструктор
	function __construct() {
		parent::__construct();
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
		$pattern_world = "/^\+?([87](?!95[4-79]|99[^2457]|907|94[^0]|336|986)([348]\d|9[0-689]|7[0247])\d{8}|[1246]\d{9,13}|68\d{7}|5[1-46-9]\d{8,12}|55[1-9]\d{9}|55119\d{8}|500[56]\d{4}|5016\d{6}|5068\d{7}|502[45]\d{7}|5037\d{7}|50[457]\d{8}|50855\d{4}|509[34]\d{7}|376\d{6}|855\d{8}|856\d{10}|85[0-4789]\d{8,10}|8[68]\d{10,11}|8[14]\d{10}|82\d{9,10}|852\d{8}|90\d{10}|96(0[79]|17[01]|13)\d{6}|96[23]\d{9}|964\d{10}|96(5[69]|89)\d{7}|96(65|77)\d{8}|92[023]\d{9}|91[1879]\d{9}|9[34]7\d{8}|959\d{7}|989\d{9}|97\d{8,12}|99[^4568]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|9989\d{8}|380[34569]\d{8}|381\d{9}|385\d{8,9}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}|37[6-9]\d{7,11}|30[69]\d{9}|34[67]\d{8}|3[12359]\d{8,12}|36\d{9}|38[1679]\d{8}|382\d{8,9})$/";
		//проверка номера снг
		$pattern_sng = "/^((\+?7|8)(?!95[4-79]|99[^2457]|907|94[^0]|336)([348]\d|9[0-689]|7[07])\d{8}|\+?(99[^456]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|380[34569]\d{8}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}))$/";
		
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
	
	//отправка смс, пост отправка, добавление записи в историю смс
	public function sendSms($phones, $mess, $time=0, $sender=false, $prim='', $addHistory=true, $update=false, $error=false) {
			//print_r($error['send']);
			if(($time==0 || $time<time()) && !$error) {
				$time = 0;
				$send = $this->_sendSms($phones, $mess, $time, $sender);
				if($send->error_code){
					//return $send;
					return $this->sendSms($phones, $mess, time(), $sender, $prim.', '.$send->error, $addHistory, $update, array('status'=>12, 'send'=>$send));
				}
			}

			if($addHistory) {
				if(!$sender) $sender = COption::GetOptionString("mlife.smsservices","sender");
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
						'provider' => str_replace('.php','',COption::GetOptionString("mlife.smsservices","transport","smsc.php")),
						'smsid' => $id,
						'sender' => $sender,
						'phone' => $phones,
						'time' => $time,
						'time_st' => 0,
						'mess' => $mess,
						'prim' => $prim,
						'status'=> $status
					);
					$sendsms = $this->addSms($arFields);
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
				$this->updateSmsStatus($update['id'],$status,$sendid);
			}
			
			if(!$error) {
				return $send;
			}
			else{
				return $error['send'];
			}
				
	}
	
	//метод получает отправителей + кеширует ответ (дополнительно CMlifeSmsTransport::_getAllSender())
	private function getAllSender() {
	
		$obCache = new CPHPCache();
		$cache_time = COption::GetOptionString("mlife.smsservices","cacheotp","86400");
		$cache_id = 'senders.'.COption::GetOptionString("mlife.smsservices","transport","smsc.php");
		if( $obCache->InitCache($cache_time,$cache_id,"/mlife/smsservices/admin/") )
		{
			$vars = $obCache->GetVars();
		}
		elseif( $obCache->StartDataCache()  )
		{
			$vars = $this->_getAllSender();
			if(!$vars->error){
				$obCache->EndDataCache($vars);
			}else{
				$obCache->AbortDataCache();
			}
		}
		return $vars;
		
	}
	
	//хтмл списка отправителей (options)
	public function getAllSenderOptions() {
	
		if(COption::GetOptionString("mlife.smsservices", "listotp")!='Y') return '';
	
		$data = $this->getAllSender();
		if($data->error){
			return '';
		}
		else {
		
			$html = '';
			$val = COption::GetOptionString("mlife.smsservices", "sender", ".");
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
	
	//метод для получения баланса + кеширование ответа (дополнительно CMlifeSmsTransport::_getBalance())
	public function getBalance(){
	
		$obCache = new CPHPCache();
		$cache_time = COption::GetOptionString("mlife.smsservices","cachebalance","3600");
		$cache_id = 'balance.'.COption::GetOptionString("mlife.smsservices","transport","smsc.php");
		
		if( $obCache->InitCache($cache_time,$cache_id,"/mlife/smsservices/admin/") )
		{
			$vars = $obCache->GetVars();
		}
		elseif( $obCache->StartDataCache()  )
		{
			$vars = $this->_getBalance();
			
			if(!$vars->error){
				$obCache->EndDataCache($vars);
			}
			else{
				$obCache->AbortDataCache();
			}
		}
		
		return $vars;
	
	}
	
	//получение истории смс
	public function GetList($arOrder, $arFilter, $arSelect) {
		return CMlifeSmsServicesSql::getList($arOrder, $arFilter, $arSelect);
	}
	
	//удаление смс из истории
	public function DeleteSms($id) {
		return CMlifeSmsServicesSql::DeleteSms($id);
	}
	
	//добавление смс в историю
	private function addSms($arFields) {
		return CMlifeSmsServicesSql::addSms($arFields);
	}
	
	//получаем список неотправленных смс и отправляем их
	public function getTurnSms() {
		
		global $DB;
		$arFilter['status'] = 1;
		$arFilter['sendto_unix'] = time();
		$arFilter['provider'] = str_replace('.php','',COption::GetOptionString("mlife.smsservices","transport","smsc.php"));
		$arSel = array('id','sender','phone','mess');
		$res = $this->GetList(array(),$arFilter,$arSel);
		
		while ($ob = $res->GetNext(false,false)){
			usleep(100000); //на всякий случай не более 10 запросов в секунду (некоторые шлюзы могут блокировать ip)
			$send = $this->sendSms($ob['phone'], $ob['mess'], 0, $ob['sender'], '', false, array('id'=>$ob['id']));
		}
	
	}
	
	//получаем необновленные статусы и обновляем
	public function getStatusSms() {
		
		global $DB;
		//$arFilter['status'] = 2;
		$arFilter['status'] = array(0,2,3,6);
		$arFilter['provider'] = str_replace('.php','',COption::GetOptionString("mlife.smsservices","transport","smsc.php"));
		$arSel = array('id','smsid','phone');
		$res = $this->GetList(array(),$arFilter,$arSel);
		
		while ($ob = $res->GetNext(false,false)){
		usleep(100000); //на всякий случай не более 10 запросов в секунду (некоторые шлюзы могут блокировать ip)
			//получаем статус со шлюза
			$resp = $this->getStatusSmsS($ob['smsid'],$ob['phone']);
			//если нет ошибок обновляем в базе
			if(!$resp->error_code) {
				$this->updateSmsStatus($ob['id'],$resp->status,false,$resp->last_timestamp);
			}
		}
		
	}
	
	//обновление статуса смс
	private function updateSmsStatus($id,$status_code,$sms_id=false,$time=false) {
		return CMlifeSmsServicesSql::updateSmsStatus($id,$status_code,$sms_id,$time);
	}
	
	//получение статуса сообщений со шлюза
	private function getStatusSmsS($smsid,$phone=false) {
		return $this->_getStatusSms($smsid,$phone);
	}

}
?>