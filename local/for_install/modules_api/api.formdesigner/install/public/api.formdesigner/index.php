<?
use \Bitrix\Main\Config\Option;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$form_title       = Option::get('api.formdesigner', 'form_title');
$iblock_type      = Option::get('api.formdesigner', 'iblock_type');
$iblock_id        = Option::get('api.formdesigner', 'iblock_id');
$admin_message_id = Option::get('api.formdesigner', 'post_admin_message_id');
$user_message_id  = Option::get('api.formdesigner', 'post_user_message_id');
$email_from       = Option::get('main', 'email_from', 'info@' . $_SERVER['SERVER_NAME']);

$APPLICATION->SetTitle($form_title);
//$APPLICATION->AddChainItem($form_title);
?>
	<br>
<? $APPLICATION->IncludeComponent(
	"api:formdesigner",
	".default",
	array(
		"IBLOCK_TYPE"           => $iblock_type,
		"IBLOCK_ID"             => $iblock_id,
		"UNIQUE_FORM_ID"        => "form1",
		"POST_EMAIL_CODE"       => "EMAIL",
		"SHOW_ERRORS"           => array(
			0 => "IN_FIELD",
		),
		"FORM_WIDTH"            => "550px",
		"FORM_AUTOCOMPLETE"     => "Y",
		"FORM_TITLE"            => $form_title,
		"SUBMIT_BUTTON_CLASS"   => "afd-button",
		"TEMPLATE_THEME"        => "modern",
		"TEMPLATE_COLOR"        => "blue2",
		"CACHE_TYPE"            => "A",
		"CACHE_TIME"            => "86400",
		"IBLOCK_ON"             => "Y",
		"IBLOCK_TICKET_CODE"    => "TICKET_ID",
		"IBLOCK_ELEMENT_NAME"   => "Ticket##TICKET_ID#",
		"IBLOCK_ELEMENT_CODE"   => "Ticket##TICKET_ID#",
		"IBLOCK_ELEMENT_ACTIVE" => "N",
		"JQUERY_ON"             => "Y",
		"JQUERY_VERSION"        => "jquery",
		"POST_ON"               => "Y",
		"POST_EMAIL_FROM"       => $email_from,
		"POST_EMAIL_TO"         => $email_from,
		"POST_ADMIN_MESSAGE_ID" => array(
			0 => $admin_message_id,
		),
		"POST_USER_MESSAGE_ID"  => array(
			0 => $user_message_id,
		),
		"POST_MESS_STYLE_WRAP"  => "padding:10px;border-bottom:1px dashed #dadada;",
		"POST_MESS_STYLE_NAME"  => "font-weight:bold;",
		"POST_MESS_STYLE_VALUE" => "",
	),
	false
); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>