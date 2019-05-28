<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$module_id = "sale";
$CAT_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($CAT_RIGHT >= "R") :

include_once($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
IncludeModuleLangFile(__FILE__);

$rsSite = CSite::GetList($by="sort", $order="asc", $arFilter=array("ACTIVE" => "Y"));
$arSites = array("" => GetMessage("SALE_1C_ALL_SITES"));
while ($arSite = $rsSite->GetNext())
{
	$arSites[$arSite["LID"]] = $arSite["NAME"];
}

$arStatuses = Array("" => GetMessage("SALE_1C_NO"));
$dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID));
while ($arStatus = $dbStatus->Fetch())
{
	$arStatuses[$arStatus["ID"]] = "[".$arStatus["ID"]."] ".$arStatus["NAME"];
}

$arUGroupsEx = Array();
$dbUGroups = CGroup::GetList($by = "c_sort", $order = "asc");
while($arUGroups = $dbUGroups -> Fetch())
{
	$arUGroupsEx[$arUGroups["ID"]] = $arUGroups["NAME"];
}

$arAllOptions = array(
	array("1C_SALE_SITE_LIST", GetMessage("SALE_1C_SITE_LIST"), "", Array("list", $arSites)),
	array("1C_IMPORT_NEW_ORDERS", GetMessage("SALE_1C_IMPORT_NEW_ORDERS"), "N", Array("checkbox")),
	array("1C_SITE_NEW_ORDERS", GetMessage("SALE_1C_SITE_NEW_ORDERS"), "s1", Array("list", $arSites)),
	array("1C_SALE_ACCOUNT_NUMBER_SHOP_PREFIX", GetMessage("SALE_1C_SALE_ACCOUNT_NUMBER_SHOP_PREFIX"), "", Array("text")),
	array("1C_EXPORT_PAYED_ORDERS", GetMessage("SALE_1C_EXPORT_PAYED_ORDERS"), "", Array("checkbox")),
	array("1C_EXPORT_ALLOW_DELIVERY_ORDERS", GetMessage("SALE_1C_EXPORT_ALLOW_DELIVERY_ORDERS"), "", Array("checkbox")),
	array("1C_EXPORT_FINAL_ORDERS", GetMessage("SALE_1C_EXPORT_FINAL_ORDERS"), "", Array("list", $arStatuses)),
	array("1C_FINAL_STATUS_ON_DELIVERY", GetMessage("SALE_1C_FINAL_STATUS_ON_DELIVERY"), "F", Array("list", $arStatuses)),
	array("1C_REPLACE_CURRENCY", GetMessage("SALE_1C_REPLACE_CURRENCY"), GetMessage("SALE_1C_RUB"), Array("text")),
	array("1C_SALE_GROUP_PERMISSIONS", GetMessage("SALE_1C_GROUP_PERMISSIONS"), "1", Array("mlist", 5, $arUGroupsEx)),
	array("1C_SALE_USE_ZIP", GetMessage("SALE_1C_USE_ZIP"), "Y", Array("checkbox")),
	array("1C_INTERVAL", GetMessage("SALE_1C_INTERVAL"), 30, Array("text", 20)),
	array("1C_FILE_SIZE_LIMIT", GetMessage("SALE_1C_FILE_SIZE_LIMIT"), 200*1024, Array("text", 20)),
);

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && $CAT_RIGHT>="W" && check_bitrix_sessid())
{
	$allOptionCount = count($arAllOptions);
	for ($i=0; $i<$allOptionCount; $i++)
	{
		$name = $arAllOptions[$i][0];
		$val = $_REQUEST[$name];
		if($arAllOptions[$i][3][0]=="checkbox" && $val!="Y")
			$val = "N";
		if($arAllOptions[$i][3][0]=="mlist" && is_array($val))
			$val = implode(",", $val);
		COption::SetOptionString("sale", $name, $val, $arAllOptions[$i][1]);
	}
	return;
}

foreach($arAllOptions as $Option):
	$val = COption::GetOptionString("sale", $Option[0], $Option[2]);
	$type = $Option[3];
	?>
	<tr>
		<td width="40%"<?if($type[0]=="mlist") echo " valign=\"top\""?>><?	if($type[0]=="checkbox")
						echo "<label for=\"".htmlspecialcharsbx($Option[0])."\">".$Option[1]."</label>";
					else
						echo $Option[1];?>:</td>
		<td width="60%">
				<?if($type[0]=="checkbox"):?>
					<input type="checkbox" name="<?echo htmlspecialcharsbx($Option[0])?>" id="<?echo htmlspecialcharsbx($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
				<?elseif($type[0]=="text"):?>
					<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($Option[0])?>">
				<?elseif($type[0]=="textarea"):?>
					<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialcharsbx($Option[0])?>"><?echo htmlspecialcharsbx($val)?></textarea>
				<?elseif($type[0]=="list"):?>
					<select name="<?echo htmlspecialcharsbx($Option[0])?>">
					<?foreach($type[1] as $key=>$value):?>
						<option value="<?echo htmlspecialcharsbx($key)?>" <?if($val==$key) echo "selected"?>><?echo htmlspecialcharsbx($value)?></option>
					<?endforeach?>
					</select>
				<?elseif($type[0]=="mlist"):
					$val = explode(",", $val)?>
					<select multiple name="<?echo htmlspecialcharsbx($Option[0])?>[]" size="<?echo $type[1]?>">
					<?foreach($type[2] as $key=>$value):?>
						<option value="<?echo htmlspecialcharsbx($key)?>" <?if(in_array($key, $val)) echo "selected"?>><?echo htmlspecialcharsbx($value)?></option>
					<?endforeach?>
					</select>
				<?endif?>
		</td>
	</tr>
<?endforeach;
endif;
?>