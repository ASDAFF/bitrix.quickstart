<?
$MODULE_ID = "%DOT%";
$MLANG = "%UNDER_CAPS%_";

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule($MODULE_ID);
CModule::IncludeModule("main");

$aTabs = array(
	array(
		'DIV' => 'index',
		'TAB' => GetMessage('MAIN_TAB_SET'),
		'ICON' => $MODULE_ID,
		'TITLE' => GetMessage('MAIN_TAB_TITLE_SET'),
		'OPTIONS' => Array(
			'PARAMETER' => Array(GetMessage($MLANG.'PARAM_PARAMETER'), Array('text', 70)),
			'CHECKBOX' => Array(GetMessage($MLANG.'PARAM_CHECKBOX'), Array('checkbox')),
		)
	),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && check_bitrix_sessid()){
	if (strlen($RestoreDefaults) > 0){
		COption::RemoveOption($MODULE_ID);
	}else{
		foreach ($aTabs as $i => $aTab){
			foreach ($aTab["OPTIONS"] as $name => $arOption){
				$disabled = array_key_exists("disabled", $arOption) ? $arOption["disabled"] : "";
				if ($disabled)
					continue;

				$val = $_POST[$name];
				if ($arOption[1][0] == "checkbox" && $val != "Y")
					$val = "N";

				COption::SetOptionString($MODULE_ID, $name, $val, $arOption[0]);
			}
		}
	}


	if (strlen($Update) > 0 && strlen($_REQUEST["back_url_settings"]) > 0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}

$tabControl->Begin();
?>
<form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<?= LANGUAGE_ID ?>">
	<?
	foreach ($aTabs as $aTab):
		$tabControl->BeginNextTab();
		foreach ($aTab["OPTIONS"] as $name => $arOption):
			$val      = COption::GetOptionString($MODULE_ID, $name);
			$type     = $arOption[1];
			$disabled = array_key_exists("disabled", $arOption) ? $arOption["disabled"] : "";
			$name = htmlspecialcharsbx($name);

			?>
			<tr>
				<td width="40%" nowrap <?=($type[0] == "textarea")?'class="adm-detail-valign-top"':'';?>>
					<label for="<?=$name?>"><? echo $arOption[0] ?></label>
				<td width="60%">
					<? if ($type[0] == "checkbox"): ?>
						<input type="checkbox" name="<?=$name;?>" id="<?=$name;?>" value="Y"<?=($val == "Y")?' checked':'';?><?=($disabled)?' disabled="disabled"':'';?>>
						<?=($disabled)?'<br>'.$disabled:''; ?>
					<? elseif ($type[0] == "text"): ?>
						<input type="text" size="<? echo $type[1] ?>" maxlength="255"
						       value="<? echo htmlspecialcharsbx($val) ?>" name="<? echo htmlspecialcharsbx($name) ?>">
					<?
					elseif ($type[0] == "textarea"): ?>
						<textarea rows="<? echo $type[1] ?>" cols="<? echo $type[2] ?> "
						          name="<? echo htmlspecialcharsbx($name) ?>" style=
						"width:100%"><? echo htmlspecialcharsbx($val) ?></textarea>
					<?endif ?>
				</td>
			</tr>
		<?endforeach;
	endforeach;?>

	<? $tabControl->Buttons(); ?>
	<input type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE") ?>"
	       title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>" class="adm-btn-save">
	<input type="submit" name="Apply" value="<?= GetMessage("MAIN_OPT_APPLY") ?>"
	       title="<?= GetMessage("MAIN_OPT_APPLY_TITLE") ?>">
	<? if (strlen($_REQUEST["back_url_settings"]) > 0): ?>
		<input type="button" name="Cancel" value="<?= GetMessage("MAIN_OPT_CANCEL") ?>"
		       title="<?= GetMessage("MAIN_OPT_CANCEL_TITLE") ?>"
		       onclick="window.location='<? echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'">
		<input type="hidden" name="back_url_settings" value="<?= htmlspecialcharsbx($_REQUEST["back_url_settings"]) ?>">
	<? endif ?>
	<input type="submit" name="RestoreDefaults" title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
	       OnClick="return confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
	       value="<? echo GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
	<?= bitrix_sessid_post(); ?>
	<? $tabControl->End(); ?>
</form>
