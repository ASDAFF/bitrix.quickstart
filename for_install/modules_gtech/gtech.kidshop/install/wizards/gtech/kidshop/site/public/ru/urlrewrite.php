<?
$arUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^/personal/order/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:sale.personal.order",
		"PATH"	=>	"/personal/order/index.php",
	),
	array(
		"CONDITION"	=>	"#^/about/news/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/about/news/index.php",
	),
	array(
		"CONDITION"	=>	"#^/catalog/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:catalog",
		"PATH"	=>	"/catalog/index.php",
	),
	array(
		"CONDITION"	=>	"#^/forum/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:forum",
		"PATH"	=>	"/forum/index.php",
	),
);

?>