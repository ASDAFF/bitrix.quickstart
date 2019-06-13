<?
$module_id = "parnas.khayrcomment";
CModule::IncludeModule("iblock");
CModule::IncludeModule($module_id);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$CAT_RIGHT = KhayRComment::GetRightsMax();
if ($CAT_RIGHT < "X")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$arOptions = array(
	"IBLOCK" => array(
		"CODE" => "IBLOCK",
		"TEXT" => GetMessage("KHAYR_COMMENT_OPTION_IBLOCK"),
		"DESCRIPTION" => "",
		"TYPE" => "IBLOCKS"
	),
	"ON_ADD_EMAIL" => array(
		"CODE" => "ON_ADD_EMAIL",
		"TEXT" => GetMessage("KHAYR_COMMENT_OPTION_ON_ADD_EMAIL"),
		"DESCRIPTION" => "",
		"TYPE" => "CHECKBOX"
	),
	"EMAIL" => array(
		"CODE" => "EMAIL",
		"TEXT" => GetMessage("KHAYR_COMMENT_OPTION_EMAIL"),
		"DESCRIPTION" => GetMessage("KHAYR_COMMENT_OPTION_EMAIL_DESCRIPTION"),
		"TYPE" => "TEXT"
	),
);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$arSites = array();
$arSiteList = array('');
$dbSites = CSite::GetList($b = "sort", $o = "asc", array("ACTIVE" => "Y"));
while ($arSite = $dbSites->Fetch())
{
	$arSites[$arSite['ID']] = $arSite;
	$arSiteList[] = $arSite['ID'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["Update"].$_POST["Apply"].$_POST["RestoreDefaults"] != '' && check_bitrix_sessid())
{
	if ($_POST["RestoreDefaults"] != '')
	{
		COption::RemoveOption($module_id);
	}
	else
	{
		foreach ($arSiteList as $site)
		{
			$suffix = ($site != '' ? '_'.$site : '');
			
			if ($site != '')
			{
				COption::SetOptionString($module_id, "use_on_sites_".$site, htmlspecialcharsbx(trim($_POST["OPTION_use_on_sites_".$site])));
			}
			
			foreach ($arOptions as $option)
			{
				$val_new = htmlspecialcharsbx(trim($_POST["OPTION_".$option["CODE"].$suffix]));
				if ($option["CODE"] == "IBLOCK")
				{
					$val = COption::GetOptionString($module_id, $option["CODE"].$suffix, "");
					//if ($val != $val_new)
					//{
						if ($val_new > 0)
						{
							KhayRComment::CheckIBlock($val_new);
						}
						elseif ($val_new == -1)
						{
							$val_new = KhayRComment::CreateIBlock();
						}
					//}
				}
				COption::SetOptionString($module_id, $option["CODE"].$suffix, $val_new);
			}
		}
	}
	
	if (strlen($_REQUEST["back_url_settings"]) > 0)
	{
		if ($_POST["Apply"] != '' || $_POST["RestoreDefaults"] != '')
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam().($_REQUEST["siteTabControl_active_tab"] != '' ? "&siteTabControl_active_tab=".urlencode($_REQUEST["siteTabControl_active_tab"]) : ''));
		else
			LocalRedirect($_REQUEST["back_url_settings"]);
	}
	else
	{
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam().($_REQUEST["siteTabControl_active_tab"] != '' ? "&siteTabControl_active_tab=".urlencode($_REQUEST["siteTabControl_active_tab"]) : ''));
	}
}

$aSiteTabs = array(
	array("DIV" => "opt_common", "TAB" => GetMessage("KHAYR_COMMENT_OPTION_COMMON"), 'TITLE' => GetMessage("KHAYR_COMMENT_OPTION_COMMON_TITLE"), 'ONSELECT'=>"document.forms['".$module_id."_settings'].siteTabControl_active_tab.value='opt_common'")
);

$arUseOnSites = array();

foreach ($arSites as $arSite)
{
	$arUseOnSites[$arSite["ID"]] = COption::GetOptionString($module_id, "use_on_sites_".$arSite["ID"], "");
	
	$aSiteTabs[] = array("DIV" => "opt_site_".$arSite["ID"], "TAB" => '['.$arSite["ID"].'] '.htmlspecialcharsbx($arSite["NAME"]), 'TITLE' => GetMessage("KHAYR_COMMENT_OPTION_SITE_TITLE").' ['.$arSite["ID"].'] '.htmlspecialcharsbx($arSite["NAME"]), 'ONSELECT'=>"document.forms['".$module_id."_settings'].siteTabControl_active_tab.value='opt_site_".$arSite["ID"]."'");
}

$iblocks = array();
$db_iblock_type = CIBlockType::GetList(Array("NAME" => "ASC"), Array());
while ($ar_iblock_type = $db_iblock_type->Fetch())
{
	if ($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANGUAGE_ID))
	{
		if (!array_key_exists($ar_iblock_type["ID"], $iblocks))
		{
			$iblocks[$ar_iblock_type["ID"]] = array("TYPE" => $arIBType, "IBLOCKS" => array());
		}
	}
}
$res = CIBlock::GetList(Array("NAME" => "ASC"), Array('ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N'));
while ($ar_res = $res->Fetch())
{
	$iblocks[$ar_res["IBLOCK_TYPE_ID"]]["IBLOCKS"][] = $ar_res;
}
?>
<form method="post" name="<?=$module_id?>_settings" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&lang=<?=urlencode(LANGUAGE_ID)?>">
	<?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	?>
	<tr>
		<td colspan="2">
			<?
			$siteTabControl = new CAdminViewTabControl("siteTabControl", $aSiteTabs);
			$siteTabControl->Begin();
			foreach ($arSiteList as $site)
			{
				$suffix = ($site != '' ? '_'.$site : '');
				$siteTabControl->BeginNextTab();
				if ($site != '')
				{
					?>
					<table cellpadding="0" cellspacing="0" border="0" class="edit-table" width="100%">
						<tr>
							<td width="50%" class="field-name">
								<label for="OPTION_use_on_sites<?=$suffix?>"><?=GetMessage("KHAYR_COMMENT_OPTION_SITE_APPLY")?>:</label>
							</td>
							<td width="50%" style="padding-left: 7px;">
								<input type="checkbox" id="OPTION_use_on_sites<?=$suffix?>" name="OPTION_use_on_sites<?=$suffix?>" value="Y" <?=($arUseOnSites[$site] == "Y" ? 'checked' : '')?> onclick="BX('site_settings<?=$suffix?>').style.display=(this.checked ? '' : 'none');" />
							</td>
						</tr>
					</table>
					<?
				}
				?>
				<table cellpadding="0" cellspacing="0" border="0" class="edit-table" width="100%" id="site_settings<?=$suffix?>" <?=($site != '' && $arUseOnSites[$site] != "Y" ? 'style="display: none;"' : '')?>>
					<?
					foreach ($arOptions as $option)
					{
						if ($option["TYPE"] == "TEXT")
						{
							?>
							<tr>
								<td width="50%" class="field-name">
									<?=$option["TEXT"]?>:
								</td>
								<td width="50%" style="padding-left: 7px;">
									<input type="text" id="OPTION_<?=$option["CODE"].$suffix?>" name="OPTION_<?=$option["CODE"].$suffix?>" value="<?=COption::GetOptionString($module_id, $option["CODE"].$suffix, "")?>" />
								</td>
							</tr>
							<?
						}
						elseif ($option["TYPE"] == "CHECKBOX")
						{
							?>
							<tr>
								<td width="50%" class="field-name">
									<label for="OPTION_<?=$option["CODE"].$suffix?>"><?=$option["TEXT"]?>:</label>
								</td>
								<td width="50%" style="padding-left: 7px;">
									<input type="checkbox" id="OPTION_<?=$option["CODE"].$suffix?>" name="OPTION_<?=$option["CODE"].$suffix?>" value="Y" <?=(COption::GetOptionString($module_id, $option["CODE"].$suffix, "") == "Y" ? "checked" : "")?> />
								</td>
							</tr>
							<?
						}
						elseif ($option["TYPE"] == "IBLOCKS")
						{
							?>
							<tr>
								<td width="50%" class="field-name">
									<?=$option["TEXT"]?>:
								</td>
								<td width="50%" style="padding-left: 7px;">
									<?$val = COption::GetOptionString($module_id, $option["CODE"].$suffix, "");?>
									<select id="OPTION_<?=$option["CODE"].$suffix?>" name="OPTION_<?=$option["CODE"].$suffix?>">
										<option value="" <?=($val == "" ? "selected" : "")?>><?=GetMessage("KHAYR_COMMENT_OPTION_IBLOCK_SELECT")?></option>
										<option value="-1"><?=GetMessage("KHAYR_COMMENT_OPTION_IBLOCK_CREATE")?></option>
										<?
										foreach ($iblocks as $iblock)
										{
											?><optgroup label="<?=$iblock["TYPE"]["NAME"]?>"><?
											foreach ($iblock["IBLOCKS"] as $ib)
											{
												?><option value="<?=$ib["ID"]?>" <?=($val == $ib["ID"] ? "selected" : "")?>><?=$ib["NAME"]?></option><?
											}
											?></optgroup><?
										}
										?>
									</select>
								</td>
							</tr>
							<?
						}
					}
					?>
				</table>
				<?
			}
			$siteTabControl->End();
			?>
		</td>
	</tr>
	<?$tabControl->Buttons();?>
	<input type="hidden" name="siteTabControl_active_tab" value="<?=htmlspecialcharsbx($_REQUEST["siteTabControl_active_tab"])?>" />
	<?if ($_REQUEST["back_url_settings"] != '') {?>
		<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" />
	<?}?>
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>" />
	<?if ($_REQUEST["back_url_settings"] != '') {?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?=htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'" />
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>" />
	<?}?>
	<input type="submit" name="RestoreDefaults" title="<?=GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" onclick="return confirm('<?=AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?=GetMessage("MAIN_RESTORE_DEFAULTS")?>" />
	<?=bitrix_sessid_post()?>
	<?$tabControl->End();?>
</form>