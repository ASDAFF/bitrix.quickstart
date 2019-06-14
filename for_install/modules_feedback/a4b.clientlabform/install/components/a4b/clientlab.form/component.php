<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Config\Option;

$arResult['FORM'] = json_decode((preg_replace('/&quot;/', '"', $arParams['OPTIONS'])), true);

$arParams['RE_SITE_KEY'] = COption::GetOptionString("a4b.clientlabform", "RE_SITE_KEY");


if ($_REQUEST['AJAX'] !== "Y"){
	$this->IncludeComponentTemplate();
}else{
	$APPLICATION->RestartBuffer();
	CModule::IncludeModule('a4b.clientlabform');

	$form = new ClientlabForm($_REQUEST);

	$result = array();

	if ($arParams['USE_RECAPTCHA']==="Y") {
		$result['recaptcha'] = $form->checkRecaptchaResponce($_REQUEST['g-recaptcha-response']);
	}else{
		$result['recaptcha'] = "true";
	}


	if ($result['recaptcha'] === "true") {
		$result['email_sent'] = $form->sendMail($_REQUEST['mail_template']);
		$result['iblock_save'] = $form->saveToIBlock();
	}

	echo json_encode($result);

	die();
}