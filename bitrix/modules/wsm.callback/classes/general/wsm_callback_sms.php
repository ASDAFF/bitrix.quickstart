<?
class WSMCallbackSMS {

	const MODULE_ID = 'wsm.callback';
	var $MODULE_ID = 'wsm.callback'; 
	
	function GetModules(){

		$arrSmsService = array(
			
			'rarus.sms4bcontacts' => array(
				'NAME' => 'SMS4Bcontacts',
				'SITE' => 'sms4b.ru',
				'TARIF' => '/prices/',
				),
			);

		foreach($arrSmsService as $modul_id => $serv){
			//проверка наличия модуля в системе
			$arrSmsService[$modul_id]['INSTALLED'] = (CModule::IncludeModule($modul_id)) ? true : false ;
			}

		return $arrSmsService;
    }
	
	function SendSms($data, $SITE_ID = '')
	{		
		$sms_service = COption::GetOptionString(self::MODULE_ID, 'sms_service', '', $SITE_ID);

		$sms_time = COption::GetOptionString(self::MODULE_ID, 'sms_time', '', $SITE_ID);
		$sms_time = explode(',', $sms_time);
		
		$sms_translit = COption::GetOptionString(self::MODULE_ID, 'sms_translit', '', $SITE_ID);
		$sms_translit = $sms_translit == 'Y' ? 'Y' : 'N' ;
		
		//расчет переноса отправки
		if( date("H")>=$sms_time[0] && date("H")<=$sms_time[1]) {
			$StartSend = date("Y-m-d H:i:s", time());
		}	
		elseif(date("H") < $sms_time[0]){
			$StartSend = date("Y-m-d", time())." ".sprintf('%02d', $sms_time[0] ).":00:00";
		}
		elseif(date("H") > $sms_time[1]){
			$arrAdd = array("DD" => 1);
			$stmp2 = AddToTimeStamp($arrAdd, time());
			$StartSend = date("Y-m-d", $stmp2)." ".sprintf('%02d', $sms_time[0] ).":00:00";
		}

		$data['SEND_DATE'] = $StartSend;
		
		if(strlen($sms_service) > 0 && CModule::IncludeModule($sms_service) )
		{	
			switch ($sms_service)
			{
				case 'rarus.sms4bcontacts':
					
					global $APPLICATION, $SMS4B;
					$message = $sms_translit == "Y" ? $SMS4B->Translit($data['MESSAGE']) : $data['MESSAGE'];

					$sender = COption::GetOptionString(self::MODULE_ID, 'sms_service_sender', '', $SITE_ID);

					//отправляем СМС
					if(count($data['PHONE']))
					{
						foreach($data['PHONE'] as $phone)
						{
							$to = $SMS4B->is_phone($phone);
						
							if(strlen($sender) > 0 && $to && strlen($message) > 0)
							{	
								$ston = $SMS4B->get_ton($sender);
								$snpi = $SMS4B->get_npi($sender);

								$dton = $SMS4B->get_ton($to);
								$dnpi = $SMS4B->get_npi($to);
								
								$body = $SMS4B->enCodeMessage($message);
								$encoded = $SMS4B->get_type_of_encoding($message);
								
								$sess_id = $SMS4B->GetSID();
								$date_actual = date("Ymd H:i:s",(time()+86400*2));
								$outsms_guid = $SMS4B->CreateGuid();

								$params_sms = 	array(
									"SessionID" => $SMS4B->GetSID(),
									"guid" => $outsms_guid,
									"Destination" => $to,
									"Source" => $sender,
									"Body" => $body,
									"Encoded" => $encoded,
									"dton" => $dton,
									"dnpi" => $dton,
									"ston" => $ston,
									"snpi" => $snpi,
									"TimeOff" =>$date_actual,
									"Priority" => 0,
									"NoRequest" => 0
									);
									
								$resSendMess = $SMS4B->GetSOAP("SaveMessage",$params_sms);
								
								$arrparam[] = array(
									"GUID" => $outsms_guid,
									"SenderName" => $sender,
									"Destination" => $to,
									"StartSend" => $StartSend,
									"LastModified" => date("Y-m-d H:i:s", time()),
									"CountPart" => $resSendMess["SEND"] > 0 ? $resSendMess["SEND"] : 0,
									"SendPart" => $resSendMess["OK"] > 0 ? $resSendMess["OK"] : 0,
									"CodeType" => $encoded,
									"TextMessage" => $message,
									"Sale_Order" => 0,
									"Status" => 5,
									"Posting" => 0,
									"Events" => ''
								);
								$SMS4B->ArrayAdd($arrparam);
								
							}
						}
					}

				break;
				
				case 'imaginweb.sms':
					
				break;
				
				case 'webdebug.sms':
					
				break;
				
				case 'giveandget.smssubscribe':
					
				break;
				
				case 'sozdavatel.sms':
					
				break;
				
				default:
					return false;
				break;
			}
		}
		elseif(strlen($sms_service) > 0 && $sms_service == 'other')	
		{
			$other_data = $data;
			$other_data['TIME'] = $sms_time;
			$other_data['TRANSLIT'] = $sms_translit;
			
			if( date("H")>=$sms_time[0] && date("H")<=$sms_time[1] )
			{
				$events = GetModuleEvents("wsm.callback", "OnSmsSend");
				while ($arEvent = $events->Fetch())
					ExecuteModuleEventEx($arEvent, array(&$other_data));
			}
		}
	}
}
?>