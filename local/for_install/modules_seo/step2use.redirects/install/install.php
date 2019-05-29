<?
if (!check_bitrix_sessid()) return;
include(GetLangFileName(__FILE__, ''));

$sModuleId = "step2use.redirects";
$fileBackupName = '.htaccess-'.$sModuleId.'.bac';

//Module Register
RegisterModule($sModuleId);

//Copy Admin files
CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $sModuleId . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/", true, true);
CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $sModuleId . "/install/images/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images/s2u_redirects/", true, true);
CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $sModuleId . "/install/themes/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/", true, true);

RegisterModuleDependences("main", "OnBeforeProlog", "step2use.redirects", "S2uRedirects", "handlerOnBeforeProlog", "1000");
RegisterModuleDependences("main", "OnEpilog", "step2use.redirects", "S2uRedirects", "handlerOnEpilog", "1000");
RegisterModuleDependences("iblock", "OnBeforeIBlockSectionUpdate", "step2use.redirects", "S2uRedirects", "handlerOnBeforeIBlockSectionUpdate", "1000");
RegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", "step2use.redirects", "S2uRedirects", "handlerOnBeforeIBlockElementUpdate", "1000");
RegisterModuleDependences("iblock", "OnBeforeIBlockElementDelete", "step2use.redirects", "S2uRedirects", "OnBeforeIBlockElementDeleteHandler", "100");
RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "step2use.redirects", "S2uRedirects", "OnAfterIBlockElementAddHandler", "1000");
//RegisterModuleDependences("iblock", "OnIBlockElementUpdate", "step2use.redirects", "S2uRedirects", "handlerOnIBlockElementUpdate", "100");

//CAgent::RemoveModuleAgents("step2use.redirects");
CAgent::AddAgent("S2uRedirects::DeleteOldEntities();", "step2use.redirects", "N", 86400);
//-----------------------------------------------------------------------------------------------------------------------

if($ex = $APPLICATION->GetException())
	echo CAdminMessage::ShowMessage(Array(
		"TYPE" => "ERROR",
		"MESSAGE" =>  GetMessage("MOD_INST_OK"),
		"DETAILS" => $ex->GetString(),
		"HTML" => true,
	));
else
    echo CAdminMessage::ShowMessage(Array(
		"TYPE" => "OK",
		"MESSAGE" =>  GetMessage("MOD_INST_OK"),
		"HTML" => true,
	));
?>

<form action="/bitrix/admin/step2use_redirects_index.php">
	<input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="submit" name="" value="<? echo GetMessage("S2U_REDIRECT_GO_TO_MODULE") ?>">	
</form>

<form action="<? echo $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<? echo LANG ?>">
	<input type="submit" name="" value="<? echo GetMessage("MOD_BACK") ?>">	
</form>
