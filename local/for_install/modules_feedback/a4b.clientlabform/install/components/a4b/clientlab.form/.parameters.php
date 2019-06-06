<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::includeModule("a4b.clientlabform");

//echo ClientlabForm::MODULE_PATH;

?>

<!-- <link rel="stylesheet" href="/bitrix/components/a4b/clientlab.form/parameters.css" />
<link rel="stylesheet" href="/bitrix/components/a4b/clientlab.form/templates/.default/lib/bootstrap/bootstrap.css">

<script defer src="/bitrix/components/a4b/clientlab.form/templates/.default/lib/jquery/jquery.js"></script>
<script defer src="/bitrix/components/a4b/clientlab.form/templates/.default/lib/jquery/jquery-ui.js"></script>
<script defer src="/bitrix/components/a4b/clientlab.form/templates/.default/lib/form-builder/form-builder.js"></script>
<script defer src="/bitrix/components/a4b/clientlab.form/parameters.js"></script>
 -->

<?php 

$rsTemplateList = CEventMessage::GetList(
	$by="site_id",
	$order="desc",
	array("TYPE" => "CLIENTLAB_FORM_ADD")
);

while($arTempl = $rsTemplateList->GetNext())
{
	$arTemplates[$arTempl['ID']] = $arTempl['SUBJECT'];
}

if (count($arTemplates)<1) {
	echo GetMessage("CLIENTLAB_NO_MAIL_TEMPLATE_MSG");
	$arTemplates[""] = GetMessage("CLIENTLAB_NO_MAIL_TEMPLATE_MSG");
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => Array(
		"FORM_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_FORM_NAME"),
			"TYPE" => "TEXT",
			"DEFAULT" => "form1"
		),
		"MAIL_TEMPLATE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_MAIL_TEMPLATE"),
			"TYPE" => "LIST",
			"VALUES" => $arTemplates
		),
		"OPTIONS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_OPTIONS"),
			"TYPE" => "TEXTAREA"
		),

		"AGREEMENT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_AGREEMENT"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("CLIENTLAB_PARAM_AGREEMENT_DEFAULT")
		),
		"AGREEMENT_ERR_MSG" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_AGREEMENT_ERR_MSG"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("CLIENTLAB_PARAM_AGREEMENT_ERR_MSG_DEFAULT")
		),
		"SUBMIT_TEXT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_SUBMIT_TEXT_PROCESS"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("CLIENTLAB_PARAM_SUBMIT_TEXT_DEFAULT")
		),
		"SUBMIT_TEXT_PROCESS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_SUBMIT_TEXT_PROCESS"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("CLIENTLAB_PARAM_SUBMIT_TEXT_PROCESS_DEFAULT")
		),
		"IS_MODAL_FORM" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_IS_MODAL_FORM"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"MODAL_BTN_TEXT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_MODAL_BTN_TEXT"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("CLIENTLAB_PARAM_MODAL_BTN_TEXT_DEFAULT")
		),
		"IS_THANKS_MODAL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_IS_THANKS_MODAL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		"THANKS_MESSAGE_TITLE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_THANKS_MESSAGE_TITLE"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("CLIENTLAB_PARAM_THANKS_MESSAGE_TITLE_DEFAULT")
		),
		"THANKS_MESSAGE_TEXT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_THANKS_MESSAGE_TEXT"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("CLIENTLAB_PARAM_THANKS_MESSAGE_TEXT_DEFAULT")
		),
		"USE_RECAPTCHA" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_USE_RECAPTCHA"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"RECAPTCHA_ERR_MSG" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLIENTLAB_PARAM_RECAPTCHA_ERR_MSG"),
			"TYPE" => "TEXT",
			"DEFAULT" => GetMessage("CLIENTLAB_PARAM_RECAPTCHA_ERR_MSG_DEFAULT")
		)

	), 
);