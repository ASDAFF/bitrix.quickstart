<?
$arUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^/wishlist/([0-9a-zA-Z_-]+)/(.*)#",
		"RULE"	=>	"",
		"ID"	=>	"cm:wishlist.list",
		"PATH"	=>	"/wishlist/index.php",
	),
	array(
		"CONDITION"	=>	"#^/catalogue/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:catalog",
		"PATH"	=>	"/catalogue/index.php",
	),
);

?>