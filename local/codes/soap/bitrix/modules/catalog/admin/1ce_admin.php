<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$module_id = "catalog";

if ($USER->CanDoOperation('catalog_read')) :

include_once($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	$strError = $ex->GetString();
	ShowError($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);

if(CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")):

	$arIBlock = array();
	$rsCatalog = CCatalog::GetList(Array("sort" => "asc"));
	while($arr = $rsCatalog->Fetch())
	{
		if(!$arr["PRODUCT_IBLOCK_ID"])
			$arIBlock[$arr["IBLOCK_ID"]] = "[".$arr["IBLOCK_ID"]."] ".CIBlock::GetArrayByID($arr["IBLOCK_ID"], "NAME");
	}

	$arUGroupsEx = Array();
	$dbUGroups = CGroup::GetList($by = "c_sort", $order = "asc");
	while($arUGroups = $dbUGroups -> Fetch())
	{
		$arUGroupsEx[$arUGroups["ID"]] = $arUGroups["NAME"];
	}

	$arAllOptions = array(
		array("1CE_IBLOCK_ID", GetMessage("CAT_1CE_IBLOCK_ID"), "", Array("list", $arIBlock)),
		array("1CE_ELEMENTS_PER_STEP", GetMessage("CAT_1CE_ELEMENTS_PER_STEP"), 1, Array("text", 5)),
		array("1CE_INTERVAL", GetMessage("CAT_1CE_INTERVAL"), "30", Array("text", 20)),
		array("1CE_GROUP_PERMISSIONS", GetMessage("CAT_1CE_GROUP_PERMISSIONS"), "-", Array("mlist", 5, $arUGroupsEx)),
		array("1CE_USE_ZIP", GetMessage("CAT_1CE_USE_ZIP"), "Y", Array("checkbox")),
	);

	if($REQUEST_METHOD=="POST" && strlen($Update)>0 && $USER->CanDoOperation('edit_php'))
	{
		for ($i=0; $i<count($arAllOptions); $i++)
		{
			$name = $arAllOptions[$i][0];
			$val = $_REQUEST[$name];
			if($arAllOptions[$i][3][0]=="checkbox" && $val!="Y")
				$val = "N";
			if($arAllOptions[$i][3][0]=="mlist")
				$val = implode(",", $val);
			COption::SetOptionString("catalog", $name, $val, $arAllOptions[$i][1]);
		}
		return;
	}

	foreach($arAllOptions as $Option):
		$val = COption::GetOptionString("catalog", $Option[0], $Option[2]);
		$type = $Option[3];
		?>
		<tr>
			<td <? echo ('textarea' == $type[0] || 'mlist' == $type[0] ? 'valign="top"' : ''); ?> width="40%"><?	if($type[0]=="checkbox")
							echo "<label for=\"".htmlspecialcharsbx($Option[0])."\">".$Option[1]."</label>";
						else
							echo $Option[1];?>:</td>
			<td width="60%">
					<?if($type[0]=="checkbox"):?>
						<input type="checkbox" name="<?echo htmlspecialcharsbx($Option[0])?>" id="<?echo htmlspecialcharsbx($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?> onclick="Check(this.id);">
					<?elseif($type[0]=="text"):?>
						<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($Option[0])?>" id="<?echo htmlspecialcharsbx($Option[0])?>">
					<?elseif($type[0]=="textarea"):?>
						<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialcharsbx($Option[0])?>" id="<?echo htmlspecialcharsbx($Option[0])?>"><?echo htmlspecialcharsbx($val)?></textarea>
					<?elseif($type[0]=="list"):?>
						<select name="<?echo htmlspecialcharsbx($Option[0])?>" id="<?echo htmlspecialcharsbx($Option[0])?>">
						<?foreach($type[1] as $key=>$value):?>
							<option value="<?echo htmlspecialcharsbx($key)?>" <?if($val==$key) echo "selected"?>><?echo htmlspecialcharsbx($value)?></option>
						<?endforeach?>
						</select>
					<?elseif($type[0]=="mlist"):
						$val = explode(",", $val)?>
						<select multiple name="<?echo htmlspecialcharsbx($Option[0])?>[]" size="<?echo $type[1]?>" id="<?echo htmlspecialcharsbx($Option[0])?>">
						<?foreach($type[2] as $key=>$value):?>
							<option value="<?echo htmlspecialcharsbx($key)?>" <?if(in_array($key, $val)) echo "selected"?>><?echo htmlspecialcharsbx($value)?></option>
						<?endforeach?>
						</select>
					<?endif?>
			</td>
		</tr>
	<?endforeach;
else:
	CAdminMessage::ShowMessage(GetMessage("CAT_NO_IBLOCK_MOD"));
endif;

endif;
?>