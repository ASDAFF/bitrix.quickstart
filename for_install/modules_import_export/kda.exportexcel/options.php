<?
use Bitrix\Main\Localization\Loc;
$module_id = 'kda.exportexcel';

if($USER->IsAdmin())
{
	Loc::loadMessages(__FILE__);

	$aTabs = array(
		array("DIV" => "edit0", "TAB" => Loc::getMessage("KDA_EE_SETTINGS"), "ICON" => "", "TITLE" => Loc::getMessage("KDA_EE_SETTINGS_TITLE")),
		array("DIV" => "edit1", "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"), "ICON" => "form_settings", "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")),
	);
	$tabControl = new CAdminTabControl("kdaExportexcelTabControl", $aTabs, true, true);

	if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['RestoreDefaults']) && !empty($_GET['RestoreDefaults']) && check_bitrix_sessid())
	{
		COption::RemoveOption($module_id);
		$arGROUPS = array();
		$z = CGroup::GetList($v1, $v2, array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch())
		{
			$ar = array();
			$ar["ID"] = intval($zr["ID"]);
			$ar["NAME"] = htmlspecialcharsbx($zr["NAME"])." [<a title=\"".GetMessage("MAIN_USER_GROUP_TITLE")."\" href=\"/bitrix/admin/group_edit.php?ID=".intval($zr["ID"])."&lang=".LANGUAGE_ID."\">".intval($zr["ID"])."</a>]";
			$groups[$zr["ID"]] = "[".$zr["ID"]."] ".$zr["NAME"];
			$arGROUPS[] = $ar;
		}
		reset($arGROUPS);
		while (list(,$value) = each($arGROUPS))
			$APPLICATION->DelGroupRight($module_id, array($value["ID"]));
	
		LocalRedirect($APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID.'&mid_menu=1&mid='.$module_id);
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
	{
		if(isset($_POST['Update']) && $_POST['Update'] === 'Y' && is_array($_POST['SETTINGS']))
		{
			foreach($_POST['SETTINGS'] as $k=>$v)
			{
				COption::SetOptionString($module_id, $k, $v);
			}

			//LocalRedirect($APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID.'&mid_menu=1&mid='.$module_id.'&'.$tabControl->ActiveTabParam());
		}
	}


	$tabControl->Begin();
	?>
	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANGUAGE_ID?>&mid_menu=1&mid=<?=$module_id?>" name="kda_exportexacel_settings">
	<? echo bitrix_sessid_post();?>
	
	<?
	$tabControl->BeginNextTab();
	$setMaxExecutionTime = (bool)(COption::GetOptionString($module_id, 'SET_MAX_EXECUTION_TIME')=='Y');
	?>
	<tr class="heading">
		<td colspan="2"><? echo Loc::getMessage('KDA_EE_OPTIONS_COMMON_SETTINGS'); ?></td>
	</tr>
	<tr>
		<td width="50%"><? echo Loc::getMessage('KDA_EE_SET_MAX_EXECUTION_TIME'); ?></td>
		<td width="50%">
			<input type="hidden" name="SETTINGS[SET_MAX_EXECUTION_TIME]" value="N">
			<input type="checkbox" name="SETTINGS[SET_MAX_EXECUTION_TIME]" value="Y" onchange="document.getElementById('MAX_EXECUTION_TIME').style.display=document.getElementById('EXECUTION_DELAY').style.display=(this.checked ? '' : 'none')" <?if($setMaxExecutionTime){echo 'checked';}?>>
		</td>
	</tr>
	<tr id="MAX_EXECUTION_TIME" <?if(!$setMaxExecutionTime){echo 'style="display: none;"';}?>>
		<td width="50%"><? echo Loc::getMessage('KDA_EE_MAX_EXECUTION_TIME'); ?></td>
		<td width="50%">
			<input type="text" name="SETTINGS[MAX_EXECUTION_TIME]" value="<?echo htmlspecialcharsex(COption::GetOptionString($module_id, 'MAX_EXECUTION_TIME'));?>" size="3" maxlength="3">
		</td>
	</tr>
	<tr id="EXECUTION_DELAY" <?if(!$setMaxExecutionTime){echo 'style="display: none;"';}?>>
		<td width="50%"><? echo Loc::getMessage('KDA_EE_EXECUTION_DELAY'); ?></td>
		<td width="50%">
			<input type="text" name="SETTINGS[EXECUTION_DELAY]" value="<?echo htmlspecialcharsex(COption::GetOptionString($module_id, 'EXECUTION_DELAY'));?>" size="3" maxlength="3">
		</td>
	</tr>
	<tr>
		<td width="50%"><? echo Loc::getMessage('KDA_EE_AUTO_CONTINUE_IMPORT'); ?></td>
		<td width="50%">
			<input type="hidden" name="SETTINGS[AUTO_CONTINUE_EXPORT]" value="N">
			<input type="checkbox" name="SETTINGS[AUTO_CONTINUE_EXPORT]" value="Y" <?if(COption::GetOptionString($module_id, 'AUTO_CONTINUE_EXPORT', 'N')=='Y'){echo 'checked';}?>>
		</td>
	</tr>

	<?$tabControl->BeginNextTab();?>
	<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
	<?
	$tabControl->Buttons();?>
<script type="text/javascript">
function RestoreDefaults()
{
	if (confirm('<? echo CUtil::JSEscape(Loc::getMessage("KDA_EE_OPTIONS_BTN_HINT_RESTORE_DEFAULT_WARNING")); ?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?lang=<? echo LANGUAGE_ID; ?>&mid_menu=1&mid=<? echo $module_id; ?>&RestoreDefaults=Y&<?=bitrix_sessid_get()?>";
}
</script>
	<input type="submit" name="Update" value="<?echo Loc::getMessage("KDA_EE_OPTIONS_BTN_SAVE")?>">
	<input type="hidden" name="Update" value="Y">
	<input type="reset" name="reset" value="<?echo Loc::getMessage("KDA_EE_OPTIONS_BTN_RESET")?>">
	<input type="button" title="<?echo Loc::getMessage("KDA_EE_OPTIONS_BTN_HINT_RESTORE_DEFAULT")?>" onclick="RestoreDefaults();" value="<?echo Loc::getMessage("KDA_EE_OPTIONS_BTN_RESTORE_DEFAULT")?>">
	<?$tabControl->End();?>
	</form>
<?
}
?>