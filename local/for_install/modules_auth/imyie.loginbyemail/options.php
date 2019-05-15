<?if(!$USER->IsAdmin()) return;

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('imyie.loginbyemail');

if(isset($_REQUEST["save"]) && check_bitrix_sessid())
{
	if($_REQUEST["loginbyemail_onoff"]=="Y")
	{
		CIMYIELoginByEmail::On();
	} else {
		CIMYIELoginByEmail::Off();
	}
}

$aTabs = array(
	array("DIV" => "imyie_tab1", "TAB" => GetMessage("IMYIE_SETTINGS"), "ICON" => "settings", "TITLE" => GetMessage("IMYIE_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$tabControl->Begin();
$auth_by_email = COption::GetOptionString("imyie.loginbyemail", "auth_by_email");
$loginbylink_onoff = COption::GetOptionString("imyie.loginbyemail", "loginbylink_onoff");
?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
<?$tabControl->BeginNextTab();?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("IMYIE_LOGINBYEMAIL_MAIN_SETTINGS")?></td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_LOGINBYEMAIL_ACTIVE")?>:</td>
		<td valign="top" width="50%"><input type="checkbox" name="loginbyemail_onoff" value="Y"<?if($auth_by_email=="Y"):?> checked="checked" <?endif;?> /></td>
	</tr>
	<?/*?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("IMYIE_LOGINBYLINK_PART")?></td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("IMYIE_LOGINBYLINK_ACTIVE")?>:</td>
		<td valign="top" width="50%"><input type="checkbox" name="loginbylink_onoff" value="Y"<?if($loginbylink_onoff=="Y"):?> checked="checked" <?endif;?> /></td>
	</tr>
	<?*/?>
<?$tabControl->Buttons();?>
	<input type="submit" name="save" value="<?=GetMessage("IMYIE_LOGINBYEMAIL_BTN_SAVE")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>