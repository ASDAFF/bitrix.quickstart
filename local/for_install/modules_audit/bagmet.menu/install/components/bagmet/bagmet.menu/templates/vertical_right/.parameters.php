<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************************************************************
				Input params
********************************************************************/
$arThemesMessages = array(
	"default" => GetMessage("F_THEME_DEFAULT"),
	"blue" => GetMessage("F_THEME_BLUE"),
	"orange" => GetMessage("F_THEME_ORANGE"),
);
$arThemes = array();
$dir = trim(preg_replace("'[\\\\/]+'", "/", dirname(__FILE__)."/themes/"));
if (is_dir($dir) && $directory = opendir($dir)):
	
	while (($file = readdir($directory)) !== false)
	{
		if ($file != "." && $file != ".." && is_dir($dir.$file))
			$arThemes[$file] = (!empty($arThemesMessages[$file]) ? $arThemesMessages[$file] : strtoupper(substr($file, 0, 1)).strtolower(substr($file, 1)));
	}
	closedir($directory);
endif;
$hidden = (!is_set($arCurrentValues, "USE_LIGHT_VIEW") || $arCurrentValues["USE_LIGHT_VIEW"] == "Y" ? "Y" : "N");
/********************************************************************
				/Input params
********************************************************************/

$arTemplateParameters = array(
	"THEME" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("F_THEMES"),
		"TYPE" => "LIST",
		"VALUES" => $arThemes,
		"MULTIPLE" => "N",
		"DEFAULT" => "default",
		"ADDITIONAL_VALUES" => "N"),
);
?>