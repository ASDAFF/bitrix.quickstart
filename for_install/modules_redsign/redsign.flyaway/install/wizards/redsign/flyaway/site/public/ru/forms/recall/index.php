<?
$IS_AJAX = false;
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_REQUEST['AJAX_CALL']) && $_REQUEST['AJAX_CALL']=='Y') ) {
  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
  $IS_AJAX = true;
} else {
  require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
  $APPLICATION->SetTitle("Свяжитесь с нами");
}
?>

<?$APPLICATION->IncludeComponent(
	"rsflyaway:forms", 
	"form", 
	array(
		"TITLE_FOR_WEBFORM" => "",
		"DESCRIPTION_FOR_WEBFORM" => "",
		"ALFA_EMAIL_TO" => "",
		"SHOW_FIELDS" => array(
			0 => "RS_NAME",
			1 => "RS_PHONE",
			2 => "RS_EMAIL",
			3 => "RS_TEXTAREA",
		),
		"REQUIRED_FIELDS" => array(
			0 => "RS_NAME",
			1 => "RS_EMAIL",
		),
		"ALFA_USE_CAPTCHA" => "N",
		"INPUT_NAME_RS_PERSONAL_SITE" => "Ваш сайт",
		"INPUT_NAME_RS_TEXTAREA" => "Комментарий автора",
		"ALFA_MESSAGE_AGREE" => "Спасибо, ваша заявка принята!",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"AJAX_OPTION_ADDITIONAL" => "",
		"EVENT_TYPE" => "RS_FLYAWAY_RECALL",
		"FORM_TITLE" => "Свяжитесь с нами",
		"FORM_DESCRIPTION" => "",
		"EMAIL_TO" => "shtonda1994@mail.ru",
		"USE_CAPTCHA" => "Y",
		"MESSAGE_AGREE" => "Спасибо, ваша заявка принята!",
		"RS_MONOPOLY_EXT_FIELDS_COUNT" => "0",
		"COMPONENT_TEMPLATE" => "form",
		"RS_FLYAWAY_EXT_FIELDS_COUNT" => "0"
	),
	false
);?>

<?if(!$IS_AJAX):?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
<?endif;?>