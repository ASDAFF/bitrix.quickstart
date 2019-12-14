<?$IS_AJAX = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_REQUEST['AJAX_CALL']) && 'Y' == $_REQUEST['AJAX_CALL'];
if ($IS_AJAX) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
} else {
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
	$APPLICATION->SetTitle("Обратная связь");
}
?>

<?$APPLICATION->IncludeComponent("bitrix:main.feedback", "gopro", array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Ваше сообщение успешно отправлено!",
		"EMAIL_TO" => "#SHOP_EMAIL#",
		"REQUIRED_FIELDS" => array(
		),
		"EVENT_MESSAGE_ID" => array(
		),
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
	),
	false
);
?>

<?if(!$IS_AJAX):?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
<?endif;?>