<?php
if(!$USER->IsAdmin()) {
	return;
}
CModule::IncludeModule('karudo.vcs');
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

CVCSMain::InitJS();

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	//array("DIV" => "edit2", "TAB" => GetMessage("VCS_DRIVER_TAB_SET"), "TITLE" => GetMessage("VCS_DRIVER_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$Update = empty($_REQUEST['Update']) ? '' : $_REQUEST['Update'];
$Apply = empty($_REQUEST['Apply']) ? '' : $_REQUEST['Apply'];

$strWarning = "";
if($_SERVER['REQUEST_METHOD'] == 'POST' && strlen($Update.$Apply)>0 && check_bitrix_sessid()) {

	/*foreach ($_POST['driver_options'] as $code => $arSettings) {
		$arSettings['extensions'] = array_filter($arSettings['extensions']);
		$arSettings['included_dirs'] = array_filter($arSettings['included_dirs']);
		$arSettings['excluded_dirs'] = array_filter($arSettings['excluded_dirs']);
		CVCSConfig::SetOptionArray(CVCSConfig::GetConfigDriverKey($code), $arSettings);
	}*/

	CVCSConfig::SetStepExecutionTime($_POST['step_execution_time']);
	CVCSConfig::SetDriversInMenu($_POST['show_drivers_in_menu']);
	CVCSConfig::SetShowPanelButtons($_POST['show_panel_buttons']);

	if (CVCSConfig::GetShowPanelButtons()) {
		RegisterModuleDependences('main', 'OnProlog', CVCSConfig::MODULE_ID, 'CVCSMain', 'AddPanelButtons');
	} else {
		UnRegisterModuleDependences('main', 'OnProlog', CVCSConfig::MODULE_ID, 'CVCSMain', 'AddPanelButtons');
	}

	//*
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
	//*/
}
//$arDrivers = CVCSMain::GetDriversArray(array('all' => true));

$tabControl->Begin();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?$tabControl->BeginNextTab();?>
	<tr>
		<td valign="top" width="40%"><?=GetMessage('VCS_STEP_EXECUTION_TIME')?>:</td>
		<td valign="top" width="60%">
			<input type="text" name="step_execution_time" value="<?=CVCSConfig::GetStepExecutionTime()?>" />
		</td>
	</tr>
	<tr>
		<td valign="top" width="40%"><?=GetMessage("VCS_SHOW_PANEL_BUTTONS")?>:</td>
		<td valign="top" width="60%">
			<input type="hidden" name="show_panel_buttons" value="0">
			<input type="checkbox" name="show_panel_buttons" value="1"<? if(CVCSConfig::GetShowPanelButtons()) { ?> checked="checked"<? } ?>>
		</td>
	</tr>
<?
	//$tabControl->BeginNextTab();
	?>

	<tr>
		<td valign="top" width="40%"><?=GetMessage('VCS_SHOW_DRIVERS_IN_MENU')?>:</td>
		<td valign="top" width="60%">
			<input type="hidden" name="show_drivers_in_menu" value="0">
			<input type="checkbox" name="show_drivers_in_menu" value="1"<? if(CVCSConfig::GetDriversInMenu()) { ?> checked="checked"<? } ?>>
			<a href="/bitrix/admin/karudo.drivers_list.php?lang=<?=LANG?>"><?=GetMessage('VCS_GOTO_EDIT_DRIVERS')?></a>
		</td>
	</tr>




<?$tabControl->Buttons();?>

	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
	<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
	<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<script type="text/javascript">
Karudo.$(function() {
	Karudo.$('.add-list-row').click(function() {
		Karudo.$(this).parent().find('div').last().clone(true).insertBefore(this).find('input').val('');

		return false;
	});
});
</script>