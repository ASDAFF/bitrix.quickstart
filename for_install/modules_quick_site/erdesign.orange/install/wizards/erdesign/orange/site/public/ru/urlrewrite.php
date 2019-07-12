<?
$arUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^/gallery/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/gallery/index.php",
	),
	array(
		"CONDITION"	=>	"#^/events/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/events/index.php",
	),
	array(
		"CONDITION"	=>	"#^/news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/news/index.php",
	),
	array(
		"CONDITION"	=>	"#^/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/index.php",
	),
);

?>