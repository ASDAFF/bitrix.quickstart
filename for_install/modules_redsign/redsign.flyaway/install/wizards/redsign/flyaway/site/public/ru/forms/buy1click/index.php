<?
$IS_AJAX = false;
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_REQUEST['AJAX_CALL']) && $_REQUEST['AJAX_CALL']=='Y') ) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$IS_AJAX = true;
} else {
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
	$APPLICATION->SetTitle("Купить в 1 клик");
}
?><?$APPLICATION->IncludeComponent(
	"rsflyaway:forms", 
	"form", 
	array(
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"ALFA_EMAIL_TO" => "",
		"ALFA_EXT_FIELDS_COUNT" => "0",
		"ALFA_MESSAGE_AGREE" => "Спасибо, ваша заявка принята!",
		"ALFA_USE_CAPTCHA" => "N",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"DESCRIPTION_FOR_WEBFORM" => "",
		"EMAIL_TO" => "",
		"EVENT_TYPE" => "RS_FLYAWAY_BUY_1_CLICK",
		"FORM_DESCRIPTION" => "",
		"FORM_TITLE" => "Купить в 1 клик",
		"INPUT_NAME_RS_PERSONAL_SITE" => "Ваш сайт",
		"INPUT_NAME_RS_TEXTAREA" => "Комментарий",
		"MESSAGE_AGREE" => "Спасибо, ваша заявка принята!",
		"REQUIRED_FIELDS" => array(
			0 => "RS_NAME",
			1 => "RS_PHONE",
		),
		"RS_FLYAWAY_EXT_FIELDS_COUNT" => "1",
		"RS_FLYAWAY_FIELD_0_NAME" => "Товар",
		"SHOW_FIELDS" => array(
			0 => "RS_NAME",
			1 => "RS_PHONE",
			2 => "RS_TEXTAREA",
		),
		"DISABLED_FIELDS" => array(
			   0 => "RS_EXT_FIELD_0"
		 ),
		"TITLE_FOR_WEBFORM" => "Купить в 1 клик",
		"USE_CAPTCHA" => "Y",
		"COMPONENT_TEMPLATE" => "form"
	),
	false
);?>
<?if(!$IS_AJAX):?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>



<?endif;?>