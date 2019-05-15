<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(!$USER->IsAdmin()) {
	CVCSMain::GetAPPLICATION()->AuthForm(GetMessage("ACCESS_DENIED"));
}
$iIncludeResult = CModule::IncludeModuleEx('karudo.vcs');
if ($iIncludeResult == MODULE_DEMO_EXPIRED) {
	die(GetMessage("VCS_EXPIRED"));
}
$item_id = empty($_REQUEST['item_id']) ? 0 : intval($_REQUEST['item_id']);
$revision_id = empty($_REQUEST['revision_id']) ? 0 : intval($_REQUEST['revision_id']);

$Item = CVCSItem::GetByID($item_id);
if (empty($Item)) {
	die(GetMessage("VCS_UNKNOWN_ITEM"));
}

$strFileName = basename($Item->GetOrigID());
$strFileContent = $Item->GetSource($revision_id);

header("Content-Type: application/force-download; name=\"" . CVCSAdminHelpers::FNConvertCharset($strFileName) . "\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . (function_exists("mb_strlen") ? mb_strlen($strFileContent, 'latin1') : strlen($strFileContent)));
header("Content-Disposition: attachment; filename=\"" . CVCSAdminHelpers::FNConvertCharset($strFileName) . "\"");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
echo $strFileContent;

die();