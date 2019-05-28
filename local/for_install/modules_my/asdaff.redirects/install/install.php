<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

if (!check_bitrix_sessid()) return;
include(GetLangFileName(__FILE__));

$sModuleId = "asdaff.redirects";
$fileBackupName = '.htaccess-'.$sModuleId.'.bac';

//Module Register
RegisterModule($sModuleId);

//Copy Admin files
CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $sModuleId . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/", true, true);
CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $sModuleId . "/install/images/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images/seo2_redirects/", true, true);
CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $sModuleId . "/install/themes/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/", true, true);

RegisterModuleDependences("main", "OnBeforeProlog", "asdaff.redirects", "seo2Redirects", "handlerOnBeforeProlog", "100");

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

<form action="/bitrix/admin/asdaff_redirects_index.php">
	<input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="submit" name="" value="<? echo GetMessage("SEO2_REDIRECT_GO_TO_MODULE") ?>">
</form>

<form action="<? echo $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<? echo LANG ?>">
	<input type="submit" name="" value="<? echo GetMessage("MOD_BACK") ?>">	
</form>