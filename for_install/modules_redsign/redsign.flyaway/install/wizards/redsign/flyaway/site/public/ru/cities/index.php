<?
$IS_AJAX = false;
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_REQUEST['AJAX_CALL']) && $_REQUEST['AJAX_CALL']=='Y') ) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$IS_AJAX = true;
} else {
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
	$APPLICATION->SetTitle("Свяжитесь с нами");
}
?><?$APPLICATION->IncludeComponent(
	"redsign:autodetect.location",
	"flyaway",
	Array(
		"COMPONENT_TEMPLATE" => "flyaway",
		"RSLOC_INCLUDE_JQUERY" => "N",
		"RSLOC_LOAD_LOCATIONS" => "Y",
		"RSLOC_LOAD_LOCATIONS_CNT" => "20"
	)
);?>
<?if(!$IS_AJAX):?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
<?endif;?>