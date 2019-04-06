<?
if(!$USER->IsAdmin()) return;

$ModuleID = 'webdebug.popup';

IncludeModuleLangFile(__FILE__);

$arAllOptions = Array(
	Array("webdebug_popup_always_init", GetMessage("WEBDEBUG_POPUP_ALWAYS_INIT"), "", Array("checkbox")),
);
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("WEBDEBUG_POPUP_TAB_1"), "ICON" => "webdebug_popup_params", "TITLE" => GetMessage("WEBDEBUG_POPUP_TAB_1_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply)>0 && check_bitrix_sessid()) {
	foreach($arAllOptions as $arOption) {
		$name=$arOption[0];
		$val=$_REQUEST[$name];
		if($arOption[2][0]=="checkbox" && $val!="Y")
			$val="N";
		COption::SetOptionString($ModuleID, $name, $val, $arOption[1]);
	}
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}

$tabControl->Begin();
CModule::IncludeModule($ModuleID);
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
	<?$tabControl->BeginNextTab();?>
		<?foreach($arAllOptions as $arOption):
			$val = COption::GetOptionString($ModuleID, $arOption[0], $arOption[2]);
			$OptionValues = $arOption[4];
			$type = $arOption[3];
		?>
		<tr>
			<td valign="top" width="50%"><?
				if($type[0]=="checkbox")
					echo "<label for=\"".htmlspecialchars($arOption[0])."\">".$arOption[1]."</label>";
				else
					echo $arOption[1];?>:</td>
			<td valign="top" width="50%">
				<?if($type[0]=="checkbox"):?>
					<input type="checkbox" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="Y"<?if($val=="Y")echo" checked='checked'";?> />
				<?endif?>
			</td>
		</tr>
		<?endforeach?>
	<?$tabControl->Buttons();?>
		<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
		<input type="submit" name="Apply" value="<?=GetMessage("MAIN_APPLY")?>">
		<?if(strlen($_REQUEST["back_url_settings"])>0):?>
			<input type="button" name="Cancel" value="<?=GetMessage("MAIN_CANCEL")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
			<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
		<?endif?>
		<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
</form>