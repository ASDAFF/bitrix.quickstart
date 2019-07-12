<?$APPLICATION->IncludeComponent(
	"bitrix:system.auth.form", 
	".default", 
	array(
		"PRMEDIA_AUTH_FORM_REDIRECT_URL" => "",
		"REGISTER_URL" => SITE_DIR . "registration/",
		"FORGOT_PASSWORD_URL" => SITE_DIR . "auth/forgot_password.php",
		"PROFILE_URL" => SITE_DIR . "profile/",
		"SHOW_ERRORS" => "Y",
		"PRMEDIA_AUTH_FORM_SHOW_TITLE" => "N"
	),
	false
);?> 