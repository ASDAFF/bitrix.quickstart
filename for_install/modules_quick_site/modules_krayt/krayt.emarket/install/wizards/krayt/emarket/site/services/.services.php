<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php",  
			"search.php", 
			"template.php",  
			"theme.php",  
			"menu.php",  
			"settings.php",
		),
	),
	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
            "types.php",        
			"news.php",
            "option.php",
            "slider.php",
            "catalog_property.php",
			"ref_my.php",
			"hl_data.php",
			"catalog_my.php",
			"catalog_sku.php",
			"catalog3.php",
		),
	),
    "sale" => Array(
        "NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
        "STAGES" => Array(
            'locations.php',"step1.php", "step2.php", "step3.php", "step4.php"
        ),
    ),
    "catalog" => Array(
        "NAME" => GetMessage("SERVICE_CATALOG_SETTINGS"),
        "STAGES" => Array(
            "index.php",            
        ),
    )   
);
?>