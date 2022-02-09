<?
$arUrlRewrite = array(
    	array(
		"CONDITION"	=>	"#^/about/interesting/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	"/about/interesting/index.php",
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
		"ID"	=>	"devteam:catalog",
		"PATH"	=>	"/catalog/index.php",
	),
);
 