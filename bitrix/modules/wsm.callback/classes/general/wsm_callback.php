<?

IncludeModuleLangFile(__FILE__);

class WSMCallback {

	const MODULE_ID = 'wsm.callback';
	var $last_error = '';
	var $message = '';

	//добавление заявки
	
	function Add($fields, $site_id = ''){

		GLOBAL $APPLICATION;

		$site_id = htmlspecialcharsEx($site_id);
		$IBLOCK_ID = COption::GetOptionInt(self::MODULE_ID, 'iblock', 0, $site_id);

		$form_message_add = COption::GetOptionString(self::MODULE_ID, 'form_message_add', '', $site_id);
		$form_message_add = htmlspecialcharsBack ($form_message_add);

		if(!CModule::IncludeModule("iblock"))
			$err[] = GetMessage("WSM_CALLBACK_ADD_NA_IBLOCK");

		if($site_id == '')
			$err[] = GetMessage("WSM_CALLBACK_SITEID_NA");

		if($IBLOCK_ID <= 0)
			$err[] = GetMessage("WSM_CALLBACK_ADD_ERRSET");

		$CAPTCHA = COption::GetOptionString(self::MODULE_ID, 'form_captcha', 'N', $site_id);
		$CAPTCHA = $CAPTCHA == 'Y' ? true : false ;

		$PROPERTY_TIME = COption::GetOptionInt(self::MODULE_ID, 'iblock_property_time', 0, $site_id);

		if(count($err) > 0)
		{
			$this->last_error = implode('<br/>', $err);
			$this->message = GetMessage("WSM_CALLBACK_ADD_ERROR_DESC");
			return false;
		}

		$err = array();
		$arProp = array();
		$arProp_code = array();

		$PROP = array();

		$arFilter = Array(
			'IBLOCK_ID' => $IBLOCK_ID, 
			'ACTIVE' 	=> 'Y',
			);

		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), $arFilter );
		while ($prop_fields = $properties->GetNext())
		{
			$arProp[$prop_fields['ID']] = $prop_fields;
			$arProp_code[$prop_fields['ID']] = $prop_fields;
		}

		$fields['NAME'] = trim($fields['NAME']);
		$fields['NAME'] = htmlspecialcharsEx($fields['NAME']);

		if(strlen($fields['NAME']) == 0)
			$err[] = GetMessage("WSM_CALLBACK_ADD_INP_NAME");

		$keys = array_keys($arProp_code);

		$fields[$PROPERTY_TIME.'_FROM'] = intval($fields[$PROPERTY_TIME.'_FROM']);
		$fields[$PROPERTY_TIME.'_TO'] = intval($fields[$PROPERTY_TIME.'_TO']);

		if(($fields[$PROPERTY_TIME.'_TO'] < $fields[$PROPERTY_TIME.'_FROM']) && ($fields[$PROPERTY_TIME.'_TO'] > 0 && $fields[$PROPERTY_TIME.'_FROM'] > 0))
			$err[] = GetMessage("WSM_CALLBACK_ADD_ERR_PERIOD");

		if($PROPERTY_TIME >=0)
		{
			$fields[$PROPERTY_TIME] = $fields[$PROPERTY_TIME.'_FROM'].'-'.$fields[$PROPERTY_TIME.'_TO'];
			unset($fields[$PROPERTY_TIME.'_FROM']);
			unset($fields[$PROPERTY_TIME.'_TO']);
		}

		if(trim($fields[$PROPERTY_TIME]) == '-')
			$fields[$PROPERTY_TIME] = '';

		foreach($fields as $id => $val)
		{
			$val = trim($val);
			$val = htmlspecialcharsEx($val);

			if(in_array($id, $keys))
			{
				switch ($arProp_code[$id]["PROPERTY_TYPE"])
				{
					case 'S':
						$val = htmlspecialcharsEx(substr($val, 0, 255));
					break;

					case 'N':
					case 'L':
					case 'E':
					case 'G':
						$val = intval($val);
					break;
				}
				
				if($arProp_code[$id]['IS_REQUIRED'] == 'Y')
				{
					switch ($arProp_code[$id]["PROPERTY_TYPE"])
					{
						case 'S':
							if(strlen($val) == 0)
								$err[] = GetMessage("WSM_CALLBACK_ADD_EMPTY_FILED").' "'.$arProp_code[$id]['NAME'].'"';
						break;

						case 'N':
						case 'L':
							if($val == 0)
								$err[] = GetMessage("WSM_CALLBACK_ADD_EMPTY_FILED").' "'.$arProp_code[$id]['NAME'].'"';
						break;
					}
				}
				
				$PROP[$id] = $val;
			}
		}
		
		if($CAPTCHA && $fields["captcha_word"] == '')
			$err[] = GetMessage("WSM_CALLBACK_ADD_EMPTY_CAPTCHA");	
		elseif ($CAPTCHA && !$APPLICATION->CaptchaCheckCode($fields["captcha_word"], $fields["captcha_sid"]) )
			$err[] = GetMessage("WSM_CALLBACK_ADD_ERR_CAPTCHA");	

		if(count($err) > 0)
		{
			$this->last_error = implode('<br/>', $err);
			$this->message = GetMessage("WSM_CALLBACK_ADD_ERROR_DESC");
			return false;
		}
		
		$el = new CIBlockElement;

		$arLoadProductArray = Array(
			"IBLOCK_ID"      => $IBLOCK_ID,
			"IBLOCK_SECTION_ID" => false,
			"NAME"           => $fields['NAME'],
			"ACTIVE"         => "Y",
			"PROPERTY_VALUES"=> $PROP,
			);

		if($PRODUCT_ID = $el->Add($arLoadProductArray))
		{
			$this->message = str_replace(array('#ID#', '#DATE#', '#NAME#'), array($PRODUCT_ID, date('d.m.Y'), $fields['NAME']), $form_message_add);
			self::SendNotice($PRODUCT_ID, $fields['SITE_ID']);
			return $PRODUCT_ID;
		}	
		else
		{
			$this->last_error = $el->LAST_ERROR;
			$this->message = GetMessage("WSM_CALLBACK_ADD_ERROR_DESC");
			return false;
		}
	}

	function SendNotice(&$ID, $site_id ) {

		$ID = intval($ID);

		if($ID <= 0 || $site_id === '') 
			return false;

		//property
		$PROPERTY_THEME = COption::GetOptionInt(self::MODULE_ID, 'iblock_property_theme', 0, $site_id);

		//property to notice SMS?
		$notice_iblock_property = COption::GetOptionString(self::MODULE_ID, 'notice_iblock_property', '', $site_id);
		$notice_iblock_property = explode(',', $notice_iblock_property);
		
		//main contact
		$notice_email = COption::GetOptionString(self::MODULE_ID, 'notice_email', '', $site_id);
		$notice_phone = COption::GetOptionString(self::MODULE_ID, 'notice_phone', '', $site_id);

		//send notise to main contacts?
		$use_main_email = COption::GetOptionString(self::MODULE_ID, 'notice_send_to_main_always', 'N', $site_id);
		$use_main_email = $use_main_email == 'Y' ? 'Y' : 'N' ;
		$use_main_phone = $use_main_email;
		
		$emails = array();
		$phones = array();
		
		$res = CIBlockElement::GetByID($ID);
		$arFields = $res->GetNext();
		
		//Email fields
		$arEventFields = Array(
			"ORDER_DATE"	=> date("d.m.Y"),
			"ORDER_TIME"	=> date("H:i:s"),
			"ID"			=> $arFields["ID"],
			"NAME"			=> $arFields["NAME"],
			"NOTICE_EMAIL"	=> '',
			);
		
		//message
		$message_arr = array('№'.$arFields['ID'], ''.$arFields['NAME']);

		//collecting properies
		$db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $ID, array("sort" => "asc"), Array());
		while($arProp = $db_props->Fetch()) 
		{
			$arEventFields[$arProp["CODE"]]=($arProp['PROPERTY_TYPE']=='L') ? $arProp["VALUE_ENUM"] : $arProp["VALUE"] ;	
			
			//properties for SMS
			if(in_array($arProp['ID'], $notice_iblock_property))
			{
				$mess_tmp = ($arProp['PROPERTY_TYPE']=='L') ? $arProp["VALUE_ENUM"] : $arProp["VALUE"] ;
				if(strlen($mess_tmp) > 0)
					$message_arr[] = $mess_tmp;
			}	
				
			//theme contacts
			if($arProp['ID'] == $PROPERTY_THEME)
			{
				$notice_email_theme = COption::GetOptionString(self::MODULE_ID, 'notice_email_'.$arProp['VALUE'], '', $site_id);
				$notice_phone_theme = COption::GetOptionString(self::MODULE_ID, 'notice_phone_'.$arProp['VALUE'], '', $site_id);
			}
		}

		//check theme email
		if(strlen(trim($notice_email_theme)) > 0)
			$emails[] = trim($notice_email_theme);

		//if send copy on main email always
		if(($use_main_email == 'Y' || strlen($notice_email_theme) == 0) && $notice_email_theme != $notice_email)
			$emails[] = $notice_email;


		//check theme phone
		if(strlen(trim($notice_phone_theme)) > 0)
			$phones[] = trim($notice_phone_theme);

		//if send copy on main phone always
		if(($use_main_phone == 'Y' || strlen(trim($notice_phone_theme)) == 0) && $notice_phone_theme != $notice_phone)
			$phones[] = $notice_phone;			
			
		foreach($emails as $email)
		{
			//send email
			$arEventFields['NOTICE_EMAIL'] = $email;
			CEvent::Send("WSM_CALLBACK_NOTICE", $site_id, $arEventFields);
		}

		//send sms
		$message = implode(', ', $message_arr);

		$arData = array(
			'PHONE' => $phones,
			'MESSAGE' => $message,
			);

		WSMCallbackSMS::SendSms($arData, $site_id);
	}
}
?>