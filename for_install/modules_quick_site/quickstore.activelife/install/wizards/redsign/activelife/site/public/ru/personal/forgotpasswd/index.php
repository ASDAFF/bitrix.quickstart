<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Восстановление пароля");
$arAuthResult = $APPLICATION->arAuthResult;
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:system.auth.forgotpasswd", 
	".default", 
	array(
        "COMPONENT_TEMPLATE" => ".default",
		"AUTH_RESULT" => $arAuthResult,
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>