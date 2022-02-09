<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

IncludeModuleLangFile(__FILE__);

$SID = $_REQUEST["SID"];

//$bInstall = strlen($handlerPath) > 0 && strlen($SID) <= 0;

$arErrorsList = array();

//$obDelivery = new CSaleDeliveryHandler();

$siteList = array();
$rsSites = CSite::GetList($by="sort", $order="asc", Array());
$i = 0;
while($arRes = $rsSites->Fetch())
{
	$siteList[] = array(
		'ID' => $arRes['ID'],
		'NAME' => $arRes['NAME'],
	);
}
$siteCount = count($siteList);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_REQUEST["Update"]) && check_bitrix_sessid())
{
	if ($arHandlersData = unserialize($_REQUEST["STRUCTURE"]))
	{
		if ($_REQUEST["USE_DIFF_SITES_SETTINGS"] != "Y")
		{
			$curSITE_ID = $_REQUEST["current_site"];
			$arHandlersData = array("ALL" => $arHandlersData[$curSITE_ID]);
		}

		foreach ($arHandlersData as $siteID => $arHandler)
		{
			if ($arHandler["PROFILE_USE_DEFAULT"] == "Y")
			{
				$arHandlersData[$siteID]["PROFILES"] = "";
			}
			else
			{
				foreach ($arHandlersData[$siteID]["PROFILES"] as $profile_id => $arProfile)
				{
					if (is_array($arProfile["RESTRICTIONS_SUM"]))
					{
						$currency = array_shift($arProfile["RESTRICTIONS_SUM"]);
						foreach ($arProfile["RESTRICTIONS_SUM"] as $key => $value)
						{
							$arProfile["RESTRICTIONS_SUM"][$key] = CCurrencyRates::ConvertCurrency($value, $currency, $arHandlersData[$siteID]["BASE_CURRENCY"]);
						}
						
						$arHandlersData[$siteID]["PROFILES"][$profile_id] = $arProfile;
					}
				}
			}
			
			$arConfig = array();
			foreach ($arHandlersData[$siteID]["CONFIG"]["CONFIG"] as $configID => $arHandlerConfig)
			{
				$arConfig[$configID] = $arHandlerConfig["VALUE"];
			}
			$arHandlersData[$siteID]["CONFIG"] = $arConfig;
			//$arHandlersData[$siteID]["HANDLER"] = $handlerPath;
		}

		foreach ($arHandlersData as $SITE_ID => $arHandlerData)
		{
			//echo $SITE_ID.'<br />';
			CSaleDeliveryHandler::Set($SID, $arHandlerData, $SITE_ID == "ALL" ? false : $SITE_ID);
			//$obDelivery->Set($_REQUEST["SID"], $arHandlersData);
			if ($ex = $APPLICATION->GetException())
			{
				$arErrorsList[] = $ex->GetString();
			}
		}
		
		//die();
		
		if (!is_array($arErrorsList) || count($arErrorsList) <= 0)
		{
			if (strlen($_REQUEST["apply"]) > 0)
				LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG."&SID=".urlencode($SID));
			else
				LocalRedirect('/bitrix/admin/sale_delivery_handlers.php?lang='.LANG);
				
			die();
		}
	}
	else
	{
		$arErrorsList[] = GetMessage('SALE_DH_ERROR_UNRECOGNIZED');
	}
}

$rsDeliveryInfo = CSaleDeliveryHandler::GetBySID($SID);
if ($rsDeliveryInfo->SelectedRowsCount() <= 0)
{
	echo 'error';
	die();
}

while ($arHandler = $rsDeliveryInfo->Fetch())
{
	$bInstall = $arHandler["INSTALLED"] == "N";
	
	unset($arHandler["DBGETSETTINGS"]);
	unset($arHandler["DBSETSETTINGS"]);
	unset($arHandler["GETCONFIG"]);
	unset($arHandler["COMPABILITY"]);
	unset($arHandler["CALCULATOR"]);
	
	if (strlen($arHandler["LID"]) > 0)
		$arDeliveryInfo[$arHandler["LID"]] = $arHandler;
	else
	{
		$arDeliveryInfo = array("ALL" => $arHandler);
		break;
	}
}

if (count($arDeliveryInfo) > 0 && !isset($arDeliveryInfo['ALL']) && count($arDeliveryInfo) != count($siteList))
{
	$tmp = array_values($arDeliveryInfo);
	$ar = $tmp[0];
	foreach ($siteList as $arSite)
	{
		if (!isset($arDeliveryInfo[$arSite['ID']]))
		{
			$arDeliveryInfo[$arSite['ID']] = $ar;
			$arDeliveryInfo[$arSite['ID']]['ACTIVE'] = 'N';
		}
	}
}

if (!$bInstall)
{
	if (count($arDeliveryInfo) > 0)
	{
		$arSitesConfigured = array_keys($arDeliveryInfo);
		$bSites = $arSitesConfigured[0] != "ALL";
		
		if (!$bSites)
		{
			foreach ($siteList as $arSite)
			{
				$arDeliveryInfo[$arSite["ID"]] = $arDeliveryInfo["ALL"];
				$arDeliveryInfo[$arSite["ID"]]["LID"] = $arSite["ID"];
			}

			unset($arDeliveryInfo["ALL"]);
		}
		
		$handlerPath = $arDeliveryInfo[$siteList[0]["ID"]]["HANDLER"];
		$deliveryHint = $arDeliveryInfo[$siteList[0]["ID"]]['DESCRIPTION_INNER'];
		$deliveryName = $arDeliveryInfo[$siteList[0]["ID"]]['NAME'];
	}
	else
	{
		$bInstall = true;
	}
}

if ($bInstall)
{
	$arDeliveryInfoTmp = $arDeliveryInfo;
	$arDeliveryInfoTmp["ALL"]["ACTIVE"] = 'N';	
	$arDeliveryInfoTmp["ALL"]["SORT"] = '100';
	$arDeliveryInfo = array();
	foreach ($siteList as $arSite)
	{
		$arDeliveryInfo[$arSite["ID"]] = $arDeliveryInfoTmp["ALL"];
		$arDeliveryInfo[$arSite["ID"]]["LID"] = $arSite["ID"];
	}
	
	unset($arDeliveryInfoTmp);

	$handlerPath = $arDeliveryInfo[$siteList[0]["ID"]]["HANDLER"];
	$deliveryHint = $arDeliveryInfo[$siteList[0]["ID"]]['DESCRIPTION_INNER'];
	$deliveryName = $arDeliveryInfo[$siteList[0]["ID"]]['NAME'];
	
	$bSites = false;
}

if ($ex = $APPLICATION->GetException())
{
	$arErrorsList[] = $ex->GetString();
}

foreach ($siteList as $arSite)
{
	$curSITE_ID = $arSite["ID"];
	unset($arDeliveryInfo[$curSITE_ID]["SETTINGS"]);
	
	if (!is_array($arDeliveryInfo[$curSITE_ID]["CONFIG"]["CONFIG_GROUPS"]))
	{
		$arDeliveryInfo[$curSITE_ID]["CONFIG"]["CONFIG"] = array();
		foreach ($arDeliveryInfo[$curSITE_ID]["CONFIG"] as $key => $arConfig)
		{
			if ($key != "CONFIG")
			{
				$arConfig["GROUP"] = "none";
				$arDeliveryInfo[$curSITE_ID]["CONFIG"]["CONFIG"] = $arConfig;
				unset($arDeliveryInfo[$curSITE_ID]["CONFIG"][$key]);
			}
		}
		
		$arDeliveryInfo[$curSITE_ID]["CONFIG"]["CONFIG_GROUPS"] = array("none" => "");
	}
	
	foreach ($arDeliveryInfo[$curSITE_ID]['PROFILES'] as $key => $arProfile)
	{
		if (!is_set($arProfile['ACTIVE'])) $arProfile['ACTIVE'] = "Y";
		
		if (!is_array($arProfile["RESTRICTIONS_WEIGHT"]) || count($arProfile["RESTRICTIONS_WEIGHT"]) <= 0)
			$arProfile["RESTRICTIONS_WEIGHT"] = array(0);
		
		if (!is_array($arProfile["RESTRICTIONS_SUM"]) || count($arProfile["RESTRICTIONS_SUM"]) <= 0)
			$arProfile["RESTRICTIONS_SUM"] = array(0);
		else
			array_unshift($arProfile["RESTRICTIONS_SUM"], $arDeliveryInfo[$curSITE_ID]['BASE_CURRENCY']);

		foreach ($arProfile["RESTRICTIONS_WEIGHT"] as $pkey => $value) 
			$arProfile["RESTRICTIONS_WEIGHT"][$pkey] = number_format(doubleval($value), 2, '.', '');
		foreach ($arProfile["RESTRICTIONS_SUM"] as $pkey => $value) 
			$arProfile["RESTRICTIONS_SUM"][$pkey] = $pkey > 0 ? number_format(doubleval($value), 2, '.', '') : $value;
		if (count($arProfile["RESTRICTIONS_SUM"]) < 3)
			$arProfile["RESTRICTIONS_SUM"][] = "0.00";
		
		$arDeliveryInfo[$curSITE_ID]['PROFILES'][$key] = $arProfile;
	}
}

//echo '<pre>'; print_r($arDeliveryInfo); echo '</pre>';

$APPLICATION->SetTitle(GetMessage("SALE_DH_TITLE_EDIT").": (".htmlspecialcharsEx($SID).") ".htmlspecialcharsEx($deliveryName));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");


$aTabs = array(
	array("DIV" => "editbase", "TAB" => GetMessage("SALE_DH_EDIT_BASECONFIG"), "TITLE" => GetMessage("SALE_DH_EDIT_BASECONFIG_DESCR"))
);

$SITE_ID = $siteList[0]['ID'];
if (is_array($arDeliveryInfo[$SITE_ID]["CONFIG"]))
{
	if (is_array($arDeliveryInfo[$SITE_ID]["CONFIG"]["CONFIG_GROUPS"]))
	{
		foreach ($arDeliveryInfo[$SITE_ID]["CONFIG"]["CONFIG_GROUPS"] as $group => $title)
		{
			$configTabsCount++;
			$aTabs[] = array("DIV" => "edit_".htmlspecialcharsbx($group), "TAB" => htmlspecialcharsbx($title), "TITLE" => htmlspecialcharsbx($title));
		}
	}
	else
	{
		$configTabsCount++;
		$aTabs[] = array("DIV" => "edit_config", "TAB" => GetMessage('SALE_DH_EDIT_CONFIG'), "TITLE" => GetMessage('SALE_DH_EDIT_CONFIG_DESCR'));
	}
}

$aTabs[] = array("DIV" => "editbase_profiles", "TAB" => GetMessage('SALE_DH_EDIT_PROFILES'), "TITLE" => GetMessage('SALE_DH_EDIT_PROFILES_DESCR'));

$tabControl = new CAdminViewTabControl("tabControl", $aTabs, true, false);
$parentTabControl = new CAdminTabControl('parentTabControl', array(
	array("DIV" => "edit_main", "TAB" => GetMessage('SALE_DH_TAB_TITLE_EDIT'), "ICON" => "sale", "TITLE" => GetMessage('SALE_DH_TAB_TITLE_EDIT_ALT'))
), true, true);

$aContext = array(
	array(
		"TEXT" => GetMessage("SALE_DH_LIST"),
		"LINK" => "sale_delivery_handlers.php?lang=".LANG,
		"TITLE" => GetMessage("SALE_DH_LIST_ALT"),
		"ICON" => "btn_list"
	),
);

$obContextMenu = new CAdminContextMenu($aContext);
$obContextMenu->Show();

$arConfigValues = array();
foreach ($arDeliveryInfo[$SITE_ID]["CONFIG"]["CONFIG"] as $config_id => $arConfig)
{
	$arConfigValues[$config_id] = strlen($arConfig["VALUE"]) > 0 ? $arConfig["VALUE"] : $arConfig["DEFAULT"];
}
//echo '<pre>'; print_r($arDeliveryInfo); echo '</pre>';
?>
<script language="JavaScript">
var arStructure = <?=CUtil::PhpToJSObject($arDeliveryInfo)?>;
</script>
<script language="javascript">
var cur_site = '<?=htmlspecialcharsbx(CUtil::JSEscape($siteList[0]["ID"]))?>';
function changeSiteList(value)
{
	var SLHandler = document.getElementById('site_id');
	SLHandler.disabled = value;
}

function selectSite(current)
{
	if (current == cur_site) return;

	ShowWaitWindow();
	
	var CSHandler = document.getElementById('current_site');
	var FormHandler = document.forms.form1;
	
	for (var i in arStructure[cur_site])
	{
		if (i == 'CONFIG')
		{
			for (var j in arStructure[cur_site]['CONFIG']['CONFIG'])
			{
				var obElement = FormHandler['HANDLER[CONFIG][' + j + ']'];
				if (obElement)
				{
					try
					{
						if (obElement.type == 'checkbox')
						{
							arStructure[cur_site]['CONFIG']['CONFIG'][j]['VALUE'] = obElement.checked ? 'Y' : 'N';


							if (current != null)
							{
								if (arStructure[current]['CONFIG']['CONFIG'][j]['VALUE'] && arStructure[current]['CONFIG']['CONFIG'][j]['VALUE'].length > 0)
									obElement.checked = arStructure[current]['CONFIG']['CONFIG'][j]['VALUE'] == 'Y';
								else
									obElement.checked = arStructure[current]['CONFIG']['CONFIG'][j]['DEFAULT'] == 'Y';
							}
						}
						else
						{
							arStructure[cur_site]['CONFIG']['CONFIG'][j]['VALUE'] = obElement.value;

							if (current != null)
							if (arStructure[current]['CONFIG']['CONFIG'][j]['VALUE'] && arStructure[current]['CONFIG']['CONFIG'][j]['VALUE'].length > 0)
								obElement.value = arStructure[current]['CONFIG']['CONFIG'][j]['VALUE'];
							else
								obElement.value = arStructure[current]['CONFIG']['CONFIG'][j]['DEFAULT'];
								
						}
					}
					catch (e)
					{
						alert('Error in config');
					}
				}
			}
		}
		else if (i == 'PROFILES')
		{
			for (var j in arStructure[cur_site]['PROFILES'])
			{
				for (var k in arStructure[cur_site]['PROFILES'][j])
				{
					if (k == 'RESTRICTIONS_WEIGHT' || k == 'RESTRICTIONS_SUM')
					{
						if (arStructure[cur_site]['PROFILES'][j][k].length <= 1)
							arStructure[cur_site]['PROFILES'][j][k] = (k == 'RESTRICTIONS_SUM' ? {0:0,1:0,2:0} : {0:0,1:0});
						
						for (var l in arStructure[cur_site]['PROFILES'][j][k])
						{
							var obElement = FormHandler['HANDLER[PROFILES][' + j + '][' + k + '][' + l + ']'];
							if (obElement)
							{
								try
								{
									arStructure[cur_site]['PROFILES'][j][k][l] = obElement.value;
									if (current != null)
										obElement.value = arStructure[current]['PROFILES'][j][k][l];
								}
								catch (e)
								{
									alert('Error in config');
								}
							}

						}
					}
					else
					{
						var obElement = FormHandler['HANDLER[PROFILES][' + j + '][' + k + ']'];
						if (obElement)
						{
							try
							{
								if (obElement.type == 'checkbox')
								{
									arStructure[cur_site]['PROFILES'][j][k] = obElement.checked ? 'Y' : 'N';
									if (current != null)
										obElement.checked = arStructure[current]['PROFILES'][j][k] == 'Y';
								}
								else
								{
									arStructure[cur_site]['PROFILES'][j][k] = obElement.value;
									if (current != null)
										obElement.value = arStructure[current]['PROFILES'][j][k];
								}
							}
							catch (e)
							{
								alert('Error in config');
							}
						}
					}
				}
			}
		
		}
		else
		{
			var obElement = FormHandler['HANDLER['+ i + ']']
			if (obElement)
			{
				try
				{
					if (obElement.type == 'checkbox')
					{
						arStructure[cur_site][i] = obElement.checked ? 'Y' : 'N';
						if (current != null)
						{
							obElement.checked = arStructure[current][i] == 'Y';
							if (i == 'PROFILE_USE_DEFAULT')
								changeProfiles(obElement.checked);
						}
					}
					else
					{
						arStructure[cur_site][i] = obElement.value;
						
						if (current != null)
							obElement.value = arStructure[current][i];
					}
				}
				catch (e)
				{
					alert('Error');
				}
			}
		}
	}
	
	if (current != null)
	{
		cur_site = current;
		CSHandler.value = current;
	}
	
	CloseWaitWindow();
	
	return;
}

function changeProfiles(flag)
{
	obElement = document.getElementById('PROFILES_DIV');
	obElement.style.display = flag ? 'none' : 'block';
}

function __serialize(obj)
{
	if (typeof(obj) == 'object'/* && obj.constructor == Array*/)
	{
		var str = '', cnt = 0;
		for (var i in obj)
		{
			++cnt;
			str += __serialize(i) + __serialize(obj[i]);
		}
		
		str = "a:" + cnt + ":{" + str + "}";
		
		return str;
	}
	else if (typeof(obj) == 'boolean')
	{
		return 'b:' + (obj ? 1 : 0) + ';';
	}
	else if (null == obj)
	{
		return 'N;'
	}
	else if (Number(obj) == obj && obj != '' && obj != ' ')
	{
		if (Math.floor(obj) == obj)
			return 'i:' + Math.floor(obj) + ';';
		else
			return 'd:' + obj + ';';
	}
	else if(typeof(obj) == 'string')
	{
		obj = obj.replace(/\r\n/g, "\n");
		obj = obj.replace(/\n/g, "\r\n");

		var offset = 0;
		<?if (defined('BX_UTF') && BX_UTF === true):?>
		for (var q = 0, cnt = obj.length; q < cnt; q++)
		{
			if (obj.charCodeAt(q) > 127) offset++;
		}
		<?endif;?>
		
		return 's:' + (offset + obj.length) + ':"' + obj + '";';
	}
}

function prepareData()
{
	selectSite();

	var structure = __serialize(arStructure);
	document.forms.form1.STRUCTURE.value = structure;

	return true;
}
</script>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" name="form1" onSubmit='return prepareData()'>
	
	<input type="hidden" name="lang" value="<?echo LANG ?>" />
	<input type="hidden" name="Update" value="Y" />
	<input type="hidden" name="SID" value="<?echo htmlspecialcharsbx($SID) ?>" />
	<input type="hidden" name="STRUCTURE" value="" />
<?=bitrix_sessid_post()?>
<?
$parentTabControl->Begin();
$parentTabControl->BeginNextTab();
?>
	<tr>
		<td width="50%">
			<label for="USE_DIFF_SITES_SETTINGS"><?=GetMessage('SALE_DH_USE_DIFF_SITES_SETTINGS')?>:</label>
		</td>
		<td width="50%"">
			<input type="checkbox" name="USE_DIFF_SITES_SETTINGS" id="USE_DIFF_SITES_SETTINGS"<?=$bSites ? " checked=\"checked\"" : ""?> onclick="changeSiteList(!this.checked)" value="Y" />
		</td>
	</tr>
	<tr>
		<td>
			<?=GetMessage("SALE_DH_SITES_LIST")?>:
		</td>
		<td><select name="site" id="site_id"<? if(!$bSites) echo " disabled=\"disabled\""; ?> onChange="selectSite(this.value)">
			<?
				for($i = 0; $i < $siteCount; $i++)
					echo "<option value=\"".htmlspecialcharsbx($siteList[$i]["ID"])."\" ".($i == 0 ? "selected=\"selected\"" : "").">".htmlspecialcharsbx($siteList[$i]["NAME"])."</option>";
			?></select><input type="hidden" name="current_site" id="current_site" value="<?=htmlspecialcharsbx($siteList[0]["ID"]);?>" /></td>
	</tr>
	<tr>
		<td colspan="2">
<?
if (strlen($deliveryHint) > 0)
{
	echo BeginNote();
	echo $deliveryHint;
	echo EndNote();
}

$tabControl->Begin();	

// base config tab
$tabControl->BeginNextTab();
?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="edit-table" id="base_params_table">
<tr>
	<td width="40%" class="field-name"><?=GetMessage('SALE_DH_HANDLER_PATH')?></td>
	<td width="60%"><b><?=htmlspecialcharsbx($handlerPath)?></b></td>
</tr>
<tr>
	<td class="field-name"><?=GetMessage('SALE_DH_HANDLER_ACTIVE')?></td>
	<td><input type="checkbox" name="HANDLER[ACTIVE]" value="Y" <?=($arDeliveryInfo[$SITE_ID]["ACTIVE"] == "Y" ? "checked=\"checked\"" : "")?> /></td>
</tr>
<tr>
	<td class="field-name"><?=GetMessage('SALE_DH_HANDLER_SORT')?></td>
	<td><input type="text" name="HANDLER[SORT]" value="<?=intval($arDeliveryInfo[$SITE_ID]["SORT"])?>" size="3" /></td>
</tr>
<tr>
	<td class="field-name"><?=GetMessage('SALE_DH_HANDLER_NAME')?></td>
	<td><input type="text" name="HANDLER[NAME]" value="<?=htmlspecialcharsbx($arDeliveryInfo[$SITE_ID]["NAME"])?>" /></td>
</tr>
<tr>
	<td valign="top" class="field-name"><?=GetMessage('SALE_DH_HANDLER_DESCRIPTION')?></td>
	<td valign="top"><textarea name="HANDLER[DESCRIPTION]" cols="40" rows="5"><?=htmlspecialcharsbx($arDeliveryInfo[$SITE_ID]["DESCRIPTION"])?></textarea></td>
</tr>
<tr>
	<td class="field-name"><?=GetMessage('SALE_DH_HANDLER_TAX_RATE')?></td>
	<td><input type="text" name="HANDLER[TAX_RATE]" value="<?=doubleval($arDeliveryInfo[$SITE_ID]["TAX_RATE"])?>" size="3" />%</td>
</tr>
</table>
<?
// config tabs
foreach ($arDeliveryInfo[$SITE_ID]["CONFIG"]["CONFIG_GROUPS"] as $group => $arConfigGroup)
{
	$tabControl->BeginNextTab();
?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="edit-table" id="params_<?=htmlspecialcharsbx($group)?>_table">
<?
	foreach ($arDeliveryInfo[$SITE_ID]["CONFIG"]["CONFIG"] as $config_id => $arConfig)
	{
		if ($arConfig["GROUP"] == $group)
		{
?>
<tr>
	<td class="field-name" <?if ($arConfig["TYPE"] == "MULTISELECT"):?>valign="top" <?endif;?>width="40%" align="right"><?=htmlspecialcharsbx($arConfig["TITLE"])?>:</td>
	<td valign="top" width="60%">
<?
	switch ($arConfig["TYPE"])
	{
		case "CHECKBOX":
			?>
			<input type="checkbox" name="HANDLER[CONFIG][<?=htmlspecialcharsbx($config_id)?>]" value="Y" <?=($arConfigValues[$config_id] == "Y" ? "checked=\"checked\"" : "")?> />
			<?
		break;
		
		case "RADIO":
			foreach ($arConfig["VALUES"] as $value => $title):
		?>
				<input type="radio" id="hc_<?=htmlspecialcharsbx($config_id)?>_<?=htmlspecialcharsEx($value)?>" name="HANDLER[CONFIG][<?=htmlspecialcharsbx($config_id)?>]" value="<?=htmlspecialcharsEx($value)?>"<?=($value == $arConfigValues[$config_id] ? " checked=\"checked\"" : "")?> /><label for="hc_<?=htmlspecialcharsbx($config_id)?>_<?=htmlspecialcharsEx($value)?>"><?=htmlspecialcharsEx($title)?></label><br />
		<?
			endforeach;
		
		break;
		
		case "PASSWORD":
		?>
			<input type="password" name="HANDLER[CONFIG][<?=htmlspecialcharsbx($config_id)?>]" value="<?=htmlspecialcharsbx($arConfigValues[$config_id])?>" />
		<?
		break;
	
		case "DROPDOWN":
		case "MULTISELECT":
		?>
			<select name="HANDLER[CONFIG][<?=htmlspecialcharsbx($config_id)?>]"<?=$arConfig["TYPE"] == "MULTISELECT" ? "multiple=\"multiple\"" : ""?>>
		<?

			foreach ($arConfig["VALUES"] as $value => $title):
		?>
				<option value="<?=htmlspecialcharsEx($value)?>"<?=($value == $arConfigValues[$config_id] ? " selected=\"selected\"" : "")?>><?=htmlspecialcharsEx($title)?></option>
		<?
			endforeach;
		?>
			
			</select>
		<?
		break;
	
		default:
		?>
			<input type="text" name="HANDLER[CONFIG][<?=htmlspecialcharsbx($config_id)?>]" value="<?=htmlspecialcharsbx($arConfigValues[$config_id])?>" />
		<?
	}

?>	
		</td>
	</tr>
<?
		}
	}
?>
</table>
<?
}

?>

<?
//profiles tab
$tabControl->BeginNextTab();
?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="edit-table" id="profiles_table">
<tr>
	<td valign="top" width="40%" class="field-name"><label for="profile_use_default"><?=GetMessage('SALE_DH_PROFILE_USE_DEFAULT')?></label></td>
	<td valign="top"><input type="checkbox" id="profile_use_default" name="HANDLER[PROFILE_USE_DEFAULT]" value="Y" <?=($arDeliveryInfo[$SITE_ID]["PROFILE_USE_DEFAULT"] == "Y" ? "checked=\"checked\"" : "")?> onclick="changeProfiles(this.checked)" /></td>
</tr>
<?
//echo "<tr><td valign=\"top\" colspan=\"2\"><pre>"; print_r($arDeliveryInfo[$SITE_ID]["PROFILES"]); echo "</pre></td></tr>";

$weight_unit = COption::GetOptionString('catalog', 'weight_unit', GetMessage('SALE_DH_WEIGHT_UNIT_DEFAULT'));
$weight_koef = COption::GetOptionString('catalog', 'weight_koef', 1);

CModule::IncludeModule('currency');

?>
	<tr>
		<td valign="top" colspan="2"><div id="PROFILES_DIV" style="display: <?=($arDeliveryInfo[$SITE_ID]["PROFILE_USE_DEFAULT"] == "Y" ? "none" : "block")?>;">
<?

if (is_array($arDeliveryInfo[$SITE_ID]["PROFILES"]))
{
	foreach ($arDeliveryInfo[$SITE_ID]["PROFILES"] as $profile_id => $arProfile)
	{
?>
		<table class="internal" cellpadding="0" cellspacing="2" border="0" width="100%" align="center" id="profile_<?=htmlspecialcharsbx($profile_id)?>">
			<tr class="heading">
				<td width="40%">&nbsp;</td>
				<td width="60%">&nbsp;</td>
			</tr>
			<tr>
				<td align="right"><?=GetMessage('SALE_DH_PROFILE_ACTIVE')?></td>
				<td valign="top"><input type="checkbox" name="HANDLER[PROFILES][<?=htmlspecialcharsbx($profile_id)?>][ACTIVE]" value="Y" <?if ($arProfile['ACTIVE'] == 'Y'):?>checked="checked"<?endif?> /></td>
			</tr>
			<tr>
				<td align="right"><?=GetMessage('SALE_DH_PROFILE_TITLE')?></td>
				<td valign="top"><input type="text" name="HANDLER[PROFILES][<?=htmlspecialcharsbx($profile_id)?>][TITLE]" value="<?=htmlspecialcharsbx($arProfile["TITLE"])?>" size="25" /></td>
			</tr>
			<tr>
				<td align="right" valign="top"><?=GetMessage('SALE_DH_PROFILE_DESCRIPTION')?></td>
				<td valign="top"><textarea name="HANDLER[PROFILES][<?=htmlspecialcharsbx($profile_id)?>][DESCRIPTION]" cols="40" rows="5"><?=htmlspecialcharsbx($arProfile["DESCRIPTION"])?></textarea></td>
			</tr>	

			<tr>
				<td align="right"><?=GetMessage('SALE_DH_PROFILE_WEIGHT_RESTRICTIONS')?></td>
				<td valign="top">
					<?=GetMessage('SALE_DH_PROFILE_WEIGHT_RESTRICTIONS_FROM')?>
					<input type="text" name="HANDLER[PROFILES][<?=htmlspecialcharsbx($profile_id)?>][RESTRICTIONS_WEIGHT][0]" value="<?=number_format($arProfile['RESTRICTIONS_WEIGHT'][0] / $weight_koef, 2, '.', '')?>" size="8" /><?=htmlspecialcharsbx($weight_unit)?>
					<?=GetMessage('SALE_DH_PROFILE_WEIGHT_RESTRICTIONS_TO')?>
					<input type="text" name="HANDLER[PROFILES][<?=htmlspecialcharsbx($profile_id)?>][RESTRICTIONS_WEIGHT][1]" value="<?=number_format($arProfile['RESTRICTIONS_WEIGHT'][1] / $weight_koef, 2, '.', '')?>" size="8" /><?=htmlspecialcharsbx($weight_unit)?>
				</td>
			</tr>

			<tr>
				<td align="right"><?=GetMessage('SALE_DH_PROFILE_SUM_RESTRICTIONS')?></td>
				<td valign="top">
					<?=GetMessage('SALE_DH_PROFILE_SUM_RESTRICTIONS_FROM')?>
					<input type="text" name="HANDLER[PROFILES][<?=htmlspecialcharsbx($profile_id)?>][RESTRICTIONS_SUM][1]" value="<?=number_format(floatval($arProfile['RESTRICTIONS_SUM'][1]), 2, '.', '')?>" size="8" />&nbsp;
					<?=GetMessage('SALE_DH_PROFILE_SUM_RESTRICTIONS_TO')?>
					<input type="text" name="HANDLER[PROFILES][<?=htmlspecialcharsbx($profile_id)?>][RESTRICTIONS_SUM][2]" value="<?=number_format(floatval($arProfile['RESTRICTIONS_SUM'][2]), 2, '.', '')?>" size="8" />
					<?=GetMessage('SALE_DH_PROFILE_SUM_RESTRICTIONS_CURRENCY')?>
					<?=CCurrency::SelectBox('HANDLER[PROFILES]['.htmlspecialcharsbx($profile_id).'][RESTRICTIONS_SUM][0]', $arDeliveryInfo[$SITE_ID]["BASE_CURRENCY"])?>
				</td>
			</tr>
		</table><br />
<?
	}
}

?>
</td>
	</tr>
</table>
<?
$tabControl->End();
?>
	</td>
</tr>
<?
$parentTabControl->Buttons(
		array(
				"disabled" => ($saleModulePermissions < "W"),
				"back_url" => "/bitrix/admin/sale_delivery_handlers.php?lang=".LANG,
			)
	);
?>
<?
$parentTabControl->End();
?>
</form>

<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>