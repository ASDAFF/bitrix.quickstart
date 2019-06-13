<?

global $MESS;
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/prolog.php");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

$module_id = "mcart.vacancy";
CModule::IncludeModule($module_id);

$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);


$prop_autoexport = COption::GetOptionString("mcart.vacancy", "AUTOEXPORT");
$prop_phone = COption::GetOptionString("mcart.vacancy", "PHONE");
$prop_mail = COption::GetOptionString("mcart.vacancy", "MAIL");


if($MOD_RIGHT>="R"):


if($MOD_RIGHT>="Y" || $USER->IsAdmin()):

	if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
	{
	}

	

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && check_bitrix_sessid())
	{
	COption::SetOptionString("mcart.vacancy", "MAIL", $MAIL);
	COption::SetOptionString("mcart.vacancy", "PHONE", $PHONE);
	COption::SetOptionString("mcart.vacancy", "AUTOEXPORT", $AUTOEXPORT);
	}
	
$prop_mail = COption::GetOptionString("mcart.vacancy", "MAIL");
$prop_phone = COption::GetOptionString("mcart.vacancy", "PHONE");
$prop_autoexport = COption::GetOptionString("mcart.vacancy", "AUTOEXPORT");
	

endif; //if($MOD_RIGHT>="W"):

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_TAB_RIGHTS")),
	array("DIV" => "edit2", "TAB" => GetMessage("VACANCY_SETTINGS"), "TITLE" => GetMessage("VACANCY_DETAIL")),
	
);


$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?
$tabControl->Begin();
?>

<style>
#tblTYPES tr td 			{vertical-align: top;}
#tblTYPES .wd-quick-edit 	{display: none; width: 500px;}
#tblTYPES .wd-quick-view	{padding: 3px; border: 1px solid transparent; width:800px;}
#tblTYPES .wd-input-hover 	{background-color:#F8F8F8; border: 1px solid #bbbbbb; cursor: pointer;}
textarea { word-wrap: break-word; }
</style>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>" name="webdav_settings">
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>

<?$tabControl->BeginNextTab();?>


<tr>
	<td   nowrap><?echo GetMessage('VACANCY_PHONE')?></td>
	<td  nowrap><input type = "text" name="PHONE" id="PHONE" value = "<?=$prop_phone?>" ></td>
</tr>
<tr>
	<td   nowrap><?echo GetMessage('VACANCY_MAIL')?></td>
	<td  nowrap><input type = "text" name="MAIL" id="MAIL" value = "<?=$prop_mail?>" ></td>
</tr>
<tr>
	<td ><?echo GetMessage('VACANCY_AUTOEXPORT')?><br><?echo GetMessage('VACANCY_AUTOEXPORT_COMMENT')?></td>
	<td><input type="checkbox" name="AUTOEXPORT" id="AUTOEXPORT" value=<?=$prop_autoexport?> checked = "checked"/></td>
</tr>


<?$tabControl->Buttons();?>

<input type="submit" name="Update" <?if ($MOD_RIGHT<"W") echo "disabled" ?> value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
<input type="hidden" name="Update" value="Y">

<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?endif;?>
