<?
IncludeModuleLangFile(__FILE__);

$arCustomPage = Array();

$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php", "r");
$settings_data=fread($handle, filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php"));
fclose($handle);

ob_start();
eval("?>".$settings_data."<?");
$err = ob_get_contents();
ob_end_clean();


//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php");

$aMenu = Array();
if ($APPLICATION->GetGroupRight("grain.customsettings")!="D")
{
	$aMenu = array(

		array(
			"parent_menu" => $arCustomPage["PARENT_MENU"]?$arCustomPage["PARENT_MENU"]:"global_menu_settings",
			"sort" => $arCustomPage["SORT"]?$arCustomPage["SORT"]:1780,
			"text" => $arCustomPage["LANG"][LANGUAGE_ID]["MENU_TEXT"]?$arCustomPage["LANG"][LANGUAGE_ID]["MENU_TEXT"]:GetMessage("GRAIN_CUSTOMSETTINGS_MENU_TEXT"),
			"url"  => "gcustomsettings.php?lang=".LANGUAGE_ID,
			"title"=> $arCustomPage["LANG"][LANGUAGE_ID]["MENU_TITLE"]?$arCustomPage["LANG"][LANGUAGE_ID]["MENU_TITLE"]:GetMessage("GRAIN_CUSTOMSETTINGS_MENU_TITLE"),
			"icon" => "grain_customsettings_menu_icon",
		),
	);
	return $aMenu;
}
return $false;
?>
