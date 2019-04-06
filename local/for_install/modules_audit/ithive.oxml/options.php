<?
if(!$USER->IsAdmin())
	return;

CModule::IncludeModule('ithive.oxml');
$module_id = "ithive.oxml";

IncludeModuleLangFile(__FILE__);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "extranet_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && check_bitrix_sessid())
{
	if(strlen($_POST["RestoreDefaults"]) > 0)
	{
		COption::RemoveOption($module_id);
	}
	elseif(strlen($Update) > 0)
	{
		COption::SetOptionString($module_id, "informer_blocks", implode('|', $INFORMER_BLOCKS));
	}
}
$dir = unserialize(COption::GetOptionString($module_id, 'options'));
?>

<?$tabControl->Begin();?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>&amp;mid=<?=htmlspecialcharsbx($mid)?>">
<?$tabControl->BeginNextTab();?>

	<tr>
		<td valign="top"><b><?echo GetMessage("ITHIVE_DIR_TO_INSTALL")?></b></td>
		<td valign="middle"><a href='<?=$dir["site"]["dir_full"]?>' target='_blank'><?=$dir["site"]["dir_full"]?></a></td>
	</tr>
	
<?$tabControl->Buttons();?>
<!--
<input type="submit" name="Update" value="<?=GetMessage("ITHIVE_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<input type="reset" name="reset" value="<?=GetMessage("ITHIVE_RESET")?>">
<input type="submit" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>" name="RestoreDefaults">-->
<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>