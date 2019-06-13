<?
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/qube.combine/include.php");

$MID = "qube.combine";

$POST_RIGHT = $APPLICATION->GetGroupRight($MID);
if ($POST_RIGHT < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$arPersonTypes = array();
$db_ptype = CSalePersonType::GetList(array("SORT" => "ASC"), $arFilter);
while ($ptype = $db_ptype->Fetch())
	$arPersonTypes[] =  $ptype;

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("QC_TAB1_TITLE"), "ICON" => "qube_combine_settings", "TITLE" => GetMessage("QC_TAB1_DESCRIPTION")),
	//array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "qube_combine_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

ob_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
$htmlGroupRights = ob_get_contents();
ob_end_clean();

$strRedirectURL = false;


if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
	COption::RemoveOption($MID);
}

if($REQUEST_METHOD=="POST" && strlen(serialize($COMBINE_FIELDS))>0 && check_bitrix_sessid())
{
	$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($zr = $z->Fetch())
		$APPLICATION->DelGroupRight($MID, array($zr["ID"]));

	if (!in_array('NAME',$COMBINE_FIELDS) && !in_array('CODE',$COMBINE_FIELDS)
		 && !in_array('DEFAULT_VALUE',$COMBINE_FIELDS)  && !in_array('DESCRIPTION',$COMBINE_FIELDS))
	{
		$strWarning = GetMessage("QC_ERROR_NOT_REQUIRED");
		
	}
	else
		COption::SetOptionString($MID, 'COMBINE_FIELDS', serialize($COMBINE_FIELDS));
		COption::SetOptionString($MID, 'ADMIN_ACTIVE', $ADMIN_ACTIVE);
		COption::SetOptionString($MID, 'COMPONENT_ACTIVE', $COMPONENT_ACTIVE);
	
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}
if(strlen($strWarning)>0)
	CAdminMessage::ShowNote($strWarning);

$aMenu = array(
	array(
		"TEXT"=> GetMessage("QC_OPDER_PROPS_TITLE"),
		"LINK"=>"sale_order_props.php?lang=".LANGUAGE_ID,
		"TITLE"=> GetMessage("QC_OPDER_PROPS_DESCRIPTION"),
	),
);
$context = new CAdminContextMenu($aMenu);
$context->Show();

$tabControl->Begin();
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
<?=bitrix_sessid_post();?>

<?$tabControl->BeginNextTab();?>
	<tr>
		<td width="40%"><label for="ADMIN_ACTIVE"><?=GetMessage("QC_ADMIN_ACTIVE")?></label></td>
		<td>
			<input type="checkbox" name="ADMIN_ACTIVE" id="ADMIN_ACTIVE" value="Y"	<?if(COption::GetOptionString('qube.combine', 'ADMIN_ACTIVE')=="Y")	echo "checked='checked'";?>>
		</td>
	</tr>
	<tr>
		<td width="40%"><label for="COMPONENT_ACTIVE"><?=GetMessage("QC_COMPONENT_ACTIVE")?></label></td>
		<td>
			<input type="checkbox" name="COMPONENT_ACTIVE" id="COMPONENT_ACTIVE" value="Y"	<?if(COption::GetOptionString('qube.combine', 'COMPONENT_ACTIVE')=="Y")	echo "checked='checked'";?>>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("QC_FIELDS")?></td>
		<td>
			<?
			$arOptions = array("NAME","CODE","DEFAULT_VALUE","DESCRIPTION");
			$arOptionsOther = array("SORT","ACTIVE","TYPE","REQUIED","UTIL","USER_PROPS","IS_LOCATION","IS_LOCATION4TAX","IS_ZIP","IS_EMAIL","IS_PROFILE_NAME","IS_PAYER","IS_FILTERED");
			$arFields = COption::GetOptionString('qube.combine', 'COMBINE_FIELDS', '');
			if(strlen($arFields) > 0)
				$arFields = unserialize($arFields);
			else
				$arFields = array();

			if(!$arFields)
				$arFields = array();
			?>
			<select name="COMBINE_FIELDS[]" multiple="" size="5">
				<optgroup label="<?=GetMessage("QC_MAIN_FIELDS")?>">
					<?foreach ($arOptions as $field):?>
						<option value="<?=$field?>" <?=(in_array($field, $arFields) ? "selected" : "")?>><?echo "[$field] ". GetMessage("QC_FIELDS_$field")?></option>
					<?endforeach;?>
				</optgroup>
				<optgroup label="<?=GetMessage("QC_OTHER_FIELDS")?>">
					<?foreach ($arOptionsOther as $field):?>
						<option value="<?=$field?>" <?=(in_array($field, $arFields) ? "selected" : "")?>><?echo "[$field] ". GetMessage("QC_FIELDS_$field")?></option>
					<?endforeach;?>
				</optgroup>
			</select>
		</td>
	</tr>
<?//$tabControl->BeginNextTab();?>
<?//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
	
	<input type="submit" name="Update" class="adm-btn-save" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input type="hidden" name="Update" value="Y">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<script language="JavaScript">
		function RestoreDefaults()
		{
			if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
				window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
		}
	</script>
	<input type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
</form>
<?$tabControl->End();?>