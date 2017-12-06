<?php if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

if (!CModule::IncludeModule("lightweb.components")) return;
$site = CSite::GetByID(SITE_ID)->Fetch();
$site_name = $site['SITE_NAME'];
// Присваиваем значения по умолчанию
$arParams['USED_FIELDS'] = (empty($arParams['USED_FIELDS'])?array(0 => "NAME",1 => "PHONE"):$arParams['USED_FIELDS']);
$arParams['REQUIRED_FIELDS'] = (empty($arParams['REQUIRED_FIELDS'])?array(0 => "NAME",1 => "PHONE"):$arParams['REQUIRED_FIELDS']);

// Проверяем есть ли такое почтовое событие если нет создаем и сразу создаем к нему шаблон
// если почтовое событие есть, то проверяем есть ли шаблоны у него если нет то создаем шаблон

$event_name="LW_FEEDBACK_STATIC_FORM";
$site = CSite::GetByID(SITE_ID)->Fetch();
$site_name = $site['SITE_NAME'];
// Проверяем есть ли соответствующие компоненту почтовые события
$event_type = new CEventType;
$event_template = new CEventMessage;
// Пробуем получить необходимый тип почтового события
$current_event_type = $event_type->GetByID($event_name, LANGUAGE_ID)->Fetch();
if (empty($current_event_type)){
	// при отсутствии добавляем
	$new_event_type_id = $event_type->Add(array(
		"LID" => LANGUAGE_ID,
		"EVENT_NAME" => $event_name,
		"NAME" => GetMessage("MFP_EVENT_TYPE_NAME"),
		"DESCRIPTION" =>
			"#USER_NAME# - ".GetMessage("MFP_NAME")."\n".
			"#USER_PHONE# - ".GetMessage("MFP_PHONE")."\n".
			"#USER_EMAIL# - ".GetMessage("MFP_EMAIL")."\n".
			"#USER_MESSAGE# - ".GetMessage("MFP_MESSAGE")."\n".
			"#FORM# - ".GetMessage("MFP_FORM_NAME")."\n".
			"#FORM_ID# - ".GetMessage("MFP_FORM_ID")."\n".
			"#EMAIL_TO# - ".GetMessage("MFP_EMAIL_TO")."\n".
			"#SITE_NAME# - ".GetMessage("MFP_SITE_NAME")
	));
	if ((int)$new_event_type_id > 0) {
		// сразу добавляем и шаблон
		$event_template->Add(array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => $event_name,
			"LID" => SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL_TO#",
			"SUBJECT" => $site_name.": ".GetMessage("MFP_SUBJECT"),
			"BODY_TYPE" => "html",
			"MESSAGE" =>
				"<b>#FORM#</b> <br/><br/>".
				GetMessage("MFP_NAME").": #USER_NAME# <br/>".
				GetMessage("MFP_PHONE").": #USER_PHONE# <br/>".
				GetMessage("MFP_EMAIL").": #USER_EMAIL# <br/><br/>".
				GetMessage("MFP_MESSAGE").": #USER_MESSAGE#"
		));
	}
} else {
	$event = array();
	$filters = array("TYPE_ID" => $event_name, "ACTIVE" => "Y");
	$current_event_templates = CEventMessage::GetList($by="ID", $order="DESC", $filters);
	while($current_event_template = $current_event_templates->GetNext()){
		$event[$current_event_template["ID"]] = "[".$current_event_template["ID"]."] ".$current_event_template["SUBJECT"];
	}
	// Если по необходимому типу почтовых шаблонов нет. Добавляем.
	if (empty($event)){
		$event_template->Add(array(
			"ACTIVE" => "Y",
			"EVENT_NAME" => $event_name,
			"LID" => SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL_TO#",
			"SUBJECT" => $site_name.": ".GetMessage("MFP_SUBJECT"),
			"BODY_TYPE" => "html",
			"MESSAGE" =>
				"<b>#FORM#</b> <br/><br/>".
				GetMessage("MFP_NAME").": #USER_NAME# <br/>".
				GetMessage("MFP_PHONE").": #USER_PHONE# <br/>".
				GetMessage("MFP_EMAIL").": #USER_EMAIL# <br/><br/>".
				GetMessage("MFP_MESSAGE").": #USER_MESSAGE#"
		));
	}
}

// Подключаем JS кастомизации плагина под заданную форму обратной связи
$component_dir = substr(__DIR__, strpos(__DIR__, "/bitrix/"), strlen(__DIR__));
$APPLICATION->AddHeadScript($component_dir."/js/custom.js");
// Формируем массив для асинхронного запроса
$arParams['IN_BASE64'] = array();
$arParams['IN_BASE64']['SITE_NAME']=$site_name;
$arParams['IN_BASE64']['FORM_ID']=$arParams['FORM_ID'];
$arParams['IN_BASE64']['FORM_NAME']=$arParams['FORM_NAME'];
$arParams['IN_BASE64']['EVENT_NAME']=$event_name;
$arParams['IN_BASE64']['EVENT_MESSAGE_ID']=$arParams['EVENT_MESSAGE_ID'];
$arParams['IN_BASE64']['EMAIL_TO']=$arParams['EMAIL_TO'];
$arParams['IN_BASE64']['USED_FIELDS']=$arParams['USED_FIELDS'];
$arParams['IN_BASE64']['REQUIRED_FIELDS']=$arParams['REQUIRED_FIELDS'];
$arParams['IN_BASE64']['SMS_RU_STATE']=$arParams['SMS_RU_STATE'];
$arParams['IN_BASE64']['SMS_RU_LOGIN']=$arParams['SMS_RU_LOGIN'];
$arParams['IN_BASE64']['SMS_RU_PASSWORD']=$arParams['SMS_RU_PASSWORD'];
$arParams['IN_BASE64']['SMS_RU_API_KEY']=$arParams['SMS_RU_API_KEY'];
$arParams['IN_BASE64']['SMS_RU_FROM']=$arParams['SMS_RU_FROM'];
$arParams['IN_BASE64']['SMS_RU_ADMIN_NUMBER']=$arParams['SMS_RU_ADMIN_NUMBER'];
$arParams['IN_BASE64']['SMS_RU_TEMPLATE']=$arParams['SMS_RU_TEMPLATE'];
$arParams['IN_BASE64'] = base64_encode(serialize($arParams['IN_BASE64']));
$arParams['EXECUTE_URL'] = $component_dir.'/execute_send.php';
$this->IncludeComponentTemplate();
