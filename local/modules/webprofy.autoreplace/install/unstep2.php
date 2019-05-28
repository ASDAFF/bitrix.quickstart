<? if (!check_bitrix_sessid())
	return; ?>
<?

$MODULE_BPATH = "/local/modules/";
$MODULE_ID = "webprofy.autoreplace";
DeleteDirFiles($DOCUMENT_ROOT.$MODULE_BPATH.$MODULE_ID."/install/admin", $DOCUMENT_ROOT."/bitrix/admin");

$errors = false;

if (!array_key_exists("savedata", $_REQUEST) || $_REQUEST["savedata"] != "Y"){

	$errors = $DB->RunSQLBatch($DOCUMENT_ROOT.$MODULE_BPATH.$MODULE_ID."/install/mysql/uninstall.sql");
	//DeleteDirFilesEx("/upload/qfiles");
}

if ($errors === false)
{
	echo CAdminMessage::ShowNote(GetMessage("WEBPROFY_AUTOREPLACE_UNINSTALL_COMPLETE"));
	COption::RemoveOption($MODULE_ID);
	CAgent::RemoveModuleAgents($MODULE_ID);
	UnRegisterModule($MODULE_ID);

	//UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $MODULE_ID, "CuIBlockPropertyFiles", "GetUserTypeDescription");
	//UnRegisterModuleDependences("main", "OnUserTypeBuildList", $MODULE_ID, "CuUserPropertyFiles", "GetUserTypeDescription");
	//UnRegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", $MODULE_ID, "CuIBlockPropertyFiles", "OnAfterIBlockElementDeleteHandler");
	//UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $MODULE_ID, "CuIBlockPropertyFiles", "OnAfterIBlockElementAddHandler");
	UnRegisterModuleDependences("main", "OnBuildGlobalMenu", $MODULE_ID, "WebprofyAutoreplace", "OnBuildGlobalMenuHandler");
}else{
	for ($i = 0; $i < count($errors); $i++)
		$alErrors .= $errors[$i]."<br>";
	echo CAdminMessage::ShowMessage(Array("TYPE" => "ERROR", "MESSAGE" => GetMessage("WEBPROFY_AUTOREPLACE_UNINSTALL_ERROR"), "DETAILS" => $alErrors, "HTML" => true));
}
?>
<form action="<? echo $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<? echo LANG ?>">
	<input type="submit" name="" value="<? echo GetMessage("MOD_BACK") ?>">

	<form>