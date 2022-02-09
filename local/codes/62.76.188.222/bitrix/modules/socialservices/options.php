<?
if(!$USER->CanDoOperation('edit_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "socialservices";
CModule::IncludeModule($module_id);

$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/js/socialservices/css/ss.css");

$arSites = array();
$arSiteList = array('');
$dbSites = CSite::GetList(($b="sort"), ($o="asc"), array("ACTIVE" => "Y"));
while ($arSite = $dbSites->Fetch())
{
	$arSites[] = $arSite;
	$arSiteList[] = $arSite['ID'];
}

$oAuthManager = new CSocServAuthManager();
$arOptions = $oAuthManager->GetSettings();

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["Update"].$_POST["Apply"].$_POST["RestoreDefaults"] <> '' && check_bitrix_sessid())
{
	if($_POST["RestoreDefaults"] <> '')
	{
		COption::RemoveOption($module_id);
	}
	else
	{
		COption::SetOptionString("socialservices", "use_on_sites", serialize($_POST["use_on_sites"]));
		foreach($arSiteList as $site)
		{
			$suffix = ($site <> ''? '_bx_site_'.$site:'');
			COption::SetOptionString("socialservices", "auth_services".$suffix, serialize($_POST["AUTH_SERVICES".$suffix]));
			foreach($arOptions as $option)
			{
				if(is_array($option))
					$option[0] .= $suffix;
				__AdmSettingsSaveOption($module_id, $option);
			}
		}
	}

	if(strlen($_REQUEST["back_url_settings"]) > 0)
	{
		if($_POST["Apply"] <> '' || $_POST["RestoreDefaults"] <> '')
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam().($_REQUEST["siteTabControl_active_tab"] <> ''? "&siteTabControl_active_tab=".urlencode($_REQUEST["siteTabControl_active_tab"]):''));
		else
			LocalRedirect($_REQUEST["back_url_settings"]);
	}
	else
	{
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam().($_REQUEST["siteTabControl_active_tab"] <> ''? "&siteTabControl_active_tab=".urlencode($_REQUEST["siteTabControl_active_tab"]):''));
	}
}

?>
<script type="text/javascript">
function MoveRowUp(a)
{
	var table = BX.findParent(a, {'tag':'table'});
	var index = BX.findParent(a, {'tag':'tr'}).rowIndex;
	if(index == 0)
		return;
	table.rows[index].parentNode.insertBefore(table.rows[index], table.rows[index-1]);
	a.focus();
}
function MoveRowDown(a)
{
	var table = BX.findParent(a, {'tag':'table'});
	var index = BX.findParent(a, {'tag':'tr'}).rowIndex;
	if(index == table.rows.length-1)
		return;
	table.rows[index].parentNode.insertBefore(table.rows[index+1], table.rows[index]);
	a.focus();
}
</script>

<form method="post" name="socserv_settings" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=urlencode(LANGUAGE_ID)?>">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<tr><td colspan="2">
<?
$aSiteTabs = array(array("DIV" => "opt_common", "TAB" => GetMessage("socserv_sett_common"), 'TITLE' => GetMessage("socserv_sett_common_title"), 'ONSELECT'=>"document.forms['socserv_settings'].siteTabControl_active_tab.value='opt_common'"));
foreach($arSites as $arSite)
	$aSiteTabs[] = array("DIV" => "opt_site_".$arSite["ID"], "TAB" => '['.$arSite["ID"].'] '.htmlspecialcharsbx($arSite["NAME"]), 'TITLE' => GetMessage("socserv_sett_site").' ['.$arSite["ID"].'] '.htmlspecialcharsbx($arSite["NAME"]), 'ONSELECT'=>"document.forms['socserv_settings'].siteTabControl_active_tab.value='opt_site_".$arSite["ID"]."'");

$siteTabControl = new CAdminViewTabControl("siteTabControl", $aSiteTabs);

$siteTabControl->Begin();

$arUseOnSites = unserialize(COption::GetOptionString("socialservices", "use_on_sites", ""));

foreach($arSiteList as $site):
	$suffix = ($site <> ''? '_bx_site_'.$site:'');
	$siteTabControl->BeginNextTab();
?>
<?if($site <> ''):?>
<table cellpadding="0" cellspacing="0" border="0" class="edit-table">
	<tr>
		<td width="50%" class="field-name"><label for="use_on_sites<?=$suffix?>"><?echo GetMessage("socserv_sett_site_apply")?></td>
		<td width="50%" style="padding-left:7px;">
			<input type="hidden" name="use_on_sites[<?=htmlspecialcharsbx($site)?>]" value="N">
			<input type="checkbox" name="use_on_sites[<?=htmlspecialcharsbx($site)?>]" value="Y"<?if($arUseOnSites[$site] == "Y") echo ' checked'?> id="use_on_sites<?=$suffix?>" onclick="BX('site_settings<?=$suffix?>').style.display=(this.checked? '':'none');">
		</td>
	</tr>
</table>
<?endif?>
<table cellpadding="0" cellspacing="0" border="0" class="edit-table" id="site_settings<?=$suffix?>"<?if($site <> '' && $arUseOnSites[$site] <> "Y") echo ' style="display:none"';?>>
	<tr valign="top">
		<td width="50%" class="field-name"><?echo GetMessage("soc_serv_opt_allow")?></td>
		<td width="50%">
			<table cellpadding="0" style="width:45%;" cellspacing="3" border="0" width="" class="padding-0">
<?
	$arServices = $oAuthManager->GetAuthServices($suffix);
	foreach($arServices as $id=>$service):
?>
				<tr>
					<td style="padding-top: 3px;">
						<input type="hidden" name="AUTH_SERVICES<?=$suffix?>[<?=htmlspecialcharsbx($id)?>]" value="N">
						<input type="checkbox" name="AUTH_SERVICES<?=$suffix?>[<?=htmlspecialcharsbx($id)?>]"
							id="AUTH_SERVICES<?=$suffix?><?=htmlspecialcharsbx($id)?>"
							value="Y"
							<?if($service["__active"] == true) echo " checked"?> 
							<?if($service["DISABLED"] == true) echo " disabled"?>>
					</td>
					<td><div class="bx-ss-icon <?=htmlspecialcharsbx($service["ICON"])?>"></div></td>
					<td><label for="AUTH_SERVICES<?=$suffix?><?=htmlspecialcharsbx($id)?>"><?=htmlspecialcharsbx($service["NAME"])?></label></td>
					<td>&nbsp;</td>
					<td><a href="javascript:void(0)" onclick="MoveRowUp(this)"><img src="/bitrix/images/socialservices/up.gif" width="16" height="16" alt="<?echo GetMessage("soc_serv_opt_up")?>" border="0"></a></td>
					<td><a href="javascript:void(0)" onclick="MoveRowDown(this)"><img src="/bitrix/images/socialservices/down.gif" width="16" height="16" alt="<?echo GetMessage("soc_serv_opt_down")?>" border="0"></a></td>
				</tr>
<?
	endforeach;
?>
			</table>
		</td>
	</tr>
<?
	foreach($arOptions as $option)
	{
		if(!is_array($option))
			$option = GetMessage("soc_serv_opt_settings_of", array("#SERVICE#"=>$option));
		else
			$option[0] .= $suffix;
		__AdmSettingsDrawRow($module_id, $option);
	}
?>
</table>
<?
endforeach; //foreach($arSiteList as $site)

$siteTabControl->End();
?>
</td></tr>
<?$tabControl->Buttons();?>
	<input type="hidden" name="siteTabControl_active_tab" value="<?=htmlspecialcharsbx($_REQUEST["siteTabControl_active_tab"])?>">
<?if($_REQUEST["back_url_settings"] <> ''):?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
<?endif?>
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
<?if($_REQUEST["back_url_settings"] <> ''):?>
	<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
	<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
<?endif?>
	<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" onclick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
