<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$module_mode = CModule::IncludeModuleEx("grain.customsettings");

$arCustomPage = Array();
$arCustomSettings = Array();

$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php", "r");
$settings_data=fread($handle, filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php"));
fclose($handle);

ob_start();
$settings_data_error = eval("?>".$settings_data."<?")===false;
$err = ob_get_contents();
ob_end_clean();

$settings_data_empty = (is_array($arCustomSettings) && count($arCustomSettings)<=0) || !is_array($arCustomSettings);

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php");

IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/prolog.php");

$GKS_RIGHT = $APPLICATION->GetGroupRight("grain.customsettings");

if ($GKS_RIGHT == "D")
  $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$aTabs = Array();

foreach($arCustomSettings as $tab_id => $arTab) {

	$aTabs[] = Array(
		"DIV" => "edit".$tab_id,
		"TAB" => $arTab["LANG"][LANGUAGE_ID]["NAME"],
		"ICON"=>"main_user_edit",
		"TITLE"=>$arTab["LANG"][LANGUAGE_ID]["TITLE"]
	);

}

$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0 && $GKS_RIGHT>="S" && check_bitrix_sessid() && $module_mode!=MODULE_DEMO_EXPIRED)
{
	COption::RemoveOption("grain.customsettings");
	LocalRedirect("/bitrix/admin/gcustomsettings.php?lang=".LANG);
}


if(
	$REQUEST_METHOD == "POST"
	&& ($save!="" || $apply!="")
	&& $GKS_RIGHT>="S"
	&& check_bitrix_sessid()
	&& $module_mode!=MODULE_DEMO_EXPIRED
)
{


	foreach($arCustomSettings as $tab_id => $arTab) {
	
		foreach($arTab["FIELDS"] as $arField) {
		
			COption::SetOptionString(
				"grain.customsettings", 
				$arField["NAME"], 
				${$arField["NAME"]}
			);
		
		}
	
	}

	LocalRedirect("/bitrix/admin/gcustomsettings.php?lang=".LANG."&mess=ok&".$tabControl->ActiveTabParam());

}


$APPLICATION->SetTitle($arCustomPage["LANG"][LANGUAGE_ID]["PAGE_TITLE"]?$arCustomPage["LANG"][LANGUAGE_ID]["PAGE_TITLE"]:GetMessage("GRAIN_CUSTOMSETTINGS_ADMIN_GCUSTOMSETTINGS_TITLE"));

// split data prepare and out
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if($module_mode==MODULE_DEMO)
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/install/trial/trial.php");	
elseif($module_mode==MODULE_DEMO_EXPIRED)
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/install/trial/expired.php");

if($_REQUEST["mess"] == "ok")
  CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("GRAIN_CUSTOMSETTINGS_ADMIN_GCUSTOMSETTINGS_DATA_SAVED"), "TYPE"=>"OK"));

if($settings_data_error)
  CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("GRAIN_CUSTOMSETTINGS_ADMIN_GCUSTOMSETTINGS_DATA_FILE_ERROR"), "TYPE"=>"ERROR"));
elseif($settings_data_empty && $GKS_RIGHT=="W") {

?>
<?echo BeginNote();?>
	<?=GetMessage("GRAIN_CUSTOMSETTINGS_ADMIN_GCUSTOMSETTINGS_NO_SETTINGS_NOTE",Array("#SETTINGS_URL#"=>"/bitrix/admin/settings.php?lang=".LANGUAGE_ID."&mid=grain.customsettings"."&back_url_settings=".urlencode($APPLICATION->GetCurPageParam())))?>
<?echo EndNote();?>
<?

}


if (!$settings_data_error && !$settings_data_empty):

?>
<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?// check session identifier ?>
<?echo bitrix_sessid_post();?>
<?
// Show tab headers  
$tabControl->Begin();
?>
<?

foreach($arCustomSettings as $tab_id => $arTab):
	$tabControl->BeginNextTab();

	foreach($arTab["FIELDS"] as $arField):
		$val = COption::GetOptionString("grain.customsettings", $arField["NAME"]);
	?>
		<tr>
			<td valign="top" width="50%"><?if($arField["TYPE"]=="checkbox")
							echo "<label for=\"".htmlspecialchars($arField["NAME"])."_cchbc\">".$arField["LANG"][LANGUAGE_ID]["NAME"]."</label>";
						else
							echo $arField["LANG"][LANGUAGE_ID]["NAME"];?></td>
			<td valign="top" width="50%">
					<?if($arField["TYPE"]=="checkbox"):?>
						<input type="checkbox" name="<?echo htmlspecialchars($arField["NAME"])?>" id="<?echo htmlspecialchars($arField["NAME"])?>_cchbc" value="Y"<?if($val=="Y")echo" checked";?>>
					<?elseif($arField["TYPE"]=="text"):?>
						<input type="text" maxlength="255" value="<?echo htmlspecialchars($val)?>" <?if($arField["SIZE"]):?>size="<?=$arField["SIZE"]?>" <?endif?>name="<?echo htmlspecialchars($arField["NAME"])?>">
					<?elseif($arField["TYPE"]=="date"):?>
						<input type="text" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arField["NAME"])?>"> <?=Calendar(htmlspecialchars($arField["NAME"]),"post_form")?>
					<?elseif($arField["TYPE"]=="textarea"):?>
						<textarea name="<?echo htmlspecialchars($arField["NAME"])?>"<?if($arField["COLS"]):?> cols="<?=$arField["COLS"]?>"<?endif?><?if($arField["ROWS"]):?> rows="<?=$arField["ROWS"]?>"<?endif?>><?echo htmlspecialchars($val)?></textarea>
					<?elseif($arField["TYPE"]=="select"):?>
						<select  name="<?echo htmlspecialchars($arField["NAME"])?>">
							<?foreach($arField["VALUES"] as $v):?>
								<option value="<?=$v["VALUE"]?>" <? if($val==$v["VALUE"]) echo 'selected';?>><?=$v["LANG"][LANGUAGE_ID]?></option>
							<?endforeach?>
						</select>
					<?elseif($arField["TYPE"]=="link"):?>
						<?
						$arParameters = $arField["LINK"];
					
						$arParameters["INPUT_NAME"] = $arField["NAME"];
						$arParameters["USE_SEARCH"] = in_array($arField["INTERFACE"],Array("search","selectsearch"))?"Y":"N";
						$arParameters["USE_SEARCH_COUNT"] = "";
						$arParameters["EMPTY_SHOW_ALL"] = in_array($arField["INTERFACE"],Array("select","selectsearch"))?"Y":"N";
						$arParameters["NAME_TRUNCATE_LEN"] = "";
						$arParameters["USE_AJAX"] = $arField["INTERFACE"]=="ajax"?"Y":"N";
						$arParameters["VALUE"] = $val;
						$arParameters["MULTIPLE"] = "N";
						$arParameters["ADMIN_SECTION"] = "Y";
						$arParameters["LEAVE_EMPTY_INPUTS"] = "N";
						$arParameters["USE_VALUE_ID"] = "N";
								
						$GLOBALS["APPLICATION"]->IncludeComponent(
							"grain:links.edit",
							"",
							$arParameters,
							null,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					<?endif?>
					
					<?if($arField["LANG"][LANGUAGE_ID]["TOOLTIP"]):?>
						<?echo BeginNote();?>
							<?echo $arField["LANG"][LANGUAGE_ID]["TOOLTIP"];?>
						<?echo EndNote();?>
					<?endif?>
					
			</td>
		</tr>
	<?endforeach;
endforeach;?>


<?
// show buttons 
$tabControl->Buttons();
?>
<script language="JavaScript">
function RestoreDefaults()
{
	if (confirm('<?echo AddSlashes(GetMessage("GRAIN_CUSTOMSETTINGS_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&<?echo bitrix_sessid_get()?>";
}
</script>

<input type="submit" <?if ($GKS_RIGHT<"S" || $module_mode==MODULE_DEMO_EXPIRED) echo "disabled" ?> name="save" value="<?echo GetMessage("MAIN_SAVE")?>">
&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" <?if ($GKS_RIGHT<"S" || $module_mode==MODULE_DEMO_EXPIRED):?>disabled<?else:?>onClick="RestoreDefaults();"<?endif?> value="<?echo GetMessage("GRAIN_CUSTOMSETTINGS_RESTORE_DEFAULTS")?>" />
<input type="hidden" name="lang" value="<?=LANG?>">
<?
// end tab interface
$tabControl->End();

if($GKS_RIGHT=="W") {
	echo BeginNote();
	?>
		<?=GetMessage("GRAIN_CUSTOMSETTINGS_ADMIN_GCUSTOMSETTINGS_MODULE_SETTINGS_NOTE",Array("#SETTINGS_URL#"=>"/bitrix/admin/settings.php?lang=".LANGUAGE_ID."&mid=grain.customsettings"."&back_url_settings=".urlencode($APPLICATION->GetCurPageParam())))?>
	<?
	echo EndNote();
}

 
endif;
 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>