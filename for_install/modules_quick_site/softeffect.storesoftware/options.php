<?
if(!$USER->IsAdmin())
	return;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "softeffect.storesoftware";
$PDFIT_RIGHT = $APPLICATION->GetGroupRight($module_id);

### <get info> ###
CModule::IncludeModule("sale");

$arPersons=array();
$dbPerson = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"));
while($arPerson = $dbPerson->GetNext()) {
	$arPersons[$arPerson["ID"]] = $arPerson["NAME"];
}

$db_ptype = CSalePaySystem::GetList($arOrder = Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), false, false, false, array('*'));
while ($ptype = $db_ptype->Fetch()){
	$arPersType = CSalePersonType::GetByID($ptype['PSA_PERSON_TYPE_ID']);
	$groups[$ptype['ID']] = '[' . $ptype['LID'] . '] ' . $ptype['NAME'] . ' (' . $arPersType['NAME'] . ')';
}
### </get info> ###

$arAllOptions = Array(
	array("shopFacebook", GetMessage("LINK_FACEBOOK"), "", Array("text", "")),
	array("shopTwitter", GetMessage("LINK_TWITTER"), "", Array("text", "")),
	array("shopVK", GetMessage("LINK_VK"), "", Array("text", "")),
);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

function ShowParamsHTMLByArray($arParams) {
	foreach($arParams as $Option) 	{
	 	__AdmSettingsDrawRow("softeffect.storesoftware", $Option);
	}
}

if ($REQUEST_METHOD=="POST" && $PDFIT_RIGHT=="W" && check_bitrix_sessid() && $_REQUEST["SUpdate"])
{
	foreach($arAllOptions as $option) {
		__AdmSettingsSaveOption("softeffect.storesoftware", $option);
	}

}

$tabControl->Begin();
?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($module_id)?>&lang=<?=LANGUAGE_ID?>"><?

$tabControl->BeginNextTab();//set common
ShowParamsHTMLByArray($arAllOptions);

$tabControl->Buttons();?>
<input type="submit" <?if ($PDFIT_RIGHT<"W") echo "disabled" ?> name="SUpdate" value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
<?=bitrix_sessid_post();?>

<? $tabControl->End(); ?>
</form>