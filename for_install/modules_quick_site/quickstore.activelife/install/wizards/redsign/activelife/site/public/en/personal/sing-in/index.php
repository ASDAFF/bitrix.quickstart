<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Sing in");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:system.auth.form", 
	"fancybox", 
	array(
		"COMPONENT_TEMPLATE" => "fancybox",
		"REGISTER_URL" => "#SITE_DIR#auth/",
		"FORGOT_PASSWORD_URL" => "#SITE_DIR#auth/",
		"PROFILE_URL" => "#SITE_DIR#personal/profile/",
		"SHOW_ERRORS" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>