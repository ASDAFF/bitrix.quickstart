<?
$module_id = "epages.pickpoint";
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/constants.php");
$CAT_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($CAT_RIGHT>="R"):
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/fields.php");
	global $MESS;
	global $arOptions;
	IncludeModuleLangFile(__FILE__);
	include_once($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/include.php");
	
	$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];
	$Update = $_REQUEST["Update"];
	$RestoreDefaults = $_REQUEST["RestoreDefaults"];
	if($CAT_RIGHT>="W" && check_bitrix_sessid())
	{
		
		if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0)
		{
			COption::RemoveOption($module_id);
			$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
			while($zr = $z->Fetch())
				$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
		}
		if($REQUEST_METHOD=="POST" && strlen($Update)>0)
		{
			if(!CheckPickpointLicense($_REQUEST["pp_ikn_number"]))
			{
				$APPLICATION->ThrowException(GetMessage("PP_WRONG_KEY"));
			
			}
			else
			{

				foreach($_REQUEST as $sCode=>$Value)
				{
				    if (in_array($sCode,array_keys($arOptions["OPTIONS"])))
                    {
    					if($Value)
    					{
    						if(is_array($Value)) $Value=serialize($Value);

    						if(!COption::SetOptionString($module_id, $sCode, $Value))
    						$arOptions["OPTIONS"][$sCode] = $Value;
    					}
                        else
                            COption::SetOptionString($module_id, $sCode, "");

                    }

				}
				if($_REQUEST["pp_add_info"])
				{
					COption::SetOptionString($module_id, "pp_add_info", 1);
				}
				else COption::SetOptionString($module_id, "pp_add_info", 0);

                if($_REQUEST["pp_use_coeff"])
				{
					COption::SetOptionString($module_id, "pp_use_coeff", 1);
				}
                else COption::SetOptionString($module_id, "pp_use_coeff", 0);

                if ($_REQUEST["pp_test_mode"])
                {
					COption::SetOptionString($module_id, "pp_test_mode", 1);
				}
                else COption::SetOptionString($module_id, "pp_test_mode", 0);

				$arTableOptions = $_REQUEST["OPTIONS"];


				foreach($arTableOptions as $iPT=>$arPersonTypeValues)
				{
					foreach($arPersonTypeValues as $sValueCode=>$arValues)
					{
						foreach($arValues as $iKey=>$arValueList)
						{
							
							if($arValueList["TYPE"]=="ANOTHER")
							{
								$arTableOptions[$iPT][$sValueCode][$iKey]["VALUE"] = $arValueList["VALUE_ANOTHER"];
							}
							unset($arTableOptions[$iPT][$sValueCode][$iKey]["VALUE_ANOTHER"]);
						}
					}
				}
				COption::SetOptionString($module_id, "OPTIONS", serialize($arTableOptions));
				
				if(!empty($_REQUEST["CITIES"]))
				{
					foreach($_REQUEST["CITIES"] as $arCityFields)
					{
						if(IntVal($arCityFields["BX_ID"]))
						{
							$arCityFields["PRICE"] = FloatVal($arCityFields["PRICE"]);
							if(!isset($arCityFields["ACTIVE"])) $arCityFields["ACTIVE"]="N";
							CPickpoint::SetPPCity($arCityFields["PP_ID"], $arCityFields);
						}
					}
				}

                if(!empty($_REQUEST["ZONES"]))
				{

					foreach($_REQUEST["ZONES"] as $zoneId => $arZoneFields)
					{
					    $arZoneFields["PRICE"] = FloatVal($arZoneFields["PRICE"]);
						CPickpoint::SetPPZone($zoneId, $arZoneFields);

					}
				}
				$arOptions = Array();
				if(!(COption::GetOptionString($module_id,"pp_service_types_all","")))
				{
					COption::SetOptionString($module_id,"pp_service_types_all",serialize($arServiceTypes));
				}
				if(!(COption::GetOptionString($module_id,"pp_enclosing_types_all","")))
				{
					COption::SetOptionString($module_id,"pp_enclosing_types_all",serialize($arEnclosingTypes));
				}

				$iTimestamp = COption::GetOptionInt($module_id, "pp_city_download_timestamp",0);

				if(time() > $iTimestamp || !file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/cities.csv"))
				{
					CPickpoint::GetCitiesCSV();
				}

				$arOptions = Array();
				$arOptions["OPTIONS"]["pp_add_info"] = COption::GetOptionString($module_id, "pp_add_info","1");
				$arOptions["OPTIONS"]["pp_ikn_number"] = COption::GetOptionString($module_id, "pp_ikn_number","");
				$arOptions["OPTIONS"]["pp_enclosure"] = COption::GetOptionString($module_id, "pp_enclosure","");
				$arOptions["OPTIONS"]["pp_service_types_selected"] = COption::GetOptionString($module_id, "pp_service_types_selected");
				$arOptions["OPTIONS"]["pp_service_types_all"] = COption::GetOptionString($module_id, "pp_service_types_all");
				$arOptions["OPTIONS"]["pp_enclosing_types_selected"] = COption::GetOptionString($module_id, "pp_enclosing_types_selected");
				$arOptions["OPTIONS"]["pp_enclosing_types_all"] = COption::GetOptionString($module_id, "pp_enclosing_types_all");
				$arOptions["OPTIONS"]["pp_zone_count"] = COption::GetOptionString($module_id, "pp_zone_count", 10);
				$arOptions["OPTIONS"]["pp_from_city"] = COption::GetOptionString($module_id, "pp_from_city", "");
				$arOptions["OPTIONS"]["pp_use_coeff"] = COption::GetOptionString($module_id, "pp_use_coeff", "");
				$arOptions["OPTIONS"]["pp_custom_coeff"] = COption::GetOptionString($module_id, "pp_custom_coeff", "");
				$arOptions["OPTIONS"]["pp_api_login"] = COption::GetOptionString($module_id, "pp_api_login", "");
				$arOptions["OPTIONS"]["pp_api_password"] = COption::GetOptionString($module_id, "pp_api_password", "");
				$arOptions["OPTIONS"]["pp_test_mode"] = COption::GetOptionString($module_id, "pp_test_mode", "");
				$arOptions["OPTIONS"]["pp_free_delivery_price"] = COption::GetOptionString($module_id, "pp_free_delivery_price", "");
				LocalRedirect($APPLICATION->GetCurPageParam());
			}
		}
	}
	$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "support_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
		array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_TITLE_ZONES"), "ICON" => "support_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_ZONES")),
		array("DIV" => "edit3", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "pickpoint_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
	);
	ShowNote(COption::GetOptionString("pickpoint","comment"),COption::GetOptionString("pickpoint","comment_type"));
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	$tabControl->Begin();
	$arServiceTypes = strlen($arOptions["OPTIONS"]["pp_service_types_all"])?unserialize($arOptions["OPTIONS"]["pp_service_types_all"]):$arServiceTypes;
	$arSelectedST = strlen($arOptions["OPTIONS"]["pp_service_types_selected"])?unserialize($arOptions["OPTIONS"]["pp_service_types_selected"]):Array();
	$arEnclosingTypes = strlen($arOptions["OPTIONS"]["pp_enclosing_types_all"])?unserialize($arOptions["OPTIONS"]["pp_enclosing_types_all"]):$arEnclosingTypes;
	$arSelectedET = strlen($arOptions["OPTIONS"]["pp_enclosing_types_selected"])?unserialize($arOptions["OPTIONS"]["pp_enclosing_types_selected"]):Array();
	$arTableOptions = (unserialize(COption::GetOptionString($module_id,"OPTIONS")));
	if(isset($_REQUEST["OPTIONS"])) $arTableOptions = $_REQUEST["OPTIONS"];
	foreach($arOptions["OPTIONS"] as $sKey=>$sValue)
	{
		if(isset($_REQUEST[$sKey])) $arOptions["OPTIONS"][$sKey] = (is_array($_REQUEST[$sKey]))?serialize($_REQUEST[$sKey]):$_REQUEST[$sKey];
	}
	?>
	<?if($ex = $APPLICATION->GetException()) CAdminMessage::ShowOldStyleError($ex->GetString());?>
	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?echo LANG?>" name="ara">
	<?=bitrix_sessid_post();?>

	<?
    $tabControl->BeginNextTab();?>

		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("PP_IKN_NUMBER");?>
			</td>
			<td valign="top" width="50%">
				<input type="text" size="30" maxlength="255" value="<?=htmlspecialcharsbx($arOptions["OPTIONS"]["pp_ikn_number"])?>" name="pp_ikn_number">
			</td>
		</tr>
        <tr>
            <td valign="top" width="50%">
                <?=GetMessage("PP_API_LOGIN");?>
            </td>
            <td valign="top" width="50%">
                <input type="text" size="30" maxlength="255" value="<?=htmlspecialcharsbx($arOptions["OPTIONS"]["pp_api_login"])?>" name="pp_api_login">
            </td>
        </tr>
        <tr>
            <td valign="top" width="50%">
                <?=GetMessage("PP_API_PASSWORD");?>
            </td>
            <td valign="top" width="50%">
                <input type="text" size="30" maxlength="255" value="<?=htmlspecialcharsbx($arOptions["OPTIONS"]["pp_api_password"])?>" name="pp_api_password">
            </td>
        </tr>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("PP_ENCLOSURE");?>
			</td>
			<td valign="top" width="50%">
				<input type="text" size="30" maxlength="255" value="<?=htmlspecialcharsbx($arOptions["OPTIONS"]["pp_enclosure"])?>" name="pp_enclosure">
			</td>
		</tr>
        <tr>
			<td valign="top">
				<input type = "checkbox" id = "pp_test_mode" <?=$arOptions["OPTIONS"]["pp_test_mode"]?"checked":""?> name = "pp_test_mode"/>
			</td>
			<td valign="top">
				<label for = "pp_test_mode"><?=GetMessage("PP_TEST_MODE")?></label>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<span class="required">*</span><?echo GetMessage("PP_SERVICE_TYPES_SELECTED")?>:<br><img src="/bitrix/images/sale/mouse.gif" width="44" height="21" border="0" alt="">
			</td>
			<td valign="top">
				<select name="pp_service_types_selected[]" size="5" multiple width="30">
					<?foreach($arServiceTypes as $iKey=>$sValue):?>
						<option value="<?=$iKey?>" <?=in_array($iKey,$arSelectedST)?"selected":""?>><?=$sValue?></option>
					<?endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<span class="required">*</span><?echo GetMessage("PP_ENCLOSING_TYPES_SELECTED")?>:<br><img src="/bitrix/images/sale/mouse.gif" width="44" height="21" border="0" alt="">
			</td>
			<td valign="top">
				<select name="pp_enclosing_types_selected[]" size="5" multiple width="30">
					<?foreach($arEnclosingTypes as $iKey=>$sValue):?>
						<option value="<?=$iKey?>" <?=in_array($iKey,$arSelectedET)?"selected":""?>><?=$sValue?></option>
					<?endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<input type = "checkbox" id = "pp_add_info" <?=$arOptions["OPTIONS"]["pp_add_info"]?"checked":""?> name = "pp_add_info"/>
			</td>
			<td valign="top">
				<label for = "pp_add_info"><?=GetMessage("PP_ADD_INFO")?></label>
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("PP_FREE_DELIVERY_PRICE");?>
			</td>
			<td valign="top" width="50%">
				<input type="text" size="30" maxlength="255" value="<?=htmlspecialcharsbx($arOptions["OPTIONS"]["pp_free_delivery_price"])?>" name="pp_free_delivery_price">
			</td>
		</tr>

<?/*        <tr>
            <td valign="top" width="50%">
                <?=GetMessage("PP_ZONES_COUNT");?>
            </td>
            <td valign="top" width="50%">
                <input type="text" size="30" maxlength="255" value="<?=htmlspecialcharsbx($arOptions["OPTIONS"]["pp_zone_count"])?>" name="pp_zone_count">
            </td>
        </tr>
*/?>
		<tr>
			<td colspan = "2" align="center">
				<script>
					var arFields = <?=CPickpoint::PHPArrayToJS_in($arFields,"arFields")?>;
					var arFieldsName = <?=CPickpoint::PHPArrayToJS_in($arFieldsName,"arFieldsName")?>;
					function DeleteTable(a)
					{
						while(a=a.parentNode)
						{
							//alert(a.tagName+"=="+);
							if(a.className=="dAdded") break;
						}
						a.parentNode.removeChild(a);
					}
					function AddTable(key,pt,button)
					{
						Table = "<table cellspacing='2' cellpadding='0' border='0' class = 'tType'><tr><td><?=GetMessage("PP_TYPE")?></td><td><select name='OPTIONS[#PT#][#KEY#][#NUMBER#][TYPE]' id='OPTIONS[#PT#][#KEY#][#NUMBER#][TYPE]' onchange='PropertyTypeChange(this,#PT#)'>";
						for (sKey in arFieldsName)
						{
							Table += "<option value = '"+sKey+"'>"+arFieldsName[sKey]+"</option>";
						}
						Table += "</select></td></tr><tr><td><?=GetMessage("PP_VALUE")?></td><td><select name='OPTIONS[#PT#][#KEY#][#NUMBER#][VALUE]' id='OPTIONS[#PT#][#KEY#][#NUMBER#][VALUE]' style='display: none;'></select><input type='text' value='' name='OPTIONS[#PT#][#KEY#][#NUMBER#][VALUE_ANOTHER]' id='OPTIONS[#PT#][#KEY#][#NUMBER#][VALUE_ANOTHER]' style=''></td></tr><tr><td colspan='2' align='right'><a onclick = 'DeleteTable(this)' class='aDelete'><?=GetMessage("PP_DELETE")?></a></td></tr></table>";										
						Td = button.parentNode.parentNode;
						Number = Td.children.length-1;
						Table = Table.split("#KEY#").join(key); 
						Table = Table.split("#PT#").join(pt); 
						Table = Table.split("#NUMBER#").join(Number); 
						
						var Div = document.createElement('div');
						Div.setAttribute('class',"dAdded");
						Div.innerHTML = Table;
						Td.insertBefore(Div, button.parentNode);
					}
					
					function CleanSelect(Select)
					{
						count = Select.options.length;
						for(i=0; i<count; i++) Select.removeChild(Select.options[0]);
					}
					
					function PropertyTypeChange(Select,iPersonType)
					{
						
						ID = Select.id;
						ChildSelect = document.getElementById(ID.replace("[TYPE]","[VALUE]"));
						InputAnother = document.getElementById(ID.replace("[TYPE]","[VALUE_ANOTHER]"));
						Options = Select.options;
						for(i = 0; i < Options.length; i++)
						{
							if(Options[i].selected)
							{
								SelectedOption = Options[i];
								break;
							}
						}
						CleanSelect(ChildSelect);
						InputAnother.value = "";
						Type = SelectedOption.value;
						switch(Type)
						{
							case "ANOTHER":
								ChildSelect.style.display="none";
								InputAnother.style.display="";	
							break;
							case "PROPERTY":
								for (sKey in arFields[Type][iPersonType] )
								{
									NewOption = new Option;
									NewOption.value = sKey;
									NewOption.text = arFields[Type][iPersonType][sKey];
									ChildSelect.appendChild(NewOption);
								}
								ChildSelect.style.display="";
								InputAnother.style.display="none";								
							break;
							case "USER":
							case "ORDER":
								for (sKey in arFields[Type] )
								{
									NewOption = new Option;
									NewOption.value = sKey;
									NewOption.text = arFields[Type][sKey];
									ChildSelect.appendChild(NewOption);
								}
								ChildSelect.style.display="";
								InputAnother.style.display="none";									
							break;
							
						}
						
						//alert(ID);
					
					}
				</script>
				

				<?
				$aTabs1 = array();
				$personType = Array();
				$dbPersonType = CSalePersonType::GetList(Array("ID"=>"ASC"), Array("ACTIVE"=>"Y"));
				while($arPersonType = $dbPersonType -> GetNext())
				{
					$aTabs1[] = Array("DIV"=>"oedit".$arPersonType["ID"], "TAB" => $arPersonType["NAME"], "TITLE" => $arPersonType["NAME"]);
					$personType[$arPersonType["ID"]] = $arPersonType;
				}
				$tabControl1 = new CAdminViewTabControl("tabControl1", $aTabs1);
				$tabControl1->Begin();
				foreach($personType as $val)
				{
					$tabControl1->BeginNextTab();
					?>
					<table class = "internal" width = "80%">
						<tr class="heading"><td align="center"><?=GetMessage("PP_VALUE_NAME")?></td><td align="center"><?=GetMessage("PP_VALUE")?></td></tr>
						
						<?$arSelected = count($arTableOptions[$val["ID"]]["FIO"])?$arTableOptions[$val["ID"]]["FIO"]:Array($arOptionDefaults["FIO"]);
						$arRow = Array(
							"NAME"=>GetMessage("PP_FIO"),
							"CODE"=>"FIO",
							"SELECTED"=>$arSelected
						);
						require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/row.php");?>

						<?$arSelected = count($arTableOptions[$val["ID"]]["ADDITIONAL_PHONES"])?$arTableOptions[$val["ID"]]["ADDITIONAL_PHONES"]:Array($arOptionDefaults["ADDITIONAL_PHONES"]);
						$arRow = Array(
							"NAME"=>GetMessage("PP_ADDITIONAL_PHONES"),
							"CODE"=>"ADDITIONAL_PHONES",
							"SELECTED"=>$arSelected
						);
						require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/row.php");?>
						
						<?$arSelected = count($arTableOptions[$val["ID"]]["NUMBER_P"])?$arTableOptions[$val["ID"]]["NUMBER_P"]:Array($arOptionDefaults["NUMBER_P"]);
						$arRow = Array(
							"NAME"=>GetMessage("PP_NUMBER_P"),
							"CODE"=>"NUMBER_P",
							"SELECTED"=>$arSelected
						);
						require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/row.php");?>
						
						<?$arSelected = count($arTableOptions[$val["ID"]]["EMAIL"])?$arTableOptions[$val["ID"]]["EMAIL"]:Array($arOptionDefaults["EMAIL"]);
						$arRow = Array(
							"NAME"=>GetMessage("PP_EMAIL"),
							"CODE"=>"EMAIL",
							"SELECTED"=>$arSelected
						);
						require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/row.php");?>
					</table>
					<?
				}
				$tabControl1->End();
				?>				
			</td>
		</tr>

	<?$tabControl->BeginNextTab();?>
        <?
			$hFile = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/cities.csv","r");
			$arCities = Array();

			while($sStr = fgets($hFile))
			{
				$arStr = explode(";",$sStr);
				//$arCity = CPickpoint::GetCity(Array("PP_ID"=>trim($arStr[0]),"CODE"=>trim($arStr[2])));
				if ('true' === trim($arStr[4])) {
					$arCity["NAME"] = trim($arStr[1]);
					$arCities[] = $arCity;
				}
			}

		?>
        <tr>
            <td valign="top" width="50%">
                <?=GetMessage("PP_STORE_LOCATION");?>
            </td>
            <td valign="top" width="50%">
            <select name="pp_from_city">
            <?foreach($arCities as $iKey=>$arCity):?>
                <option <?if ($arOptions["OPTIONS"]["pp_from_city"]==$arCity['NAME']):?>selected="selected"<?endif?>><?=$arCity['NAME']?></option>
            <?endforeach?>
            </select>
            </td>
        </tr>
        <tr>
            <td valign="top" width="50%">
                <?=GetMessage("PP_STORE_ADDRESS");?>
            </td>
            <td valign="top" width="50%">
                <input type="text" size="30" maxlength="255" value="<?=htmlspecialcharsbx($arOptions["OPTIONS"]["pp_store_address"])?>" name="pp_store_address">
        </td>
        </tr>
        <tr>
            <td valign="top" width="50%">
                <?=GetMessage("PP_STORE_PHONE");?>
            </td>
            <td valign="top" width="50%">
                <input type="text" size="30" maxlength="255" value="<?=htmlspecialcharsbx($arOptions["OPTIONS"]["pp_store_phone"])?>" name="pp_store_phone">
        </td>
        </tr>
		<tr class="heading">
			<td><?=GetMessage("MAIN_TAB_TITLE_ZONES")?></td>

			<td><?=GetMessage("PP_USER_PRICE")?></td>

		</tr>

		<?
        $arZones = CPickpoint::GetZonesArray();

        for($iKey = 1; $iKey <= PP_ZONES_COUNT; $iKey++):
//        foreach($arCities as $iKey=>$arCity):?>
			<tr>
				<td align = "left"><?=GetMessage("PP_ZONE")?> <?=$iKey?></td>

				<td align = "center">
					<?
						if(isset($_REQUEST["ZONES"][$iKey]["PRICE"])) $iVal = $_REQUEST["ZONES"][$iKey]["PRICE"];
						else $iVal = $arZones[$iKey]["PRICE"];
					?>
					<input type = "text" name = "ZONES[<?=$iKey?>][PRICE]" value = "<?=number_format($iVal ,2,".","")?>"/>

				</td>

			</tr>
		<?endfor;?>
        <tr class="heading">
			<td colspan="2"><?=GetMessage("PP_COEFF")?></td>
		</tr>
        <tr>
			<td valign="top">
				<input type = "checkbox" id = "pp_use_coeff" <?=$arOptions["OPTIONS"]["pp_use_coeff"]?"checked":""?> name = "pp_use_coeff"/>
			</td>
			<td valign="top">
				<label for = "pp_use_coeff"><?=GetMessage("PP_USE_COEFF")?></label>
			</td>
		</tr>
        <?if($arOptions["OPTIONS"]["pp_use_coeff"]):?>
    	<tr>
    		<td align = "left"><?=GetMessage("PP_COEFF_CUSTOM")?></td>

    		<td>

    			<input type = "text" name = "pp_custom_coeff" value = "<?=$arOptions["OPTIONS"]["pp_custom_coeff"]?number_format($arOptions["OPTIONS"]["pp_custom_coeff"] ,2,".",""):""?>"/>

    		</td>

    	</tr>
        <?endif;?>
	
	<?
	$tabControl->BeginNextTab();
	?>
		<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
		<?$tabControl->Buttons();?>
		<script language="JavaScript">
		function RestoreDefaults()
		{
			if (confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
				window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
		}
		</script>
		<input type="submit" <?if ($CAT_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
		<input type="hidden" name="Update" value="Y">
		<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
	<?$tabControl->End();?>
	</form>
<?endif;?>
