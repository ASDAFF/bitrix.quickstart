<?
IncludeModuleLangFile(__FILE__);
$aMenu = array(
	"parent_menu" => "global_menu_services",
	"section" => "indi",
	"text" => "Панель управления выгрузкой",
	"icon" => "eq_menu_icon",
	"title" => "Панель управления выгрузкой",
	"sort" => 9900,
	"url" => 'indi_data_import.php?lang=' . LANGUAGE_ID,
	"more_url" => array(
		'indi_data_import.php'
	),
	"items" => array()
);
return $aMenu;
?>