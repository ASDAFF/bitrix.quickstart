<?
if(!$USER->IsAdmin()) return;

$module_id = 'webdebug.reviews';
CModule::IncludeModule("webdebug.reviews");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php"); 
IncludeModuleLangFile(__FILE__);

$arAllOptions = Array(
	array("show_version_1", GetMessage("WD_REVIEWS2_SHOW_VERSION_1"), false, array("select"), array('Y'=>GetMessage('WD_REVIEWS2_Y'),'N'=>GetMessage('WD_REVIEWS2_N'))),
	array("use_epilog_for_scripts_include", GetMessage("WD_REVIEWS2_USE_EPILOG_FOR_SCRIPTS_INCLUDE"), false, array("checkbox")),
	array("skip_sessid_check", GetMessage("WD_REVIEWS2_SKIP_SESSID_CHECK"), false, array("checkbox")),
	array("show_target_links", GetMessage("WD_REVIEWS2_SHOW_TARGET_LINKS"), false, array("checkbox")),
	array("allow_unreg_voting", GetMessage("WD_REVIEWS2_ALLOW_UNREG_VOTING"), false, array("checkbox")),
);

function WebdebugReviewsWriteVotes() {
	$arOptions = array();
	for ($i=0; $i<10; $i++) {
		$arOptions[$i] = COption::GetOptionString("webdebug.reviews", "vote_name_".$i);
	}
	?>
	<tr>
		<td>
			<table width="100%">
				<tbody>
					<?for ($i=0; $i<10; $i++):?>
						<tr>
							<td class="field-name"><?=GetMessage("WD_REVIEWS2_VOTE_FIELD_NAME")?><?=$i?>:</td>
							<td class="field-value"><input type="text" size="40" name="webdebug_votes[<?=$i?>]" value="<?=COption::GetOptionString("webdebug.reviews", "vote_name_".$i)?>" /></td>
						</tr>
					<?endfor?>
				</tbody>
			</table>
		</td>
	</td>
	<?
}

$aTabs = array();
$aTabs[] = array("DIV" => "tab_version", "TAB" => GetMessage("WD_REVIEWS2_TAB_0_NAME"), "TITLE" => GetMessage("WD_REVIEWS2_TAB_0_DESC"));
$aTabs[] = array("DIV" => "tab_vote_names", "TAB" => GetMessage("WD_REVIEWS2_TAB_1_NAME"), "TITLE" => GetMessage("WD_REVIEWS2_TAB_1_DESC"));
$aTabs[] = array("DIV" => "tab_rights", "TAB" => GetMessage("WD_REVIEWS2_TAB_2_NAME"), "TITLE" => GetMessage("WD_REVIEWS2_TAB_2_DESC"));
$tabControl = new CAdminTabControl("tabControl", $aTabs);
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) {
	if(strlen($RestoreDefaults)>0) {
		$arGroups = array();
		$resGroups = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($arGroup = $resGroups->GetNext(false,false)) {
			$arGroups[] = $arGroup["ID"];
		}
		$APPLICATION->DelGroupRight($module_id, $arGroups);
		COption::RemoveOption("webdebug.reviews");
		LocalRedirect($_SERVER["REQUEST_URI"]);
	} else {
		for ($i=0; $i<10; $i++) {
			COption::SetOptionString("webdebug.reviews", "vote_name_".$i, $_REQUEST["webdebug_votes"][$i], GetMessage("WD_REVIEWS2_VOTE_FIELD_NAME").$i);
		}
		foreach($arAllOptions as $arOption) {
			$name=$arOption[0];
			$val=$_REQUEST[$name];
			if($arOption[3][0]=="checkbox" && $val!="Y") $val="N";
			COption::SetOptionString($module_id, $name, $val, $arOption[1]);
		}
	}
}
?>

<?if(CModule::IncludeModule("webdebug.reviews")):?>
	<?$tabControl->Begin();?>
	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>" id="webdebug-reviews-table">
		<?$tabControl->BeginNextTab();?>
		<?foreach($arAllOptions as $arOption):?>
			<?
				$val = COption::GetOptionString($module_id, $arOption[0]);
				$OptionValues = $arOption[4];
				$type = $arOption[3];
			?>
			<tr>
				<td width="50%"><?
					if($type[0]=="checkbox")
						echo "<label for=\"".htmlspecialchars($arOption[0])."\">".$arOption[1]."</label>";
					else
						echo $arOption[1];?>:</td>
				<td width="50%">
					<?if($type[0]=="checkbox"):?>
						<input type="checkbox" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="Y"<?if($val=="Y")echo" checked='checked'";?> />
					<?elseif($type[0]=="text"):?>
						<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arOption[0])?>" />
					<?elseif($type[0]=="select"):?>
						<select name="<?echo htmlspecialchars($arOption[0])?>">
							<?foreach ($OptionValues as $OptionValue => $OptionName):?>
								<option value="<?=$OptionValue?>"<?if($OptionValue==$val)echo" selected='selected'";?>><?=$OptionName?></option>
							<?endforeach?>
						</select>
					<?endif?>
					<?if($arOption[0]=='log_filename'):?>
						<br/>
						<a href="<?=$val?>" target="_blank"><?=sprintf(GetMessage('WEBDEBUG_EXCEL_LOG_FILENAME_OPEN'),$val)?></a>
					<?endif?>
				</td>
			</tr>
		<?endforeach?>
		<?$tabControl->BeginNextTab();?>
			<?=WebdebugReviewsWriteVotes($arSite);?>
		<?$tabControl->BeginNextTab();?>
			<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
		<?$tabControl->Buttons();?>
			<input type="submit" name="Update" value="<?=GetMessage("WD_REVIEWS2_BUTTON_UPDATE_VALUE")?>">
			<input type="hidden" name="Update" value="Y">
			<input type="submit" name="Apply" value="<?=GetMessage("WD_REVIEWS2_BUTTON_APPLY_VALUE")?>">
			<input type="submit" name="RestoreDefaults" value="<?=GetMessage("MAIN_RESET")?>" onclick="return confirm('<?=AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>');">
			<?if(strlen($_REQUEST["back_url_settings"])>0):?>
				<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
			<?endif?>
			<?=bitrix_sessid_post();?>
		<?$tabControl->End();?>
	</form>
<?else:?>
	<p><?=GetMessage("WD_REVIEWS2_ERROR_MODULE_NOT_INCLUDED")?></p>
<?endif?>

<?
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) {
	if (strlen($Apply)>0) {
		LocalRedirect($_SERVER["REQUEST_URI"]."&".$tabControl->ActiveTabParam());
	} elseif (strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0) {
		LocalRedirect($_REQUEST["back_url_settings"]);
	} else {
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
	}
}
?>