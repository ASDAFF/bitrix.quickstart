<?
$IS_AJAX = false;
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_REQUEST['AJAX_CALL']) && $_REQUEST['AJAX_CALL']=='Y') ) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$IS_AJAX = true;
} else {
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
	$APPLICATION->SetTitle("Purchase of goods");
}
?>

<?$APPLICATION->IncludeComponent(
	"rsmonopoly:forms", 
	"disabled_ext_fields", 
	array(
		"TITLE_FOR_WEBFORM" => "Ask a Question",
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
			1 => "RS_PHONE",
		),
		"ALFA_USE_CAPTCHA" => "N",
		"INPUT_NAME_RS_PERSONAL_SITE" => "Your website",
		"INPUT_NAME_RS_TEXTAREA" => "Comment",
		"ALFA_MESSAGE_AGREE" => "Thank you, your application is accepted!",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"AJAX_OPTION_ADDITIONAL" => "",
		"EVENT_TYPE" => "RS_MONOPOLY_BUY",
		"ALFA_EXT_FIELDS_COUNT" => "0",
		"FORM_TITLE" => "",
		"FORM_DESCRIPTION" => "",
		"EMAIL_TO" => "",
		"USE_CAPTCHA" => "N",
		"MESSAGE_AGREE" => "Thank you, your application is accepted!",
		"RS_MONOPOLY_EXT_FIELDS_COUNT" => "1",
		"RS_MONOPOLY_FIELD_0_NAME" => "Goods"
	),
	false
);?>

<?if(!$IS_AJAX):?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
<?endif;?>