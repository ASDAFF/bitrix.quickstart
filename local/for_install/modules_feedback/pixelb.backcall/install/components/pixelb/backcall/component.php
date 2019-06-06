<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}
/**  @var $USER CUser */
/**  @var $APPLICATION CMain */
/**  @var $arParams array */
/**  @var $arResult array */

if(!isset($arParams['FORM_ID']) || strlen($arParams['FORM_ID']) == 0){
	echo GetMessage("ERROR_SAVE_PARAMS");
	return false;
}

if(defined("BX_UTF") == false || (defined("BX_UTF") == true && BX_UTF !== true)) {
	$bx_utf = false;
} else {
	$bx_utf = true;
}

if(empty($arParams['TRIGGER_LABEL'])){
	$arParams['TRIGGER_LABEL'] = GetMessage('TRIGGER_LABEL');
}

if($arParams['EMAIL_TO_IBLOCK'] > 0 && $arParams['EMAIL_TO_IBLOCK_PROPERTY'] && CModule::IncludeModule("iblock")) {

	$cache = new CPHPCache();
	$cache_path = 'pb_form_backcall';
	$cache_id = $cache_path.$arParams['FORM_ID'].md5(implode('',$arParams));
	if ($arParams['EMAIL_TO_IBLOCK_CACHE_TIME'] > 0 && $cache->InitCache($arParams['EMAIL_TO_IBLOCK_CACHE_TIME'], $cache_id, $cache_path)){
		$cache_res = $cache->GetVars();
		if (is_array($cache_res["EMAIL_TO_IBLOCK"]) && (count($cache_res["EMAIL_TO_IBLOCK"]) > 0)){
			$arResult["EMAIL_TO_IBLOCK"] = $cache_res["EMAIL_TO_IBLOCK"];
		}
	}

	if (!is_array($arResult["EMAIL_TO_IBLOCK"])){
		$arSort = array(
			'SORT'=>'ASC'
		);

		$arSelect = array(
			'ID',
			'NAME',
			'PROPERTY_'.$arParams['EMAIL_TO_IBLOCK_PROPERTY']
		);

		if($arParams['EMAIL_TO_IBLOCK_ADD_PROPERTY']) {
			$arSelect[] = 'PROPERTY_'.$arParams['EMAIL_TO_IBLOCK_ADD_PROPERTY'];
		}

		if($arParams['EMAIL_TO_IBLOCK_ADD1_PROPERTY']) {
			$arSelect[] = 'PROPERTY_'.$arParams['EMAIL_TO_IBLOCK_ADD1_PROPERTY'];
		}

		$arFilter = array(
			'IBLOCK_ID' => $arParams['EMAIL_TO_IBLOCK'],
			'ACTIVE' => 'Y'
		);

		if($arParams['EMAIL_TO_IBLOCK_ADD_IGNORE_PROPERTY']) {
			$arFilter['!PROPERTY_'.$arParams['EMAIL_TO_IBLOCK_ADD_IGNORE_PROPERTY']] = false;
		}

		$rsElements = CIBlockElement::GetList($arSort,$arFilter,false,false,$arSelect);

		$arResult['EMAIL_TO_IBLOCK'] = array();

		while($arElement = $rsElements->Fetch()) {

			if($arParams['EMAIL_TO_IBLOCK_ADD_PROPERTY']) {
				$arElement['NAME'] = $arElement['PROPERTY_'.strtoupper($arParams['EMAIL_TO_IBLOCK_ADD_PROPERTY']).'_VALUE'];
			}

			if($arParams['EMAIL_TO_IBLOCK_ADD1_PROPERTY']) {
				$arElement['NAME'] .= ' '.$arElement['PROPERTY_'.strtoupper($arParams['EMAIL_TO_IBLOCK_ADD1_PROPERTY']).'_VALUE'];
			}


			$arResult['EMAIL_TO_IBLOCK'][$arElement['ID']] = $arElement;
		}

		if ($arParams['EMAIL_TO_IBLOCK_CACHE_TIME'] > 0){
			$cache->StartDataCache($arParams['EMAIL_TO_IBLOCK_CACHE_TIME'], $cache_id, $cache_path);
			$cache->EndDataCache(array("EMAIL_TO_IBLOCK"=>$arResult['EMAIL_TO_IBLOCK']));
		}
	}
}

if($_REQUEST['pb_send_mode'] == 'pb_ajax_backcall') {

	if($arParams['FORM_ID'] == $_REQUEST['pb_form_id']) {

		$arParams['MESSAGE_MAX_STRLEN'] = intval($arParams['MESSAGE_MAX_STRLEN']);
		if ($arParams['MESSAGE_MAX_STRLEN'] >= 0) {
			$arParams['MESSAGE_MAX_STRLEN'] = 300;
		}

		$arResult['EMAIL_TO'] = array();
		$arParams['EMAIL_TO'] = trim($arParams['EMAIL_TO']);
		if (strlen($arParams['EMAIL_TO']) == 0) {
			$arParams['EMAIL_TO'] = COption::GetOptionString("main", "email_from");
		}

		$arResult['EMAIL_TO'][] = $arParams['EMAIL_TO'];
		$arResult['RECIPIENT_NAME'] = '';

		if($_REQUEST['email_to_iblock'] > 0 && is_set($arResult['EMAIL_TO_IBLOCK'][$_REQUEST['email_to_iblock']]['PROPERTY_'.strtoupper($arParams['EMAIL_TO_IBLOCK_PROPERTY']).'_VALUE'])) {
			$arResult['EMAIL_TO'][] = $arResult['EMAIL_TO_IBLOCK'][$_REQUEST['email_to_iblock']]['PROPERTY_'.strtoupper($arParams['EMAIL_TO_IBLOCK_PROPERTY']).'_VALUE'];

			$arResult['RECIPIENT_NAME'] = $arResult['EMAIL_TO_IBLOCK'][$_REQUEST['email_to_iblock']]['NAME'];
		}

		$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");

		$arErrors = array();
		$arFieldsErrors = array();

		if($arParams['SHOW_FORM_RULES'] == 'Y' && $arParams['FORM_RULES_ADDRESS'] && empty($_REQUEST['pb_form_rules'])){
			$arErrors[] = GetMessage("ERROR_form_rules_REQUIRED");
			$arFieldsErrors[] = 'pb_form_rules';
		}

		if ($arParams["USE_CAPTCHA"] == "Y") {
			include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = $_REQUEST["captcha_sid"];
			$captcha_word = $_REQUEST["captcha_word"];
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0) {
				if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass)) {
					$arErrors[] = GetMessage("ERROR_CAPTCHA_WRONG");
					$arFieldsErrors[] = 'captcha_word';
				}
			} else {
				$arErrors[] = GetMessage("ERROR_CAPTCHA_WRONG");
				$arFieldsErrors[] = 'captcha_word';
			}
		}

		if (in_array('form_email', $arParams['REQUIRED_FIELDS']) && in_array('form_email', $arParams['ENABLED_FIELDS']) && !check_email(urldecode($_REQUEST["form_email"]))) {
			$arErrors[] = $arParams['ERROR_form_email_REQUIRED'] ? $arParams['ERROR_form_email_REQUIRED'] : GetMessage("ERROR_form_email_REQUIRED");
			$arFieldsErrors[] = 'form_email';
		}

		foreach ($arParams['REQUIRED_FIELDS'] as $field) {
			$trimCurrentValue = trim(urldecode($_REQUEST[$field]));
			if (empty($trimCurrentValue) && $field != 'form_email' && in_array($field, $arParams['ENABLED_FIELDS'])) {
				$arErrors[] = $arParams['ERROR_' . $field . '_REQUIRED'] ? $arParams['ERROR_' . $field . '_REQUIRED'] : GetMessage('ERROR_' . $field . '_REQUIRED');
				$arFieldsErrors[] = $field;
			}
		}

		if (check_bitrix_sessid() && count($arErrors) == 0) {

			$arParams["EMAIL_FROM"] = COption::GetOptionString("main", "email_from");

			$arFields = array();
			$arFields['EMAIL_FROM'] = $arParams["EMAIL_FROM"];
			$arFields['EMAIL_TO'] = implode(',',$arResult['EMAIL_TO']);

			$arFields['AUTHOR'] = substr(urldecode($_REQUEST["form_client_name"]), 0, $arParams['MESSAGE_MAX_STRLEN']);
			$arFields['PHONE'] = substr(urldecode($_REQUEST["form_client_phone"]), 0, $arParams['MESSAGE_MAX_STRLEN']);
			$arFields['AUTHOR_EMAIL'] = substr(urldecode($_REQUEST["form_email"]), 0, $arParams['MESSAGE_MAX_STRLEN']);
			$arFields['COMMENT'] = substr(urldecode($_REQUEST["form_comment"]), 0, $arParams['MESSAGE_MAX_STRLEN']);

			if(!$bx_utf){
				$arFields['AUTHOR'] = iconv('utf-8','windows-1251//TRANSLIT',$arFields['AUTHOR']);
				$arFields['PHONE'] = iconv('utf-8','windows-1251//TRANSLIT',$arFields['PHONE']);
				$arFields['COMMENT'] = iconv('utf-8','windows-1251//TRANSLIT',$arFields['COMMENT']);
			}

			$arFields['TEXT'] = '';

			$arFields['COMMENT'] = trim($arFields['COMMENT']);

			if($arResult['RECIPIENT_NAME']){
				$arFields['TEXT'] .= GetMessage("RECIPIENT_NAME_form_email_LABEL").$arResult['RECIPIENT_NAME']."\r\n";
			}

			if(in_array('form_client_name', $arParams['ENABLED_FIELDS']) && $arFields['AUTHOR']){
				$arFields['TEXT'] .= GetMessage("AUTHOR_form_email_LABEL").$arFields['AUTHOR']."\r\n";
			}

			if(in_array('form_email', $arParams['ENABLED_FIELDS']) && $arFields['AUTHOR_EMAIL']){
				$arFields['TEXT'] .= GetMessage("AUTHOR_EMAIL_form_email_LABEL").$arFields['AUTHOR_EMAIL']."\r\n";
			}

			if(in_array('form_client_phone', $arParams['ENABLED_FIELDS']) && $arFields['PHONE']){
				$arFields['TEXT'] .= GetMessage("PHONE_form_email_LABEL").$arFields['PHONE']."\r\n";
			}

			if($arFields['COMMENT']){
				$arFields['TEXT'] .= GetMessage("TEXT_form_email_LABEL").$arFields['COMMENT']."\r\n";
			}

			CEvent::SendImmediate($event = $arParams['EVENT_ET_MESSAGE_ID'], $sid = SITE_ID, $arFields, $duplicate = "N", $tid = $arParams['EVENT_MESSAGE_ID']);

			$arResult['status'] = 0;
			$arResult['msg'] = $arParams['OK_TEXT'];
		} else {

			$arResult['status'] = 1;
			if (count($arErrors) == 0) {
				$arResult['msg'] = $arParams['ERROR_TEXT'] . ' ' . implode(' ', $arErrors);
			} else {
				$arResult['msg'] = $arParams['ERROR_REQUIRED_TEXT'] . ' ' . implode(' ', $arErrors);
				$arResult['fields'] = $arFieldsErrors;
			}
		}

		if ($arParams["USE_CAPTCHA"] == "Y") {
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$cpt = new CCaptcha();
			$cpt->SetCode();
			$code=$cpt->GetSID();
			$arResult["CAPTCHA"] = htmlspecialcharsbx($cpt->GetSID());
		}

		if (count($arResult) > 0) {
			$APPLICATION->RestartBuffer();
			$arResult['bx_utf'] = $bx_utf;
			echo \Bitrix\Main\Web\Json::encode($arResult);
			die();
		} else {
			die();
		}
	}

} elseif($_REQUEST['pb_send_mode'] == 'ajax_sessid') {

	$APPLICATION->RestartBuffer();
	echo bitrix_sessid_post();
	die();

} elseif($_REQUEST['pb_send_mode'] == 'pb_ajax_get_form') {

	if($arParams['FORM_ID'] == $_REQUEST['pb_form_id']) {

		$APPLICATION->RestartBuffer();

		$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");

		$arErrors = array();

		if($arParams["USE_CAPTCHA"] == "Y"){
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$cpt = new CCaptcha();
			$cpt->SetCode();
			$code=$cpt->GetSID();
			$arResult["CAPTCHA"] = htmlspecialcharsbx($cpt->GetSID());
		}

		$this->IncludeComponentTemplate();
		die();
	}

} else {

	if ($arParams['USE_SYSTEM_JQUERY'] == 'Y') {
		CJSCore::Init(array("jquery"));
	}

	include_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/pixelb.backcall/include.php');

	pb_backcall_form_init($arParams);

	//echo bitrix_sessid_post('pb_bform_sessid');
	echo 'input type="hidden" name="pb_bform_sessid" id="pb_bform_sessid" value="'.bitrix_sessid().'" />';
	$this->IncludeComponentTemplate();

}
