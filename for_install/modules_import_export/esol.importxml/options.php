<?
use Bitrix\Main\Localization\Loc;
$module_id = 'esol.importxml';

if($USER->IsAdmin())
{
	Loc::loadMessages(__FILE__);

	$aTabs = array(
		array("DIV" => "edit0", "TAB" => Loc::getMessage("ESOL_IX_SETTINGS"), "ICON" => "", "TITLE" => Loc::getMessage("ESOL_IX_SETTINGS_TITLE")),
		array("DIV" => "edit2", "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"), "ICON" => "form_settings", "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")),
	);
	$tabControl = new CAdminTabControl("esolImportxmlTabControl", $aTabs, true, true);

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
	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANGUAGE_ID?>&mid_menu=1&mid=<?=$module_id?>" name="essol_importxml_settings">
	<? echo bitrix_sessid_post();

	$tabControl->BeginNextTab();
	$setMaxExecutionTime = (bool)(COption::GetOptionString($module_id, 'SET_MAX_EXECUTION_TIME')=='Y');
	?>
	<tr>
		<td width="50%"><? echo Loc::getMessage('ESOL_IX_SET_MAX_EXECUTION_TIME'); ?></td>
		<td width="50%">
			<input type="hidden" name="SETTINGS[SET_MAX_EXECUTION_TIME]" value="N">
			<input type="checkbox" name="SETTINGS[SET_MAX_EXECUTION_TIME]" value="Y" onchange="document.getElementById('MAX_EXECUTION_TIME').style.display=document.getElementById('EXECUTION_DELAY').style.display=(this.checked ? '' : 'none')" <?if($setMaxExecutionTime){echo 'checked';}?>>
		</td>
	</tr>
	<tr id="MAX_EXECUTION_TIME" <?if(!$setMaxExecutionTime){echo 'style="display: none;"';}?>>
		<td width="50%"><? echo Loc::getMessage('ESOL_IX_MAX_EXECUTION_TIME'); ?></td>
		<td width="50%">
			<input type="text" name="SETTINGS[MAX_EXECUTION_TIME]" value="<?echo htmlspecialcharsex(COption::GetOptionString($module_id, 'MAX_EXECUTION_TIME'));?>" size="4" maxlength="4">
		</td>
	</tr>
	<tr id="EXECUTION_DELAY" <?if(!$setMaxExecutionTime){echo 'style="display: none;"';}?>>
		<td width="50%"><? echo Loc::getMessage('ESOL_IX_EXECUTION_DELAY'); ?></td>
		<td width="50%">
			<input type="text" name="SETTINGS[EXECUTION_DELAY]" value="<?echo htmlspecialcharsex(COption::GetOptionString($module_id, 'EXECUTION_DELAY'));?>" size="3" maxlength="3">
		</td>
	</tr>
	<tr>
		<td width="50%"><? echo Loc::getMessage('ESOL_IX_AUTO_CONTINUE_IMPORT'); ?></td>
		<td width="50%">
			<input type="hidden" name="SETTINGS[AUTO_CONTINUE_IMPORT]" value="N">
			<input type="checkbox" name="SETTINGS[AUTO_CONTINUE_IMPORT]" value="Y" <?if(COption::GetOptionString($module_id, 'AUTO_CONTINUE_IMPORT', 'N')=='Y'){echo 'checked';}?>>
		</td>
	</tr>
	<tr>
		<td width="50%"><? echo Loc::getMessage('ESOL_IX_AUTO_CORRECT_ENCODING'); ?></td>
		<td width="50%">
			<input type="hidden" name="SETTINGS[AUTO_CORRECT_ENCODING]" value="N">
			<input type="checkbox" name="SETTINGS[AUTO_CORRECT_ENCODING]" value="Y" <?if(COption::GetOptionString($module_id, 'AUTO_CORRECT_ENCODING', 'N')=='Y'){echo 'checked';}?>>
		</td>
	</tr>
	
	<tr class="heading">
		<td colspan="2"><? echo Loc::getMessage('ESOL_IX_OPTIONS_CRON_SETTINGS'); ?></td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_CRON_NEED_CHECKSIZE'); ?> <span id="hint_CRON_NEED_CHECKSIZE"></span><script>BX.hint_replace(BX('hint_CRON_NEED_CHECKSIZE'), '<?echo Loc::getMessage("ESOL_IX_OPTIONS_CRON_NEED_CHECKSIZE_HINT"); ?>');</script></td>
		<td>
			<input type="hidden" name="SETTINGS[CRON_NEED_CHECKSIZE]" value="N">
			<input type="checkbox" name="SETTINGS[CRON_NEED_CHECKSIZE]" value="Y" <?if(COption::GetOptionString($module_id, 'CRON_NEED_CHECKSIZE', 'N')=='Y') echo 'checked';?>>
		</td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_CRON_CONTINUE_LOADING'); ?></td>
		<td>
			<input type="hidden" name="SETTINGS[CRON_CONTINUE_LOADING]" value="N">
			<input type="checkbox" name="SETTINGS[CRON_CONTINUE_LOADING]" value="Y" <?if(COption::GetOptionString($module_id, 'CRON_CONTINUE_LOADING', 'N')=='Y') echo 'checked';?>>
		</td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_CRON_REMOVE_LOADED_FILE'); ?></td>
		<td>
			<input type="hidden" name="SETTINGS[CRON_REMOVE_LOADED_FILE]" value="N">
			<input type="checkbox" name="SETTINGS[CRON_REMOVE_LOADED_FILE]" value="Y" <?if(COption::GetOptionString($module_id, 'CRON_REMOVE_LOADED_FILE', 'N')=='Y') echo 'checked';?>>
		</td>
	</tr>
	
	<tr class="heading">
		<td colspan="2"><? echo Loc::getMessage('ESOL_IX_OPTIONS_NOTIFY'); ?></td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_NOTIFY_MODE'); ?>:</td>
		<td>
			<label><input type="radio" name="SETTINGS[NOTIFY_MODE]" value="NONE" <?if(COption::GetOptionString($module_id, 'NOTIFY_MODE', 'NONE')=='NONE') echo 'checked';?>> <? echo Loc::getMessage('ESOL_IX_OPTIONS_NOTIFY_MODE_NONE'); ?><label><br>
			<label><input type="radio" name="SETTINGS[NOTIFY_MODE]" value="CRON" <?if(COption::GetOptionString($module_id, 'NOTIFY_MODE', 'NONE')=='CRON') echo 'checked';?>> <? echo Loc::getMessage('ESOL_IX_OPTIONS_NOTIFY_MODE_CRON'); ?><label><br>
			<label><input type="radio" name="SETTINGS[NOTIFY_MODE]" value="ALL" <?if(COption::GetOptionString($module_id, 'NOTIFY_MODE', 'NONE')=='ALL') echo 'checked';?>> <? echo Loc::getMessage('ESOL_IX_OPTIONS_NOTIFY_MODE_ALL'); ?><label>
		</td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_NOTIFY_EMAIL'); ?>:</td>
		<td>
			<input type="text" name="SETTINGS[NOTIFY_EMAIL]" value="<?echo htmlspecialcharsex(COption::GetOptionString($module_id, 'NOTIFY_EMAIL'));?>">
		</td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_NOTIFY_BEGIN_IMPORT'); ?>:</td>
		<td>
			<input type="hidden" name="SETTINGS[NOTIFY_BEGIN_IMPORT]" value="N">
			<input type="checkbox" name="SETTINGS[NOTIFY_BEGIN_IMPORT]" value="Y" <?if(COption::GetOptionString($module_id, 'NOTIFY_BEGIN_IMPORT', 'N')=='Y') echo 'checked';?>>
		</td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_NOTIFY_END_IMPORT'); ?>:</td>
		<td>
			<input type="hidden" name="SETTINGS[NOTIFY_END_IMPORT]" value="N">
			<input type="checkbox" name="SETTINGS[NOTIFY_END_IMPORT]" value="Y" <?if(COption::GetOptionString($module_id, 'NOTIFY_END_IMPORT', 'N')=='Y') echo 'checked';?>>
		</td>
	</tr>
	
	<tr class="heading">
		<td colspan="2"><? echo Loc::getMessage('ESOL_IX_OPTIONS_DISCOUNT'); ?></td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_DISCOUNT_MODE'); ?>:</td>
		<td>
			<label><input type="radio" name="SETTINGS[DISCOUNT_MODE]" value="SPLIT" <?if(COption::GetOptionString($module_id, 'DISCOUNT_MODE', 'SPLIT')=='SPLIT') echo 'checked';?>> <? echo Loc::getMessage('ESOL_IX_OPTIONS_DISCOUNT_MODE_SPLIT'); ?></label><br>
			<label><input type="radio" name="SETTINGS[DISCOUNT_MODE]" value="JOIN" <?if(COption::GetOptionString($module_id, 'DISCOUNT_MODE', 'SPLIT')=='JOIN') echo 'checked';?>> <? echo Loc::getMessage('ESOL_IX_OPTIONS_DISCOUNT_MODE_JOIN'); ?></label><br>
		</td>
	</tr>
	
	<tr class="heading">
		<td colspan="2"><? echo Loc::getMessage('ESOL_IX_OPTIONS_EXTERNAL_SERVICES'); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><b><? echo Loc::getMessage('ESOL_IX_OPTIONS_YANDEX_DISC'); ?></b></td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_YANDEX_DISC_APIKEY'); ?>:</td>
		<td>
			<input type="text" name="SETTINGS[YANDEX_APIKEY]" value="<?echo htmlspecialcharsex(COption::GetOptionString($module_id, 'YANDEX_APIKEY', ''))?>" size="35">
			&nbsp; <a href="https://oauth.yandex.ru/authorize?response_type=token&client_id=30e9fb3edb184522afaf5e72ee255cbc" target="_blank"><? echo Loc::getMessage('ESOL_IX_OPTIONS_YANDEX_DISC_APIKEY_GET'); ?></a>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><br><b><? echo Loc::getMessage('ESOL_IX_OPTIONS_CLOUD_MAILRU'); ?></b></td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_CLOUD_MAILRU_LOGIN'); ?>:</td>
		<td>
			<input type="text" name="SETTINGS[CLOUD_MAILRU_LOGIN]" value="<?echo htmlspecialcharsex(COption::GetOptionString($module_id, 'CLOUD_MAILRU_LOGIN', ''))?>" size="35">
		</td>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('ESOL_IX_OPTIONS_CLOUD_MAILRU_PASSWORD'); ?>:</td>
		<td>
			<input type="text" name="SETTINGS[CLOUD_MAILRU_PASSWORD]" value="<?echo htmlspecialcharsex(COption::GetOptionString($module_id, 'CLOUD_MAILRU_PASSWORD', ''))?>" size="35">
		</td>
	</tr>
	<?$tabControl->BeginNextTab();?>
	<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
	<?
	$tabControl->Buttons();?>
<script type="text/javascript">
function RestoreDefaults()
{
	if (confirm('<? echo CUtil::JSEscape(Loc::getMessage("ESOL_IX_OPTIONS_BTN_HINT_RESTORE_DEFAULT_WARNING")); ?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?lang=<? echo LANGUAGE_ID; ?>&mid_menu=1&mid=<? echo $module_id; ?>&RestoreDefaults=Y&<?=bitrix_sessid_get()?>";
}
</script>
	<input type="submit" name="Update" value="<?echo Loc::getMessage("ESOL_IX_OPTIONS_BTN_SAVE")?>">
	<input type="hidden" name="Update" value="Y">
	<input type="reset" name="reset" value="<?echo Loc::getMessage("ESOL_IX_OPTIONS_BTN_RESET")?>">
	<input type="button" title="<?echo Loc::getMessage("ESOL_IX_OPTIONS_BTN_HINT_RESTORE_DEFAULT")?>" onclick="RestoreDefaults();" value="<?echo Loc::getMessage("ESOL_IX_OPTIONS_BTN_RESTORE_DEFAULT")?>">
	<?$tabControl->End();?>
	</form>
<?
}
?>