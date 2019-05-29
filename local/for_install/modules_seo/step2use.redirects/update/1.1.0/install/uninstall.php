<? if (!check_bitrix_sessid()) return; ?>
<?
global $APPLICATION;
//var_dump($savedata);exit;
$sModuleId = "step2use.redirects";
$DB = CDatabase::GetModuleConnection($sModuleId);

$errors = false;


// Admin Files
DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $sModuleId . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/");
DeleteDirFilesEx("/bitrix/images/s2u_redirects");
DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $sModuleId . "/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default/");
DeleteDirFilesEx("/bitrix/themes/.default/icons/s2u_redirects");

UnRegisterModuleDependences('main', 'OnBeforeProlog', "step2use.redirects", 'S2uRedirects', 'handlerOnBeforeProlog');
UnRegisterModuleDependences("main", "OnEpilog", "step2use.redirects", "S2uRedirects", "handlerOnEpilog", "100");
UnRegisterModuleDependences("iblock", "OnBeforeIBlockSectionUpdate", "step2use.redirects", "S2uRedirects", "handlerOnBeforeIBlockSectionUpdate", "100");
UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", "step2use.redirects", "S2uRedirects", "handlerOnBeforeIBlockElementUpdate", "100");

$DB->Query("DROP TABLE IF EXISTS s2u_redirects_404");
$DB->Query("DROP TABLE IF EXISTS s2u_redirects_rules");
$DB->Query("DROP TABLE IF EXISTS s2u_redirects_404_ignore");

// Module Dependences
UnRegisterModule($sModuleId);

if ($errors === false):
	echo CAdminMessage::ShowNote(GetMessage("MOD_UNINST_OK"));
else:
	for ($i = 0; $i < count($errors); $i++)
		$alErrors .= $errors[$i] . "<br>";
	echo CAdminMessage::ShowMessage(Array("TYPE" => "ERROR", "MESSAGE" => GetMessage("MOD_UNINST_ERR"), "DETAILS" => $alErrors, "HTML" => true));
endif;
?>
<form action="<? echo $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<? echo LANG ?>">
	<input type="submit" name="" value="<? echo GetMessage("MOD_BACK") ?>">	
</form>