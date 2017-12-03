<?
if(!$USER->IsAdmin()) return;

$module_id = "asdaff.mass";

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php"); 
IncludeModuleLangFile(__FILE__);

if($REQUEST_METHOD=="POST" && strlen($RestoreDefaults)>0 && check_bitrix_sessid()) {
	$arGroups = array();
	$resGroups = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($arGroup = $resGroups->GetNext(false,false)) {
		$arGroups[] = $arGroup["ID"];
	}
	$APPLICATION->DelGroupRight($module_id, $arGroups);
	LocalRedirect($_SERVER["REQUEST_URI"]);
}

$aTabs = array();
$aTabs[] = array("DIV" => "tab_1", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS"));
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
	<?=bitrix_sessid_post();?>
	<?$tabControl->BeginNextTab();?>
		<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
	<?$tabControl->Buttons();?>
		<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
		<input type="hidden" name="Update" value="Y">
		<input type="submit" name="Apply" value="<?=GetMessage("MAIN_APPLY")?>">
		<input type="submit" name="RestoreDefaults" value="<?=GetMessage("MAIN_RESET")?>" onclick="return confirm('<?=AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>');">
	<?$tabControl->End();?>
</form>