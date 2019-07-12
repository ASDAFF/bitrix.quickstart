<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.minilanding")) return;

$arParams['FORMID'] = (intval($arParams['FORMID'])>0) ? $arParams['FORMID'] : 1;
if(isset($_REQUEST["mlife_formid"]) && $_REQUEST["mlife_formid"]>0 && $_REQUEST["mlife_formid"]!=$arParams['FORMID']) return;

global $USER;
global $APPLICATION;

$error = array();
$send = array('phone'=>'','email'=>'','name'=>'','mess'=>'');
$send_req = array('phone'=>0,'email'=>0,'name'=>0,'mess'=>0);

if(is_array($arParams['FIELD_REQ']) && count($arParams['FIELD_REQ'])>0) {
	foreach($arParams['FIELD_REQ'] as $value) {
		$send_req[$value] = 1;
	}
}else{
	$send_req = array('phone'=>1,'email'=>1,'name'=>1,'mess'=>1);
}
$arResult['SEND_REQ'] = $send_req;

$check_bitrix_s = true;

//валидация данных
if(intval($_REQUEST["name_bk"])==1 && $arParams['FORMID']==intval($_REQUEST["mlife_formid"])){
	$check_bitrix_s = check_bitrix_sessid('bistrclick_sessid');
	if(!$check_bitrix_s) {
		$error['bistrclick_sessid'] = GetMessage('MLIFE_CAT_BK_FIELD_ERROR_SESS');
	}
	
	foreach($arResult['SEND_REQ'] as $key=>$value) {
		if($value==1 && !trim($_REQUEST[$key])) {
			$error[$key] = GetMessage("MLIFE_CAT_BK_FIELD_ERROR_".strtoupper($key));
		}
	}
	
	if(CMlifeMinilanding::getname($_REQUEST['phone'])) {
		$send['phone'] = CMlifeMinilanding::getname($_REQUEST['phone']);
		unset($error['phone']);
	}
	if(CMlifeMinilanding::getname($_REQUEST['name'])) {
		$send['name'] = CMlifeMinilanding::getname($_REQUEST['name']);
		unset($error['name']);
	}
	if(CMlifeMinilanding::getname($_REQUEST['mess'])) {
		$send['mess'] = CMlifeMinilanding::getname($_REQUEST['mess']);
		unset($error['mess']);
	}
	$requlEmail = "/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD";
	if(CMlifeMinilanding::getname($_REQUEST['email']) && !preg_match($requlEmail,CMlifeMinilanding::getname($_REQUEST['email']))) {
		$error['email'] = GetMessage("MLIFE_CAT_BK_FIELD_ERROR_EMAIL2");
		$send['email'] = CMlifeMinilanding::getname($_REQUEST['email']);
	}elseif(CMlifeMinilanding::getname($_REQUEST['email'])) {
		$send['email'] = CMlifeMinilanding::getname($_REQUEST['email']);
		unset($error['email']);
	}
	$send['addfield1'] = CMlifeMinilanding::getname($_REQUEST['addfield1']);
	$send['addfield2'] = CMlifeMinilanding::getname($_REQUEST['addfield2']);
	$send['addfield3'] = CMlifeMinilanding::getname($_REQUEST['addfield3']);
	$send['addfield4'] = CMlifeMinilanding::getname($_REQUEST['addfield4']);
	
	$arResult['SEND'] = $send;
	$arResult['ERROR'] = $error;
}

//отображение формы
$showform = 0;
if(isset($_REQUEST["formclick"]) && $arParams['FORMID']==intval($_REQUEST["mlife_formid"])) $showform = 1;
$arResult['SHOW_FORM'] = $showform;
$arResult['REF_START'] = $APPLICATION->GetCurPage();

if($showform) {

	$key=trim($arParams['KEY']);//ключ для доступа к форме
	$referer_start = CMlifeMinilanding::getname($_REQUEST['referer']); //хеш адреса страницы с которого запущена форма
	$date_ref=strtotime(date("M-d-Y H:00:00"));
	$ref1 = CMlifeMinilanding::getref($date_ref,$key,$arResult['REF_START'],1);
	$ref2 = CMlifeMinilanding::getref($date_ref,$key,$arResult['REF_START'],2);

	$arResult['CHECK_SPAM'] = CMlifeMinilanding::checkspam($referer_start, $ref1, $ref2);
	
	if(!$check_bitrix_s) $arResult['CHECK_SPAM'] = 1;
	
	if($arParams['CHECK_HASH']=="Y") $arResult['CHECK_SPAM'] = 0;

	if($arResult['SHOW_FORM']==1 && $arResult['CHECK_SPAM']!=1) {
		//TODO тут можно получать всякие штуки и закинуть в шаблон (при наличии ид товара, например, вывести его описание и т.п.)
	}
	
}

//если данные валидны
if(count($arResult['ERROR'])==0 && intval($_REQUEST["name_bk"])==1 && $arParams['FORMID']==intval($_REQUEST["mlife_formid"])) {
	
	//подготовка массива макросов, для подстановки в шаблоны писем и смс
	$arEventFields = array();
	if(isset($arResult['SEND']['email']) && $arResult['SEND']['email']) $arEventFields['FORM_EMAIL'] = $arResult['SEND']['email'];
	if(isset($arResult['SEND']['name']) && $arResult['SEND']['name']) $arEventFields['FORM_NAME'] = $arResult['SEND']['name'];
	if(isset($arResult['SEND']['mess']) && $arResult['SEND']['mess']) $arEventFields['FORM_MESS'] = $arResult['SEND']['mess'];
	if(isset($arResult['SEND']['phone']) && $arResult['SEND']['phone']) $arEventFields['FORM_PHONE'] = $arResult['SEND']['phone'];
	if(isset($arResult['SEND']['addfield1']) && $arResult['SEND']['addfield1']) $arEventFields['FORM_ADDFIELD1'] = $arResult['SEND']['addfield1'];
	if(isset($arResult['SEND']['addfield2']) && $arResult['SEND']['addfield2']) $arEventFields['FORM_ADDFIELD2'] = $arResult['SEND']['addfield2'];
	if(isset($arResult['SEND']['addfield3']) && $arResult['SEND']['addfield3']) $arEventFields['FORM_ADDFIELD3'] = $arResult['SEND']['addfield3'];
	if(isset($arResult['SEND']['addfield4']) && $arResult['SEND']['addfield4']) $arEventFields['FORM_ADDFIELD4'] = $arResult['SEND']['addfield4'];
	
		//создание лида в bx24
	$errlid = array();
	$postLid = array();
	if($arParams['IBL_BX24']=='Y' && $arParams["FIELDBX10_CODE"] && $arParams["FIELDBX11_CODE"] && $arParams["FIELDBX12_CODE"] && $arParams["FIELDBX14_CODE"]) {
		if($arParams["FIELDBX1_CODE"]!="-" && isset($arEventFields['FORM_NAME'])){
			$postLid[$arParams["FIELDBX1_CODE"]] = $arEventFields['FORM_NAME'];
		}
		if($arParams["FIELDBX2_CODE"]!="-" && isset($arEventFields['FORM_PHONE'])){
			$postLid[$arParams["FIELDBX2_CODE"]] = $arEventFields['FORM_PHONE'];
		}
		if($arParams["FIELDBX3_CODE"]!="-" && isset($arEventFields['FORM_EMAIL'])){
			$postLid[$arParams["FIELDBX3_CODE"]] = $arEventFields['FORM_EMAIL'];
		}
		if($arParams["FIELDBX4_CODE"]!="-" && isset($arEventFields['FORM_MESS'])){
			$postLid[$arParams["FIELDBX4_CODE"]] = $arEventFields['FORM_MESS'];
		}
		if($arParams["FIELDBX5_CODE"]!="-" && isset($arEventFields['FORM_ADDFIELD1'])){
			$postLid[$arParams["FIELDBX5_CODE"]] = $arEventFields['FORM_ADDFIELD1'];
		}
		if($arParams["FIELDBX6_CODE"]!="-" && isset($arEventFields['FORM_ADDFIELD2'])){
			$postLid[$arParams["FIELDBX6_CODE"]] = $arEventFields['FORM_ADDFIELD2'];
		}
		if($arParams["FIELDBX7_CODE"]!="-" && isset($arEventFields['FORM_ADDFIELD3'])){
			$postLid[$arParams["FIELDBX7_CODE"]] = $arEventFields['FORM_ADDFIELD3'];
		}
		if($arParams["FIELDBX8_CODE"]!="-" && isset($arEventFields['FORM_ADDFIELD4'])){
			$postLid[$arParams["FIELDBX8_CODE"]] = $arEventFields['FORM_ADDFIELD4'];
		}
		//название
		$postLid["TITLE"] = str_replace("#NAME#",$arEventFields['FORM_NAME'],$arParams["FIELDBX10_CODE"]);
		//логин и пароль
		$postLid["LOGIN"] = $arParams["FIELDBX11_CODE"];
		$postLid["PASSWORD"] = $arParams["FIELDBX12_CODE"];
		
		//ответственный
		$postLid["ASSIGNED_BY_ID"] = $arParams["FIELDBX13_CODE"];
		
		$arParams["FIELDBX15_CODE"] = str_replace("n","",$arParams["FIELDBX15_CODE"]);
		
		if(intval($arParams["FIELDBX15_CODE"])>0) {
			$rsEM = CEventMessage::GetByID(intval($arParams["FIELDBX15_CODE"]));
				$arEM = $rsEM->Fetch();
				if($arEM["MESSAGE"]) {
					$arEventFieldsNew = array();
					foreach($arEventFields as $key=>$val){
						$arEventFieldsNew["#".$key."#"] = $val;
					}
					$postLid["COMMENTS"] = str_replace(array_keys($arEventFieldsNew), $arEventFieldsNew, $arEM["MESSAGE"]);
				}
		}
		
		
		
		//источник
		$postLid["SOURCE_ID"] = "WEB";
		
		if(ToLower(SITE_CHARSET) == "windows-1251") {
			foreach($postLid as $key => &$item){
				if($key!="LOGIN" && $key!="PASSWORD" && $key!="ASSIGNED_BY_ID" && $key!="SOURCE_ID")
					$item = $GLOBALS["APPLICATION"]->ConvertCharset($item, SITE_CHARSET, 'UTF-8');
			}
		}
		
		$paramspost = array();
		foreach($postLid as $key => $item){
			$paramspost[] = $key.'='.$item;
		}
		$paramspost = implode('&', $paramspost);
		
		if (!function_exists('curl_init')) {
		   $errlid[] =  GetMessage("MLIFE_CAT_BK_FIELD_ERROR_BX24_2");
		   $errlid[] =  "PHP ERROR: CURL library not found!";
		}else{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://'.trim($arParams["FIELDBX14_CODE"]).'/crm/configs/import/lead.php');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramspost);
			curl_setopt($ch,  CURLOPT_HTTPHEADER, array(
				'Content-Length: '.strlen($paramspost),
				'Cache-Control: no-store, no-cache, must-revalidate',
				"Expires: " . date("r")
			));
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$bx24result = curl_exec($ch);
			curl_close($ch);
			
			$error = json_decode($bx24result);
			if($error->error!='201') {
				$errlid[] =  GetMessage("MLIFE_CAT_BK_FIELD_ERROR_BX24_2");
				$errlid[] =  $error->error_message;
			}
		}
		
	}elseif($arParams['IBL_BX24']=='Y') {
		$errlid[] =  GetMessage("MLIFE_CAT_BK_FIELD_ERROR_BX24_2");
		$errlid[] =  GetMessage("MLIFE_CAT_BK_FIELD_ERROR_BX24_1");
	}
	
	//если есть ошибки добавляем макрос ERRORLID
	if(count($errlid)>0) $arEventFields["ERRORLID"] = implode(", ", $errlid);
	
	
	//отправка уведомления на email админу
	if($arParams['NOTICE_ADMIN']=='Y' && $arParams['NOTICE_EMAIL']) {
		$arEventFields['SEND_EMAIL'] = $arParams['NOTICE_EMAIL_EMAIL'];
		if($arParams["EVENTPOST2"]!="Y"){
			CEvent::Send("MLIFE_MINILANDING", SITE_ID, $arEventFields, 'Y', $arParams['NOTICE_EMAIL']);
		}else{
			CEvent::SendImmediate("MLIFE_MINILANDING", SITE_ID, $arEventFields, 'Y', $arParams['NOTICE_EMAIL']);
		}
	}
	
	if($arParams['NOTICE_ADMIN_SMS']=='Y' && $arParams['NOTICE_SMS_PHONE']) {
		if((CModule::IncludeModule("mlife.smsservices") &&  $arParams['NOTICE_SMS_MODULE']=='smsservices') 
		|| (CModule::IncludeModule("asd.smsswitcher") && $arParams['NOTICE_SMS_MODULE']=='smsswitcher')) {
			
			$phones = $arParams['NOTICE_SMS_PHONE'];
			
				$rsEM = CEventMessage::GetByID($arParams['NOTICE_SMS']);
				$arEM = $rsEM->Fetch();
				$shab = $arEM['MESSAGE'];
				$mess = CMlifeMinilanding::mlife_macros_replace($arEventFields,$shab);
				
				if(CModule::IncludeModule("mlife.smsservices") && $arParams['NOTICE_SMS_MODULE']=='smsservices') {
					$obSmsServ = new CMlifeSmsServices();
					$arSend = $obSmsServ->sendSms($phones,$mess,0);
				}
				else {
					CSMSS::Send($phones, $mess);
				}
		}
	}
	
	//TODO подумать исключение при неправильных параметрах или ошибках смс модулей
	
	//запись данных в инфоблок
	if($arParams['IBL_ADMIN']=='Y') {
		
		$arConfigField = array(
			'1' => $arResult['SEND']['name'],
			'2' => $arResult['SEND']['phone'],
			'3' => $arResult['SEND']['email'],
			'4' => $arResult['SEND']['mess'],
			'5' => $arResult['SEND']['addfield1'],
			'6' => $arResult['SEND']['addfield2'],
			'7' => $arResult['SEND']['addfield3'],
			'8' => $arResult['SEND']['addfield4'],
		);
		
		$el = new CIBlockElement;
		$PROP = array();
		$arLoadProductArray = array();
		
		for($i=1,$fieldcode="";$i<9;$i++){
			$fieldcode = str_replace("---","",$arParams["FIELD".$i."_CODE"]);
			if($fieldcode!="-") {
				if(is_numeric($fieldcode)) {
					$PROP[$fieldcode] = $arConfigField[$i];
				}else{
					$arLoadProductArray[$fieldcode] = $arConfigField[$i];
				}
			}
		}
		
		$arLoadProductArray["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
		$arLoadProductArray["PROPERTY_VALUES"] = $PROP;
		$arLoadProductArray["ACTIVE"] = ($arParams["IBL_ACTIVE"]=="Y")? "N" : "Y";
		
		if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
			//успех
		}else{
			//ошибка
			//file_put_contents('file.txt',print_r(array($arLoadProductArray,$arParams,$arConfigField),true));
		}
		
	}
	
	//форма отправлена показываем сообщение
	$arResult['SENDFORM'] = true;
}else{
	$arResult['SENDFORM'] = false;
}

$this->IncludeComponentTemplate();
	


?>