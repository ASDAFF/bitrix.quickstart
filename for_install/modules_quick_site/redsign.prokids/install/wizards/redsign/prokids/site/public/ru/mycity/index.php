<?$IS_AJAX = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_REQUEST['AJAX_CALL']) && 'Y' == $_REQUEST['AJAX_CALL'];
if ($IS_AJAX) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
} else {
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
	$APPLICATION->SetTitle("Мой город");
}
?>

<?$APPLICATION->IncludeComponent("redsign:autodetect.location", "gopro", array(
	"RSLOC_INCLUDE_JQUERY" => "N",
	"RSLOC_LOAD_LOCATIONS" => "Y",
	"RSLOC_LOAD_LOCATIONS_CNT" => "20"
	),
	false
);
?>

<?if(!$IS_AJAX):?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
<?endif;?>