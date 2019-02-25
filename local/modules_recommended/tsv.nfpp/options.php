<?
if(!$USER->IsAdmin())
	return;

IncludeModuleLangFile(__FILE__);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("TSV_NFPP_MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => GetMessage("TSV_NFPP_MAIN_TAB_TITLE_SET")),	
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD == "POST" && strlen($Update) > 0 && check_bitrix_sessid())
{
	if(!$_POST['ONLY_FOR_ADMIN'])
		$_POST['ONLY_FOR_ADMIN'] = 'N';
	foreach($_POST as $key=>$val)
	{
		COption::SetOptionString(tsv_nfpp::MODULE_ID, $key, $val);
	}
}

$tabControl->Begin();
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?$tabControl->BeginNextTab();?>
		<tr class="heading">
			<td align="center" valign="top" colspan="3"><?=GetMessage("TSV_NFPP_MAIN_TAB_TITLE_SET");?></td>
		</tr>		
		<tr>
			<td valign="top" style="text-align: left">				
				<input type="checkbox" value="Y" id="ONLY_FOR_ADMIN" name="ONLY_FOR_ADMIN" <?if(COption::GetOptionString(tsv_nfpp::MODULE_ID, "ONLY_FOR_ADMIN")=="Y"):?>checked="checked"<?endif?>>			
				<label for="ONLY_FOR_ADMIN"><?=GetMessage("TSV_NFPP_ONLY_FOR_ADMIN");?></label>
			</td>
			<td valign="middle">
				
			</td>
			<td>
				&nbsp;
			</td>
		</tr>		

<?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("TSV_NFPP_MAIN_SAVE")?>" title="<?=GetMessage("TSV_NFPP_MAIN_OPT_SAVE_TITLE")?>">	
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
