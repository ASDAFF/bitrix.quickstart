<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(!CModule::IncludeModule("iblock"))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	ShowError(GetMessage("CAT_CEDIT_IBLOCK_MODULE_IS_MISSING"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}

if(!CModule::IncludeModule("catalog"))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	ShowError(GetMessage("CAT_CEDIT_CATALOG_MODULE_IS_MISSING"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}

IncludeModuleLangFile(__FILE__);

$strWarning = "";
$bVarsFromForm = false;
$IBLOCK_ID = intval($IBLOCK_ID);

$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
if($arIBlock)
	$bBadBlock = !CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_edit");
else
	$bBadBlock = true;

if($bBadBlock)
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	ShowError(GetMessage("CAT_CEDIT_BAD_IBLOCK"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}

$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => GetMessage("CAT_CEDIT_MAIN_TAB"),
		"ICON" => "iblock_section",
		"TITLE" => GetMessage("CAT_CEDIT_MAIN_TAB_TITLE"),
	),
	array(
		"DIV" => "edit3",
		"TAB" => GetMessage("CAT_CEDIT_PROPERTY_TAB"),
		"ICON" => "iblock_section",
		"TITLE" => GetMessage("CAT_CEDIT_PROPERTY_TAB_TITLE"),
	),
);

$tabControl = new CAdminForm("form_catalog_edit_".$IBLOCK_ID, $aTabs);

if($REQUEST_METHOD=="POST" && (strlen($save)>0 || strlen($apply)>0) && check_bitrix_sessid())
{

	if($_POST["NAME"] != $arIBlock["NAME"])
	{
		$ib = new CIBlock;
		$res = $ib->Update($IBLOCK_ID, array(
			"NAME" => $_POST["NAME"],
		));
		if(!$res)
		{
			$strWarning .= $ib->LAST_ERROR."<br>";
			$bVarsFromForm = true;
		}
	}

	if($arIBlock["SECTION_PROPERTY"] != "Y")
	{
		$ib = new CIBlock;
		$res = $ib->Update($IBLOCK_ID, array(
			"SECTION_PROPERTY" => "Y",
		));
		if(!$res)
		{
			$strWarning .= $ib->LAST_ERROR."<br>";
			$bVarsFromForm = true;
		}
	}

	$arCatalog = false;
	if (CModule::IncludeModule("catalog"))
		$arCatalog = CCatalog::GetSkuInfoByProductID($IBLOCK_ID);

	if(is_array($arCatalog) && CIBlock::GetArrayByID($arCatalog["IBLOCK_ID"], "SECTION_PROPERTY") != "Y")
	{
		$ib = new CIBlock;
		$res = $ib->Update($arCatalog["IBLOCK_ID"], array(
			"SECTION_PROPERTY" => "Y",
		));
		if(!$res)
		{
			$strWarning .= $ib->LAST_ERROR."<br>";
			$bVarsFromForm = true;
		}
	}

	if($strWarning === "")
	{
		$props = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID, "CHECK_PERMISSIONS" => "N"));
		while($p = $props->Fetch())
		{
			CIBlockSectionPropertyLink::Delete(0, $p["ID"]);
			if(
				isset($_POST["SECTION_PROPERTY"])
				&& is_array($_POST["SECTION_PROPERTY"])
				&& array_key_exists($p["ID"], $_POST["SECTION_PROPERTY"])
				&& $_POST["SECTION_PROPERTY"][$p["ID"]]["SHOW"] === "Y"
			)
				CIBlockSectionPropertyLink::Add(0, $p["ID"], array(
					"SMART_FILTER" => $_POST["SECTION_PROPERTY"][$p["ID"]]["SMART_FILTER"],
				));
		}

		if (is_array($arCatalog))
		{
			$props = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arCatalog["IBLOCK_ID"], "CHECK_PERMISSIONS" => "N"));
			while($p = $props->Fetch())
			{
				CIBlockSectionPropertyLink::Delete(0, $p["ID"]);
				if(
					isset($_POST["SECTION_PROPERTY"])
					&& is_array($_POST["SECTION_PROPERTY"])
					&& array_key_exists($p["ID"], $_POST["SECTION_PROPERTY"])
					&& $_POST["SECTION_PROPERTY"][$p["ID"]]["SHOW"] === "Y"
				)
				{
					CIBlockSectionPropertyLink::Add(0, $p["ID"], array(
						"SMART_FILTER" => $_POST["SECTION_PROPERTY"][$p["ID"]]["SMART_FILTER"],
						"IBLOCK_ID" => $IBLOCK_ID,
					));
				}
			}
		}

		LocalRedirect("/bitrix/admin/cat_catalog_edit.php?lang=".LANGUAGE_ID."&IBLOCK_ID=".$IBLOCK_ID."&".$tabControl->ActiveTabParam());
	}
}

ClearVars("str_");
if($bVarsFromForm)
	$str_NAME = $_POST["NAME"];
else
	$str_NAME = $arIBlock["NAME"];

$APPLICATION->SetTitle(GetMessage("CAT_CEDIT_EDIT_TITLE", array("#IBLOCK_NAME#"=>$arIBlock["NAME"])));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if($strWarning)
	CAdminMessage::ShowOldStyleError($strWarning."<br>");

$tabControl->BeginEpilogContent();
echo bitrix_sessid_post();
$tabControl->EndEpilogContent();

$tabControl->Begin(array(
	"FORM_ACTION" => "/bitrix/admin/cat_catalog_edit.php?lang=".LANGUAGE_ID."&IBLOCK_ID=".$IBLOCK_ID,
));

$tabControl->BeginNextFormTab();
$tabControl->AddEditField("NAME", GetMessage("IBLOCK_FIELD_NAME").":", true, array("size" => 50, "maxlength" => 255), $str_NAME);


$tabControl->BeginNextFormTab();
$tabControl->BeginCustomField("SECTION_PROPERTY", GetMessage("CAT_CEDIT_SECTION_PROPERTY_FIELD"));
	?>
		<tr colspan="2"><td align="center">
			<table class="internal" id="table_SECTION_PROPERTY">
			<tr class="heading">
				<td><?echo GetMessage("CAT_CEDIT_PROP_TABLE_NAME");?></td>
				<td><?echo GetMessage("CAT_CEDIT_PROP_TABLE_TYPE");?></td>
				<td><?echo GetMessage("CAT_CEDIT_PROP_TABLE_SMART_FILTER");?></td>
				<td><?echo GetMessage("CAT_CEDIT_PROP_TABLE_ACTION");?></td></tr>
			<?
			if(CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_edit"))
				$arShadow = $arHidden = array(
					-1 => GetMessage("CAT_CEDIT_PROP_SELECT_CHOOSE"),
					0 => GetMessage("CAT_CEDIT_PROP_SELECT_CREATE"),
				);
			else
				$arShadow = $arHidden = array(
					-1 => GetMessage("CAT_CEDIT_PROP_SELECT_CHOOSE"),
				);

			$arPropLinks = CIBlockSectionPropertyLink::GetArray($arIBlock["ID"], 0);
			$rsProps =  CIBlockProperty::GetList(array("SORT"=>"ASC",'ID' => 'ASC'), array("IBLOCK_ID" => $arIBlock["ID"], "CHECK_PERMISSIONS" => "N", "ACTIVE"=>"Y"));
			$rows = 0;
			while ($arProp = $rsProps->Fetch()):
				if(array_key_exists($arProp["ID"], $arPropLinks))
				{
					$rows++;
					$arLink = $arPropLinks[$arProp["ID"]];
					if($arLink["INHERITED"] == "N")
						$arShadow[$arProp["ID"]] = $arProp["NAME"];
				}
				else
				{
					$arLink = false;
					$arHidden[$arProp["ID"]] = $arProp["NAME"];
					$arShadow[$arProp["ID"]] = $arProp["NAME"];
				}
			?>
			<tr id="tr_SECTION_PROPERTY_<?echo $arProp["ID"]?>" <?if(!is_array($arLink)) echo 'style="display:none"';?>>
				<td align="left">
					<?if(!is_array($arLink) || $arLink["INHERITED"] == "N"):?>
					<input type="hidden" name="SECTION_PROPERTY[<?echo $arProp["ID"]?>][SHOW]" id="hidden_SECTION_PROPERTY_<?echo $arProp["ID"]?>" value="<?echo is_array($arLink)? "Y": "N";?>">
					<?endif?>
					<?echo htmlspecialcharsex($arProp["NAME"])?>
				</td>
				<td align="left"><?
					if($arProp['PROPERTY_TYPE'] == "S" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_S");
					elseif($arProp['PROPERTY_TYPE'] == "N" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_N");
					elseif($arProp['PROPERTY_TYPE'] == "L" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_L");
					elseif($arProp['PROPERTY_TYPE'] == "F" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_F");
					elseif($arProp['PROPERTY_TYPE'] == "G" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_G");
					elseif($arProp['PROPERTY_TYPE'] == "E" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_E");
					elseif($arProp['USER_TYPE'] && is_array($ar = CIBlockProperty::GetUserType($arProp['USER_TYPE'])))
						echo htmlspecialcharsex($ar["DESCRIPTION"]);
					else
						echo GetMessage("IBLOCK_PROP_S");
				?></td>
				<td style="text-align:center"><?
					echo '<input type="checkbox" value="Y" '.((is_array($arLink) && $arLink["INHERITED"] == "Y")? 'disabled="disabled"': '').' name="SECTION_PROPERTY['.$arProp['ID'].'][SMART_FILTER]" '.($arLink["SMART_FILTER"] == "Y"? 'checked="checked"': '').'>';
				?></td>
				<td align="left"><?
					if(!is_array($arLink) || $arLink["INHERITED"] == "N")
						echo '<a href="javascript:deleteSectionProperty('.$arProp['ID'].', \'select_SECTION_PROPERTY\', \'shadow_SECTION_PROPERTY\', \'table_SECTION_PROPERTY\')">'.GetMessage("CAT_CEDIT_PROP_TABLE_ACTION_DELETE").'</a>';
					else
						echo '&nbsp;';
				?></td>
			</tr>
			<?endwhile?>
			<tr <?echo ($rows == 0)? '': 'style="display:none"';?>>
				<td align="center" colspan="4">
					<?echo GetMessage("CAT_CEDIT_PROP_TABLE_EMPTY")?>
				</td>
			</tr>
			</table>
			<select id="shadow_SECTION_PROPERTY" style="display:none">
			<?foreach($arShadow as $key => $value):?>
				<option value="<?echo htmlspecialcharsex($key)?>"><?echo htmlspecialcharsbx($value)?></option>
			<?endforeach?>
			</select>
			<select id="select_SECTION_PROPERTY">
			<?foreach($arHidden as $key => $value):?>
				<option value="<?echo htmlspecialcharsex($key)?>"><?echo htmlspecialcharsbx($value)?></option>
			<?endforeach?>
			</select>
			<input type="button" value="<?echo GetMessage("CAT_CEDIT_PROP_TABLE_ACTION_ADD")?>" onclick="javascript:addSectionProperty(<?echo $arIBlock["ID"];?>, 'select_SECTION_PROPERTY', 'shadow_SECTION_PROPERTY', 'table_SECTION_PROPERTY')">
			<script>
			var target_id = '';
			var target_select_id = '';
			var target_shadow_id = '';
			function addSectionProperty(iblock_id, select_id, shadow_id, table_id)
			{
				var select = BX(select_id);
				if(select && select.value > 0)
				{
					var hidden = BX('hidden_SECTION_PROPERTY_' + select.value);
					var tr = BX('tr_SECTION_PROPERTY_' + select.value);
					if(hidden && tr)
					{
						jsSelectUtils.deleteOption(select_id, select.value);
						hidden.value = 'Y';
						var tds = BX.findChildren(tr, {tag:'td'}, true);
						BX.fx.colorAnimate.addRule('animationRule',"#F8F9FC","#faeeb4", "background-color", 50, 1, true);
						tr.style.display = 'table-row';
						for(var i = 0; i < tds.length; i++)
							BX.fx.colorAnimate(tds[i], 'animationRule');
					}
					adjustEmptyTR(table_id);
				}

				if(select && select.value == 0)
				{
					target_id = table_id;
					target_select_id = select_id;
					target_shadow_id = shadow_id;
					(new BX.CDialog({
						'content_url' : '/bitrix/admin/iblock_edit_property.php?lang=<?echo LANGUAGE_ID?>&IBLOCK_ID='+iblock_id+'&ID=n0&bxpublic=Y&from_module=iblock&return_url=section_edit',
						'width' : 700,
						'height' : 400,
						'buttons': [BX.CDialog.btnSave, BX.CDialog.btnCancel]
					})).Show();
				}
			}
			function deleteSectionProperty(id, select_id, shadow_id, table_id)
			{
				var hidden = BX('hidden_SECTION_PROPERTY_' + id);
				var tr = BX('tr_SECTION_PROPERTY_' + id);
				if(hidden && tr)
				{
					hidden.value = 'N';
					tr.style.display = 'none';
					var select = BX(select_id);
					var shadow = BX(shadow_id);
					if(select && shadow)
					{
						jsSelectUtils.deleteAllOptions(select);
						for(var i = 0; i < shadow.length; i++)
						{
							if(shadow[i].value <= 0)
								jsSelectUtils.addNewOption(select, shadow[i].value, shadow[i].text);
							else if (BX('hidden_SECTION_PROPERTY_' + shadow[i].value).value == 'N')
								jsSelectUtils.addNewOption(select, shadow[i].value, shadow[i].text);
						}
					}
					adjustEmptyTR(table_id);
				}
			}
			function createSectionProperty(id, name, type)
			{
				var tbl = BX(target_id);
				if(tbl)
				{
					var cnt = tbl.rows.length;
					var row = tbl.insertRow(cnt-1);
					//row.vAlign = 'top';
					row.id = 'tr_SECTION_PROPERTY_' + id;
					row.insertCell(-1);
					row.insertCell(-1);
					row.insertCell(-1);
					row.insertCell(-1);
					row.cells[0].align = 'left';
					row.cells[0].innerHTML = '<input type="hidden" name="SECTION_PROPERTY['+id+'][SHOW]" id="hidden_SECTION_PROPERTY_'+id+'" value="Y">'+name;
					row.cells[1].align = 'left';
					row.cells[1].innerHTML = type;
					row.cells[2].style.textAlign = 'center';
					row.cells[2].align = 'center';
					row.cells[2].innerHTML = '<input type="checkbox" value="Y" name="SECTION_PROPERTY['+id+'][SMART_FILTER]">';
					row.cells[3].align = 'left';
					row.cells[3].innerHTML = '<a href="javascript:deleteSectionProperty('+id+', \''+target_select_id+'\', \''+target_shadow_id+'\', \''+target_id+'\')"><?echo GetMessageJS("CAT_CEDIT_PROP_TABLE_ACTION_DELETE")?></a>';
					var shadow = BX(target_shadow_id);
					if(shadow)
						jsSelectUtils.addNewOption(shadow, id, name);
					adjustEmptyTR(target_id);
				}
			}
			function adjustEmptyTR(table_id)
			{
				var tbl = BX(table_id);
				if(tbl)
				{
					var cnt = tbl.rows.length;
					var tr = tbl.rows[cnt-1];

					var display = 'table-row';
					for(var i = 1; i < cnt-1; i++)
					{
						if(tbl.rows[i].style.display != 'none')
							display = 'none';
					}
					tr.style.display = display;
				}
			}
			</script>
		</td></tr>
		<?
		$arCatalog = false;
		if (CModule::IncludeModule("catalog"))
			$arCatalog = CCatalog::GetSkuInfoByProductID($IBLOCK_ID);

		if (is_array($arCatalog))
		{
			$arPropLinks = CIBlockSectionPropertyLink::GetArray($arCatalog["IBLOCK_ID"], 0);
		?>
		<tr colspan="2" class="heading">
			<td align="center"><?echo GetMessage("CAT_CEDIT_PROP_SKU_SECTION");?></td>
		</tr>
		<tr colspan="2"><td align="center">
			<table class="internal" id="table_SKU_SECTION_PROPERTY">
			<tr class="heading">
				<td><?echo GetMessage("CAT_CEDIT_PROP_TABLE_NAME");?></td>
				<td><?echo GetMessage("CAT_CEDIT_PROP_TABLE_TYPE");?></td>
				<td><?echo GetMessage("CAT_CEDIT_PROP_TABLE_SMART_FILTER");?></td>
				<td><?echo GetMessage("CAT_CEDIT_PROP_TABLE_ACTION");?></td></tr>
			<?
			if(CIBlockRights::UserHasRightTo($arCatalog["IBLOCK_ID"], $arCatalog["IBLOCK_ID"], "iblock_edit"))
				$arShadow = $arHidden = array(
					-1 => GetMessage("CAT_CEDIT_PROP_SELECT_CHOOSE"),
					0 => GetMessage("CAT_CEDIT_PROP_SELECT_CREATE"),
				);
			else
				$arShadow = $arHidden = array(
					-1 => GetMessage("CAT_CEDIT_PROP_SELECT_CHOOSE"),
				);

			$rsProps =  CIBlockProperty::GetList(array(
					"SORT"=>"ASC",
					'ID' => 'ASC',
				), array(
					"IBLOCK_ID" => $arCatalog["IBLOCK_ID"],
					"CHECK_PERMISSIONS" => "N",
					"ACTIVE"=>"Y",
				));
			$rows = 0;
			while ($arProp = $rsProps->Fetch()):

				if($arProp["ID"] == $arCatalog["SKU_PROPERTY_ID"])
					continue;

				if(array_key_exists($arProp["ID"], $arPropLinks))
				{
					$rows++;
					$arLink = $arPropLinks[$arProp["ID"]];
					if($arLink["INHERITED"] == "N")
						$arShadow[$arProp["ID"]] = $arProp["NAME"];
				}
				else
				{
					$arLink = false;
					$arHidden[$arProp["ID"]] = $arProp["NAME"];
					$arShadow[$arProp["ID"]] = $arProp["NAME"];
				}
			?>
			<tr id="tr_SECTION_PROPERTY_<?echo $arProp["ID"]?>" <?if(!is_array($arLink)) echo 'style="display:none"';?>>
				<td align="left">
					<?if(!is_array($arLink) || $arLink["INHERITED"] == "N"):?>
					<input type="hidden" name="SECTION_PROPERTY[<?echo $arProp["ID"]?>][SHOW]" id="hidden_SECTION_PROPERTY_<?echo $arProp["ID"]?>" value="<?echo is_array($arLink)? "Y": "N";?>">
					<?endif?>
					<?echo htmlspecialcharsex($arProp["NAME"])?>
				</td>
				<td align="left"><?
					if($arProp['PROPERTY_TYPE'] == "S" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_S");
					elseif($arProp['PROPERTY_TYPE'] == "N" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_N");
					elseif($arProp['PROPERTY_TYPE'] == "L" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_L");
					elseif($arProp['PROPERTY_TYPE'] == "F" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_F");
					elseif($arProp['PROPERTY_TYPE'] == "G" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_G");
					elseif($arProp['PROPERTY_TYPE'] == "E" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_E");
					elseif($arProp['USER_TYPE'] && is_array($ar = CIBlockProperty::GetUserType($arProp['USER_TYPE'])))
						echo htmlspecialcharsex($ar["DESCRIPTION"]);
					else
						echo GetMessage("IBLOCK_PROP_S");
				?></td>
				<td style="text-align:center"><?
					echo '<input type="checkbox" value="Y" '.((is_array($arLink) && $arLink["INHERITED"] == "Y")? 'disabled="disabled"': '').' name="SECTION_PROPERTY['.$arProp['ID'].'][SMART_FILTER]" '.($arLink["SMART_FILTER"] == "Y"? 'checked="checked"': '').'>';
				?></td>
				<td align="left"><?
					if(!is_array($arLink) || $arLink["INHERITED"] == "N")
						echo '<a href="javascript:deleteSectionProperty('.$arProp['ID'].', \'select_SKU_SECTION_PROPERTY\', \'shadow_SKU_SECTION_PROPERTY\', \'table_SKU_SECTION_PROPERTY\')">'.GetMessage("CAT_CEDIT_PROP_TABLE_ACTION_DELETE").'</a>';
					else
						echo '&nbsp;';
				?></td>
			</tr>
			<?endwhile?>
			<tr <?echo ($rows == 0)? '': 'style="display:none"';?>>
				<td align="center" colspan="4">
					<?echo GetMessage("CAT_CEDIT_PROP_TABLE_EMPTY")?>
				</td>
			</tr>
			</table>
			<select id="shadow_SKU_SECTION_PROPERTY" style="display:none">
			<?foreach($arShadow as $key => $value):?>
				<option value="<?echo htmlspecialcharsex($key)?>"><?echo htmlspecialcharsbx($value)?></option>
			<?endforeach?>
			</select>
			<select id="select_SKU_SECTION_PROPERTY">
			<?foreach($arHidden as $key => $value):?>
				<option value="<?echo htmlspecialcharsex($key)?>"><?echo htmlspecialcharsbx($value)?></option>
			<?endforeach?>
			</select>
			<input type="button" value="<?echo GetMessage("CAT_CEDIT_PROP_TABLE_ACTION_ADD")?>" onclick="javascript:addSectionProperty(<?echo $arCatalog["IBLOCK_ID"];?>, 'select_SKU_SECTION_PROPERTY', 'shadow_SKU_SECTION_PROPERTY', 'table_SKU_SECTION_PROPERTY')">
		</td></tr>
			<?
		}
		?>
	<?
$tabControl->EndCustomField("SECTION_PROPERTY", '');
$tabControl->Buttons(array("disabled"=>false));
$tabControl->Show();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
