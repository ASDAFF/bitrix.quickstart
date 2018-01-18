<?

$grain_customsettings_default_option = array();

$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/default_option.php", "r");
$settings_data=fread($handle, filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/default_option.php"));
fclose($handle);

ob_start();
$settings_data_error = eval("?>".$settings_data."<?")===false;
$err = ob_get_contents();
ob_end_clean();

if($settings_data_error) $grain_customsettings_default_option=Array();


?>