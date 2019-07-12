<?$APPLICATION->IncludeComponent(
	"bitrix:main.register", 
	".default", 
	array(
		"SHOW_FIELDS" => array(
			0 => "EMAIL",
			1 => "NAME",
			2 => "LAST_NAME",
		),
		"REQUIRED_FIELDS" => array(
			0 => "EMAIL",
		),
		"AUTH" => "Y",
		"USE_BACKURL" => "Y",
		"SUCCESS_PAGE" => SITE_DIR,
		"SET_TITLE" => "Y",
		"PATH_TO_AUTH" => SITE_DIR."auth/",
		"USE_CUSTOM_ORDER" => "N",
		"USER_PROPERTY" => array(
		)
	),
	false
);?>