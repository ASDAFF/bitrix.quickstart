<?
// sms-kontakt api
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/api/smskontakt_api.php");
// sms-bliss api
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/api/smsbliss_api.php");

if (!function_exists(smslog))
{
	function smslog($what)
	{
		$path = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sms/log.html";
		$f=fopen($path,"a+t");
		if($f){
			fwrite($f,"[".date("y/m/d H:i:s")."] ".$what."<br/>");
			fclose($f);
		}
	}
}

if (!function_exists(FormatPhone))
{
	function FormatPhone($phone)
	{
		return preg_replace("/\D/","",$phone);
	}
}

class CSMS
{	
	static function GetServiceInfo($serviceID = false)
	{
		if (!$serviceID)
		{
			$serviceID = COption::GetOptionString("sozdavatel.sms", "SMS_SERVICE");
			if ("SMSDISABLED" == $serviceID)
			{
				return false;
			}
		}
		
		$arService = array();
		
		if ("SMSKONTAKT" == $serviceID)
		{
			$arService["ID"] = "SMSKONTAKT";
			$arService["CURRENCY"] = "rub";
			$arService["ACCOUNT_LINK"] = "http://sms-kontakt.ru/panel/user/login/";
			
			$sender_id	= COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_SENDER_ID");
			$user_phone	= FormatPhone(COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_SENDER_PHONE"));
			$api_key	= COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_API_KEY");
			
			if ((!!$sender_id)&&(!!$user_phone)&&(!!$api_key))
			{
				$arService["BALANCE_PAY_LINK"] = "https://secure.onpay.ru/pay/sms_kontakt?f=7&pay_mode=free&pay_for=".$user_phone;
				
				$smskontakt = new SMSkontakt($sender_id, $user_phone, $api_key);
				$balance_result = $smskontakt->GetInfo('balance');
				$json_balance_result = json_decode($balance_result);
				$balance = $json_balance_result[0]->describe;
				
				if ($balance)
				{
					$arService["BALANCE"] = $balance;
				}
				
				$price_result = $smskontakt->GetInfo('personal_price');
				$json_price_result = json_decode($price_result);
				$price = $json_price_result[0]->describe;
				
				if ($price)
				{
					$arService["PRICE"] = $price;
				}
			}

		}
		
		if ("SMSBLISS" == $serviceID)
		{
			$arService["ID"] = "SMSBLISS";
			$arService["CURRENCY"] = "sms";
			$arService["ACCOUNT_LINK"] = "http://smsbliss.ru/users/login/";
			$arService["BALANCE_PAY_LINK"] = "http://smsbliss.ru/tariffs/#paySys";
			
			$bliss_login = COption::GetOptionString("sozdavatel.sms", "SMSBLISS_LOGIN");
			$bliss_password = COption::GetOptionString("sozdavatel.sms", "SMSBLISS_PASSWORD");
		
			if ((!!$bliss_login)&&(!!$bliss_password))
			{
				if (substr($bliss_login, 0, 3) == "szd")
				{	
					$bliss_gate = new Smsbliss_JsonGate($bliss_login, $bliss_password);
					$bliss_credits = $bliss_gate->credits(); 
					if ($bliss_credits["status"] == "ok")
					{
						$arService["BALANCE"] = $bliss_credits["credits"];
					}
				}
			}
		}
		
		return $arService;
	}
	
	function Send($message, $reciever_phone = false, $charset = LANG_CHARSET)
	{
		
		
		$sms_service = COption::GetOptionString("sozdavatel.sms", "SMS_SERVICE");
		$default_reciever_phone	= FormatPhone(COption::GetOptionString("sozdavatel.sms", "SMS_DEFAULT_RECIEVER_PHONE"));
		$send_copy_to_default	= (COption::GetOptionString("sozdavatel.sms", "SMS_SEND_COPY_TO_DEFAULT_RECIEVER") == "Y");
		
		$message = str_replace("+", "%2B", $message);
		global $APPLICATION;
        $message = $APPLICATION->ConvertCharset($message, $charset, "UTF-8");
        
		if ($sms_service == "SMSDISABLED")
		{
			return true;
		}
		else
		{
			if ((!$reciever_phone)&&(!$default_reciever_phone))
			{
				smslog("reciever_phone and default_reciever_phone not defined");
				return false;
			}
			
			$arMessages = Array();
			$phone_list = array();
			
			$clientId = 1;
			if ($reciever_phone)
			{
				$phone_list = explode(",",$reciever_phone);
				foreach($phone_list as $key=>$phone_item)
				{
					$to_phone = FormatPhone($phone_item);
					$phone_list[$key] = $to_phone;

					if ($to_phone)
					{
						$arMessages[] = array(
							"clientId" => "".$clientId,
							"phone"=> $to_phone,
							"text"=> $message,
							"sender"=> "",
							);
						$clientId++;
					}
				}
			}
			else
			{
				$arMessages[] = array(
					"clientId" => "".$clientId,
					"phone"=> $default_reciever_phone,
					"text"=> $message,
					"sender"=> "",
					);
				$clientId++;
			}
			
			if (($send_copy_to_default)&&($default_reciever_phone)&&(count($phone_list))&&($default_reciever_phone != $reciever_phone))
			{
				$arMessages[] = array(
										"clientId" => "".$clientId,
										"phone"=> $default_reciever_phone,
										"text"=> "(".$reciever_phone." copy)\n".$message,
										"sender"=> "",
									);
			}
			
			$result = false;
			
			if ($sms_service == "SMSBLISS")
			{
				// smsbliss.ru
				$bliss_login	= COption::GetOptionString("sozdavatel.sms", "SMSBLISS_LOGIN");
				$bliss_password	= COption::GetOptionString("sozdavatel.sms", "SMSBLISS_PASSWORD");
				
				/*
				if (substr($bliss_login, 0, 3) != "szd")
				{
					smslog("sms-bliss: incorrect login. please contact support");
					return false;
				}
				*/
				
				$bliss_sender_id= COption::GetOptionString("sozdavatel.sms", "SMSBLISS_SENDER_ID");
				$bliss_gate		= new Smsbliss_JsonGate($bliss_login, $bliss_password);
				
				//  
				foreach ($arMessages as $key=>$arMessage)
				{
					$arMessages[$key]["sender"] = $bliss_sender_id;
				}
				
				$bliss_result = $bliss_gate->send($arMessages); 
				
				if ($bliss_result["status"] != "ok")
				{
					smslog("sms-bliss: ".$bliss_result["status"]);
					return false;
				}
				else
				{
					$bliss_status = true;
					foreach ($bliss_result["messages"] as $key=>$ar_bliss_message)
					{
						if ($ar_bliss_message["status"] != "accepted")
						{
							$bliss_status = false;
							smslog("sms-bliss: error while sending message clientId #".$ar_bliss_message["clientId"]." - ".$ar_bliss_message["status"]);
							smslog(print_r($arMessages, true));
						}
					}
					$result = $bliss_status;	
				}			
			}
			elseif ($sms_service == "SMSKONTAKT")
			{
				// sms-kontakt.ru
				$kontakt_apiKey = COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_API_KEY");
				$kontakt_sender_id = COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_SENDER_ID");
				$kontakt_sender_phone = FormatPhone(COption::GetOptionString("sozdavatel.sms", "SMSKONTAKT_SENDER_PHONE"));
				
				// 
				$smskontakt = new SMSkontakt(	$kontakt_sender_id, 
												$kontakt_sender_phone, 
												$kontakt_apiKey, 
												$charset
											);
				$kontakt_status = true;
				foreach ($arMessages as $key=>$ar_kontakt_message)
				{
					$kontakt_result = $smskontakt->MessageSend($ar_kontakt_message["phone"], $ar_kontakt_message["text"]);
					$kontakt_json_result = json_decode($kontakt_result);
					$kontakt_json_status = $kontakt_json_result[0]->result;
					$kontakt_json_describe = $kontakt_json_result[0]->describe;
					if ($kontakt_json_status != "success")
					{
						smslog("sms-kontakt: sms to ".$ar_kontakt_message["phone"]." - ".$kontakt_json_status." - ".$kontakt_json_describe);
						$kontakt_status = false;
					}
				}
				$result = $kontakt_status;
			}
		
		    //copy sms to email, if enabled
            $copy_sms_to_email_phone = FormatPhone(COption::GetOptionString("sozdavatel.sms", "COPY_SMS_TO_EMAIL_PHONE"));
            $copy_sms_to_email_email = trim(COption::GetOptionString("sozdavatel.sms", "COPY_SMS_TO_EMAIL_EMAIL"));
            if ($copy_sms_to_email_email)
            {
                $email_subject = 'SMS: ';
				foreach ($phone_list as $key=>$phone)
				{
					if ($key > 0)
					{
						$email_subject .= ", ";
					}
					$email_subject .= $phone;
				}
				$emailCopySmsMsg = $APPLICATION->ConvertCharset($message, "UTF-8", SITE_CHARSET);
                $email_message = wordwrap($emailCopySmsMsg, 70);
                if ($copy_sms_to_email_phone)
                {
                    if (in_array($copy_sms_to_email_phone, $phone_list))
                    {
                        bxmail($copy_sms_to_email_email, $email_subject, $email_message);
                    }
                }
                else
                {
                    bxmail($copy_sms_to_email_email, $email_subject, $email_message);
                }
            }

			return $result;

		}
	}
}
