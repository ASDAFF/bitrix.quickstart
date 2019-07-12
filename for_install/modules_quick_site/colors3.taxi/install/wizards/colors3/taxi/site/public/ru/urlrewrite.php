<?
$arUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^/services/([\\w\\d-]+)(\\\\?(.*))?#",
		"RULE"	=>	"code=$1",
		"ID"	=>	"",
		"PATH"	=>	"/services/detail.php",
	),
	array(
		"CONDITION"	=>	"#^/prices/([\\w\\d-]+)(\\\\?(.*))?#",
		"RULE"	=>	"code=$1",
		"ID"	=>	"",
		"PATH"	=>	"/prices/detail.php",
	),
	array(
		"CONDITION"	=>	"#^/news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/news/index.php",
	),
);

?>