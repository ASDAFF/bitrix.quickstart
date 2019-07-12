<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy bitrix files
			//"data.php", // install demo data
			"template.php", // Install template
			"theme.php", // Install theme
			"areas.php", // Install theme
			//"group.php", // Install group
			"settings.php",
		),
	),

	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK"),
		"STAGES" => Array(
			"types.php",
			"news.php"
		),
	),

	"webdoka.smartrealt" => Array(
		"NAME" => GetMessage("SERVICE_SMARTREALT"),
		"STAGES" => Array(
            "objects.php",
            "photo1.php",
            "photo2.php",
            "photo3.php",
            "photo4.php",
            "photo5.php",
            "photo6.php",
            "photo7.php",
            "photo8.php",
            "photo9.php",
			"photo10.php",
		),
	), 
);
?>