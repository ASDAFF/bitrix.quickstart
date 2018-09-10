<?
$module_id = 'site05.usertypeyesno';
$RIGHT = $APPLICATION->GetGroupRight($module_id);
if($RIGHT <= "R") return;
IncludeModuleLangFile(__FILE__);
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	COption::SetOptionString($module_id, "prop_check",$_POST["prop_check"]);
	if($_POST["prop_check"]=="Y") {
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $module_id, "CUserTypeYesNo", "GetUserTypeDescription", 50);
	}
	else{
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $module_id, "CUserTypeYesNo", "GetUserTypeDescription");
	}
}
$aTabs = array(
	array("DIV" => "main", "TAB" => "Настройки", "ICON" => "perfmon_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),	
	array('DIV' => 'rights', 'TAB' => GetMessage('MAIN_TAB_RIGHTS'), 'ICON' => 'wiki_settings', 'TITLE' => GetMessage('MAIN_TAB_TITLE_RIGHTS')),
);	
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">	
  <?$tabControl->Begin();
	$tabControl->BeginNextTab();?>

<input type="checkbox" <? if(COption::GetOptionString($module_id, "prop_check", "N") == "Y") echo " checked";?> value="Y" name="prop_check" id="designed_checkbox_0.04534579602170197" class="adm-designed-checkbox" />
<label class="adm-designed-checkbox-label" for="designed_checkbox_0.04534579602170197" style="padding-left:20px;padding-top:1px;width:450px;" title=""><?=GetMessage("SITE05_USE_PROP")?></label>

<?$tabControl->BeginNextTab();?>

<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/group_rights.php');?>


<?$tabControl->Buttons();?>
	<input <?if ($RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("SITE05_USE_SAVE")?>" title="<?=GetMessage("SITE05_USE_SAVE")?>" class="adm-btn-save">				
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<script>
	
</script>
