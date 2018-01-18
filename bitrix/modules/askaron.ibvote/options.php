<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/include.php");
##############################################
# Askaron.Ibvote module                      #
# Copyright (c) 2011 Askaron Systems         #
# http://askaron.ru                          #
# mailto:mail@askaron.ru                     #
##############################################


IncludeModuleLangFile(__FILE__);
$module_id = "askaron.ibvote";
$RIGHT = $APPLICATION->GetGroupRight($module_id);

$RIGHT_W = ($RIGHT>="W");
$RIGHT_R = ($RIGHT>="R");

if ($RIGHT_R)
{
	if (
		$REQUEST_METHOD=="GET"
		&& $RIGHT_W
		&& strlen($RestoreDefaults)>0
		&& check_bitrix_sessid()
	)
	{
		COption::RemoveOption("askaron.ibvote");
		$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch())
			$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
	}
	
	if(
		$REQUEST_METHOD=="POST" 
		&& strlen($Update) > 0 
		&& $RIGHT_W
		&& check_bitrix_sessid()
	)
	{
		if (isset($_REQUEST[ "clear_cache" ])) 
		{
			COption::SetOptionString($module_id, "clear_cache","Y");
		}
		else
		{
			COption::SetOptionString($module_id, "clear_cache","N");
		}		
	}	
	
	$clear_cache=COption::GetOptionString($module_id,"clear_cache");

	$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
		array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
	);
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	?>
	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>">
	<?=bitrix_sessid_post()?>

	<?$tabControl->Begin();?>
	<?$tabControl->BeginNextTab();?>
	<tr class="heading" colspan="2" align="center">
			<td valign="top" colspan="2" align="center"><?=GetMessage("askaron_ibvote_option_header_cache_text")?></td>
	</tr>
	<tr>	
		<td valign="top" width="50%" class="field-name"><label for='askaron_ibvote_clear_cache'><?=GetMessage("askaron_ibvote_option_header_clear_cache_label")?>&nbsp;:</label></td>
		<td valign="top" width="50%">
			<input
				type="checkbox"
				value="Y" 
				id="askaron_ibvote_clear_cache"
				name="clear_cache"
				<?if($clear_cache=="Y"):?>
					checked="checked"
				<?endif?> 
			>
		</td>
	</tr>
		
	<?$tabControl->BeginNextTab();?>
	<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
	<?$tabControl->Buttons();?>
	<script type="text/javascript">
	function RestoreDefaults()
	{
		if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
			window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?echo urlencode($mid)?>&<?echo bitrix_sessid_get()?>";
	}
	</script>
	<input <?if (!$RIGHT_W) echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
	<input type="hidden" name="Update" value="Y">
	<input type="reset" name="reset" value="<?=GetMessage("MAIN_RESET")?>">
	
	<input <?if (!$RIGHT_W) echo "disabled" ?> type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	
	<?$tabControl->End();?>
	</form>
<?
}
?>
