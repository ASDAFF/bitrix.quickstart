<?
//<title>CSV (new)</title>
__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/export_setup_templ.php"));
__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/export_yandex.php"));

global $APPLICATION, $USER;

$NUM_CATALOG_LEVELS = intval(COption::GetOptionString("catalog", "num_catalog_levels", 3));
if (0 >= $NUM_CATALOG_LEVELS)
	$NUM_CATALOG_LEVELS = 3;

$arSetupErrors = array();

$strCatalogDefaultFolder = COption::GetOptionString("catalog", "export_default_path", CATALOG_DEFAULT_EXPORT_PATH);

$STEP = intval($STEP);
if (0 >= $STEP)
	$STEP = 1;

$ACTION = strval($ACTION);

//********************  ACTIONS  **************************************//
if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 1)
{
	if (isset($arOldSetupVars['IBLOCK_ID']))
		$IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
}
if ($STEP > 1)
{
	$IBLOCK_ID = intval($IBLOCK_ID);
	if ($IBLOCK_ID <= 0)
	{
		$arSetupErrors[] = GetMessage("CATI_NO_IBLOCK");
	}
	else
	{
		$rsIBlocks = CIBlock::GetList(array(),array('IBLOCK_ID' => $IBLOCK_ID,'CHECK_PERMISSIONS' => 'N'));
		if (!($arIBlock = $rsIBlocks->Fetch()))
		{
			$arSetupErrors[] = GetMessage("CATI_NO_IBLOCK");
		}
		elseif (!CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, 'iblock_admin_display'))
		{
			$arSetupErrors[] = str_replace('#IBLOCK_ID#', $IBLOCK_ID, GetMessage('CET_ERROR_IBLOCK_PERM'));
		}
	}

	if (!empty($arSetupErrors))
	{
		$STEP = 1;
	}
}

if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 2)
{
	if (isset($arOldSetupVars['IBLOCK_ID']) && $arOldSetupVars['IBLOCK_ID'] == $IBLOCK_ID)
	{
		if (isset($arOldSetupVars['field_needed']))
			$field_needed = $arOldSetupVars['field_needed'];
		if (isset($arOldSetupVars['field_num']))
			$field_num = $arOldSetupVars['field_num'];
		if (isset($arOldSetupVars['field_code']))
			$field_code = $arOldSetupVars['field_code'];
	}
	if (isset($arOldSetupVars['fields_type']))
		$fields_type = $arOldSetupVars['fields_type'];
	if (isset($arOldSetupVars['delimiter_r']))
		$delimiter_r = $arOldSetupVars['delimiter_r'];
	if (isset($arOldSetupVars['delimiter_r_char']))
		$delimiter_r_char = $arOldSetupVars['delimiter_r_char'];
	if ($arOldSetupVars['delimiter_other_r'])
		$delimiter_other_r = $arOldSetupVars['delimiter_other_r'];
	if (isset($arOldSetupVars['SETUP_FILE_NAME']))
		$SETUP_FILE_NAME = str_replace($strCatalogDefaultFolder, '', $arOldSetupVars['SETUP_FILE_NAME']);
	if (isset($arOldSetupVars['SETUP_PROFILE_NAME']))
		$SETUP_PROFILE_NAME = $arOldSetupVars['SETUP_PROFILE_NAME'];
	if (isset($arOldSetupVars['first_line_names']))
		$first_line_names = $arOldSetupVars['first_line_names'];
	if (isset($arOldSetupVars['export_files']))
		$export_files = $arOldSetupVars['export_files'];
	if (isset($arOldSetupVars['export_from_clouds']))
		$export_from_clouds = $arOldSetupVars['export_from_clouds'];
}

if ($STEP>2)
{
	if (!isset($fields_type) || ($fields_type!="F" && $fields_type!="R"))
	{
		$arSetupErrors[] = GetMessage("CATI_NO_FORMAT");
	}

	$delimiter_r_char = "";
	if (isset($delimiter_r))
	{
		switch ($delimiter_r)
		{
			case "TAB":
				$delimiter_r_char = "\t";
				break;
			case "ZPT":
				$delimiter_r_char = ",";
				break;
			case "SPS":
				$delimiter_r_char = " ";
				break;
			case "OTR":
				$delimiter_r_char = (isset($delimiter_other_r) ? substr($delimiter_other_r, 0, 1) : '');
				break;
			case "TZP":
				$delimiter_r_char = ";";
				break;
		}
	}

	if (strlen($delimiter_r_char) != 1)
	{
		$arSetupErrors[] = GetMessage("CATI_NO_DELIMITER");
	}

	if (!isset($SETUP_FILE_NAME) || strlen($SETUP_FILE_NAME)<=0)
	{
		$arSetupErrors[] = GetMessage("CATI_NO_SAVE_FILE");
	}

	if (empty($arSetupErrors))
	{
		$SETUP_FILE_NAME = str_replace('//','/',$strCatalogDefaultFolder.Rel2Abs("/", $SETUP_FILE_NAME));
		if (preg_match(BX_CATALOG_FILENAME_REG, $SETUP_FILE_NAME))
		{
			$arSetupErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
		}
		elseif ($strCatalogDefaultFolder == $SETUP_FILE_NAME)
		{
			$arSetupErrors[] = GetMessage("CATI_NO_SAVE_FILE");
		}
	}

	if (empty($arSetupErrors))
	{
		if (strtolower(substr($SETUP_FILE_NAME, strlen($SETUP_FILE_NAME)-4)) != ".csv")
			$SETUP_FILE_NAME .= ".csv";
		if (HasScriptExtension($SETUP_FILE_NAME))
		{
			$arSetupErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME_EXTENTIONS");
		}
	}

	if (empty($arSetupErrors))
	{
		if ($APPLICATION->GetFileAccessPermission($SETUP_FILE_NAME) < "W")
		{
			$arSetupErrors[] = str_replace("#FILE#", $SETUP_FILE_NAME, GetMessage('CATI_NO_RIGHTS_FILE'));
		}
		else
		{
			CheckDirPath($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME);
			if (!($fp = fopen($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, "wb")))
			{
				$arSetupErrors[] = GetMessage("CATI_CANNOT_CREATE_FILE");
			}
			@fclose($fp);
		}
	}

	$bFieldsPres = (!empty($field_needed) && is_array($field_needed) && in_array('Y', $field_needed));
	if ($bFieldsPres && (empty($field_code) || !is_array($field_code)))
	{
		$bFieldsPres = false;
	}
	if (!$bFieldsPres)
	{
		$arSetupErrors[] = GetMessage("CATI_NO_FIELDS");
	}

	if (($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && (!isset($SETUP_PROFILE_NAME) || strlen($SETUP_PROFILE_NAME)<=0))
	{
		$arSetupErrors[] = GetMessage("CET_ERROR_NO_NAME");
	}

	if (!empty($arSetupErrors))
	{
		$STEP = 2;
	}
}
//********************  END ACTIONS  **********************************//

if (!empty($arSetupErrors))
	echo ShowError(implode('<br />', $arSetupErrors));
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage(); ?>" enctype="multipart/form-data" name="dataload">
<?
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("CAT_ADM_CSV_EXP_TAB1"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_EXP_TAB1_TITLE")),
	array("DIV" => "edit2", "TAB" => GetMessage("CAT_ADM_CSV_EXP_TAB2"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_EXP_TAB2_TITLE")),
	array("DIV" => "edit3", "TAB" => GetMessage("CAT_ADM_CSV_EXP_TAB3"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_EXP_TAB3_TITLE")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
$tabControl->Begin();

$tabControl->BeginNextTab();

if ($STEP == 1)
{
	?><tr class="heading">
		<td colspan="2"><?echo GetMessage("CATI_DATA_EXPORT") ?></td>
	</tr>
	<tr>
		<td valign="top" width="40%"><? echo GetMessage("CAT_ADM_CSV_EXP_IBLOCK_ID"); ?>:</td>
		<td valign="top" width="60%"><?
			if (!isset($IBLOCK_ID))
				$IBLOCK_ID = 0;
			echo GetIBlockDropDownListEx($IBLOCK_ID, 'IBLOCK_TYPE_ID', 'IBLOCK_ID',array('CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'));
		?></td>
	</tr><?
}

$tabControl->EndTab();

$tabControl->BeginNextTab();

if ($STEP == 2)
{
	?><tr class="heading">
		<td colspan="2"><?echo GetMessage("CATI_FORMAT_PROPS") ?></td>
	</tr>
	<tr>
		<td valign="top" width="40%"><?echo GetMessage("CATI_DELIMITERS") ?>:</td>
		<td valign="top" width="60%"><?
			if (!isset($delimiter_r) || empty($delimiter_r))
				$delimiter_r = 'TZP';
			?><input type="hidden" name="fields_type" value="R">
			<input type="radio" name="delimiter_r" value="TZP" <?if ($delimiter_r=="TZP") echo "checked"?>><?echo GetMessage("CATI_TZP") ?><br>
			<input type="radio" name="delimiter_r" value="ZPT" <?if ($delimiter_r=="ZPT") echo "checked"?>><?echo GetMessage("CATI_ZPT") ?><br>
			<input type="radio" name="delimiter_r" value="TAB" <?if ($delimiter_r=="TAB") echo "checked"?>><?echo GetMessage("CATI_TAB") ?><br>
			<input type="radio" name="delimiter_r" value="SPS" <?if ($delimiter_r=="SPS") echo "checked"?>><?echo GetMessage("CATI_SPS") ?>
		</td>
	</tr>
	<tr>
		<td valign="top" width="40%"><label for="first_line_names_Y"><?echo GetMessage("CATI_FIRST_LINE_NAMES") ?>:</label></td>
		<td valign="top" width="60%"><?
			if (!isset($first_line_names))
				$first_line_names = 'Y';
			?><input type="hidden" name="first_line_names" id="first_line_names_N" value="N">
			<input type="checkbox" name="first_line_names" id="first_line_names_Y" value="Y" <?if ($first_line_names=="Y") echo "checked"?>>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("CATI_FIELDS") ?></td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="internal">
				<?
				$boolCatalog = false;
				$arCatalog = CCatalog::GetByID($IBLOCK_ID);
				if (!empty($arCatalog))
					$boolCatalog = true;

				$arAvailFields = array();
				$intCount = 0;
				$boolSep = true;
				$strVal = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailProdFields);
				$arVal = explode(",", $strVal);
				foreach ($arCatalogAvailProdFields as &$arOneCatalogAvailProdFields_tmp)
				{
					$mxKey = array_search($arOneCatalogAvailProdFields_tmp['value'],$arVal);
					if (false !== $mxKey)
					{
						$arAvailFields[$intCount] = array(
							"value"=>$arOneCatalogAvailProdFields_tmp["value"],
							"name"=>$arOneCatalogAvailProdFields_tmp["name"],
							'sort' => ($intCount+1)*10,
						);
						if ($boolSep)
						{
							$arAvailFields[$intCount]['SEP'] = GetMessage('CAT_ADM_CSV_EXP_SEP_ELEMENTS');
							$boolSep = false;
						}
						$intCount++;
					}
				}
				if (isset($arOneCatalogAvailProdFields_tmp))
					unset($arOneCatalogAvailProdFields_tmp);

				$properties = CIBlockProperty::GetList(array("SORT"=>"ASC", "ID"=>"ASC"), array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y", 'CHECK_PERMISSIONS' => 'N'));
				while ($prop_fields = $properties->Fetch())
				{
					$arAvailFields[$intCount] = array(
						"value"=>"IP_PROP".$prop_fields["ID"],
						"name"=>GetMessage("CATI_FI_PROPS").' "'.$prop_fields["NAME"].'"'.' ['.(''!= trim($prop_fields["CODE"]) ? $prop_fields["CODE"] : $prop_fields["ID"]).']',
						'sort' => ($intCount+1)*10,
					);
					if ($boolSep)
					{
						$arAvailFields[$intCount]['SEP'] = GetMessage('CAT_ADM_CSV_EXP_SEP_ELEMENTS');
						$boolSep = false;
					}
					$intCount++;
				}

				$boolSep = true;
				$strVal = COption::GetOptionString("catalog", "allowed_group_fields", $defCatalogAvailGroupFields);
				$arVal = explode(",", $strVal);
				for ($k_old = -1, $k = 0; $k < $NUM_CATALOG_LEVELS; $k++)
				{
					foreach ($arCatalogAvailGroupFields as &$arOnerCatalogAvailGroupFields)
					{
						$mxKey = array_search($arOnerCatalogAvailGroupFields['value'],$arVal);
						if (false !== $mxKey)
						{
							$arAvailFields[$intCount] = array(
								"value"=>$arOnerCatalogAvailGroupFields["value"].$k,
								"name"=> $arOnerCatalogAvailGroupFields["name"],
								'sort' => ($intCount+1)*10,
							);
							if ($boolSep)
							{
								$arAvailFields[$intCount]['SEP'] = GetMessage('CAT_ADM_CSV_EXP_SEP_SECTIONS');
								$boolSep = false;
							}
							if ($k_old != $k)
							{
								$arAvailFields[$intCount]['SUB_SEP'] = str_replace('#LEVEL#',($k+1),GetMessage("CAT_ADM_CSV_EXP_SECTION_LEVEL"));
								$k_old = $k;
							}
							$intCount++;
						}
					}
					if (isset($arOnerCatalogAvailGroupFields))
						unset($arOnerCatalogAvailGroupFields);
				}

				if ($boolCatalog)
				{
					$boolSep = true;
					$strVal = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailPriceFields);
					$arVal = explode(",", $strVal);
					foreach ($arCatalogAvailPriceFields as &$arOneCatalogAvailProdFields_tmp)
					{
						$mxKey = array_search($arOneCatalogAvailProdFields_tmp['value'],$arVal);
						if (false !== $mxKey)
						{
							$arAvailFields[$intCount] = array(
								"value"=>$arOneCatalogAvailProdFields_tmp["value"],
								"name"=>$arOneCatalogAvailProdFields_tmp["name"],
								'sort' => ($intCount+1)*10,
							);
							if ($boolSep)
							{
								$arAvailFields[$intCount]['SEP'] = GetMessage('CAT_ADM_CSV_EXP_SEP_PRODUCT');
								$boolSep = false;
							}
							$intCount++;
						}
					}
					if (isset($arOneCatalogAvailProdFields_tmp))
						unset($arOneCatalogAvailProdFields_tmp);

					$boolSep = true;
					$strVal = $defCatalogAvailQuantityFields;
					$arVal = explode(",", $strVal);
					foreach ($arCatalogAvailQuantityFields as &$arOneCatalogAvailQuantityFields)
					{
						$mxKey = array_search($arOneCatalogAvailQuantityFields['value'],$arVal);
						if (false !== $mxKey)
						{
							$arAvailFields[$intCount] = array(
								"value"=>$arOneCatalogAvailQuantityFields["value"],
								"name"=>$arOneCatalogAvailQuantityFields["name"],
								'sort' => ($intCount+1)*10,
							);
							if ($boolSep)
							{
								$arAvailFields[$intCount]['SEP'] = GetMessage('CAT_ADM_CSV_EXP_SEP_PRICES');
								$boolSep = false;
							}
							$intCount++;
						}
					}
					if (isset($arOneCatalogAvailQuantityFields))
						unset($arOneCatalogAvailQuantityFields);

					$strVal = COption::GetOptionString("catalog", "allowed_price_fields", $defCatalogAvailValueFields);
					$arVal = explode(",", $strVal);
					$db_prgr = CCatalogGroup::GetList(array("SORT" => "ASC"), array());
					while ($prgr = $db_prgr->Fetch())
					{
						foreach ($arCatalogAvailValueFields as &$arOneCatalogAvailValueFields)
						{
							$mxKey = array_search($arOneCatalogAvailValueFields['value'],$arVal);
							if (false !== $mxKey)
							{
								$strName = ($prgr['NAME_LANG'] ?
									str_replace(array('#TYPE#','#NAME#'),array($prgr["NAME"],$prgr['NAME_LANG']),GetMessage('EST_PRICE_TYPE2')):
									str_replace("#TYPE#", $prgr["NAME"], GetMessage("EST_PRICE_TYPE"))
								);
								$arAvailFields[$intCount] = array(
									"value" => $arOneCatalogAvailValueFields['value']."_".$prgr["ID"],
									"name" => $strName.": ".$arOneCatalogAvailValueFields["name"],
									'sort' => ($intCount+1)*10,
								);
								if ($boolSep)
								{
									$arAvailFields[$intCount]['SEP'] = GetMessage('CAT_ADM_CSV_EXP_SEP_PRICES');
									$boolSep = false;
								}
								$intCount++;
							}
						}
						if (isset($arOneCatalogAvailValueFields))
							unset($arOneCatalogAvailValueFields);
					}
				}

				$intCountAvailFields = $intCount;
				$intCountChecked = 0;
				$arCheckID = array();
				$boolAll = true;
				if (!empty($field_code) && is_array($field_code))
				{
					foreach ($arAvailFields as $i => $arOneAvailField)
					{
						$intSort = 0;
						$key = array_search($arOneAvailField['value'], $field_code);
						if (false !== $key)
						{
							if (isset($field_needed[$key]) && 'Y' == $field_needed[$key])
							{
								$boolAll = false;
								$arCheckID[] = $arOneAvailField['value'];
								$intCountChecked++;
							}
							if (isset($field_num[$key]) && 0 < intval($field_num[$key]))
								$intSort = intval($field_num[$key]);
						}
						if (0 < $intSort)
							$arAvailFields[$i]['sort'] = $intSort;
					}
				}
				if ($boolAll)
					$intCountChecked = $intCountAvailFields;
				?><tr class="heading">
					<td valign="middle" align="left" style="text-align: left;">
						<input style="vertical-align: middle;" type="checkbox" name="field_needed_all" id="field_needed_all" value="Y" onclick="checkAll(this,<? echo $intCountAvailFields; ?>);"<? echo ($boolAll || ($intCountChecked == $intCountAvailFields) ? ' checked' : ''); ?>>&nbsp;
						<b><?echo GetMessage("CATI_FIELDS_NEEDED") ?></b></td>
					<td valign="middle" align="center"><b><?echo GetMessage("CATI_FIELDS_NAMES") ?></b></td>
					<td valign="middle" align="center"><b><?echo GetMessage("CATI_FIELDS_SORTING") ?></b></td>
				</tr><?
				foreach ($arAvailFields as $i => $arOneAvailField)
				{
					if (!empty($arOneAvailField['SEP']))
					{
						?><tr><td colspan="3" valign="middle" align="center"><b><? echo htmlspecialcharsbx($arOneAvailField['SEP']); ?></b></td></tr><?
					}
					if (!empty($arOneAvailField['SUB_SEP']))
					{
						?><tr><td>&nbsp;</td><td valign="middle" align="left"><b><? echo htmlspecialcharsbx($arOneAvailField['SUB_SEP']); ?></b></td><td>&nbsp;</td></tr><?
					}
					?>
					<tr>
				<td valign="top" align="left"><input type="checkbox" name="field_needed[<? echo $i; ?>]" id="field_needed_<? echo $i; ?>"
					<?if ($boolAll || in_array($arOneAvailField['value'],$arCheckID)) echo "checked"; ?>
					value="Y" onclick="checkOne(this,<? echo $intCountAvailFields; ?>);"></td>
				<td valign="middle" align="left">
								<?if ($i<2) echo "<b>";?>
								<?echo htmlspecialcharsbx($arOneAvailField["name"]); ?>
								<?if ($i<2) echo "</b>";?>
							</td>
				<td valign="top" align="center">
							<?if ($i<2) echo "<b>";?>
							<input type="text" class="typeinput" name="field_num[<?echo $i ?>]" value="<?echo $arOneAvailField['sort']; ?>" size="4"> <input type="hidden" name="field_code[<?echo $i ?>]"
					value="<?echo htmlspecialcharsbx($arOneAvailField["value"]) ?>">
							<?if ($i<2) echo "</b>";?>
						</td>
			</tr>
					<?
				}
				?>
			</table>
			<input type="hidden" name="count_checked" id="count_checked" value="<? echo $intCountChecked; ?>">
			<script type="text/javascript">
			function checkAll(obj,cnt)
			{
				var boolCheck = obj.checked;
				for (i = 0; i < cnt; i++)
				{
					BX('field_needed_'+i).checked = boolCheck;
				}
				BX('count_checked').value = (boolCheck ? cnt : 0);
			}
			function checkOne(obj,cnt)
			{
				var boolCheck = obj.checked;
				var intCurrent = parseInt(BX('count_checked').value);
				intCurrent += (boolCheck ? 1 : -1);
				BX('field_needed_all').checked = (intCurrent < cnt ? false : true);
				BX('count_checked').value = intCurrent;
			}
			</script>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><? echo GetMessage('CAT_ADM_CSV_EXP_ADD_SETTINGS') ?></td>
	</tr>
	<tr>
		<td valing="top" width="40%"><label for="export_files"><? echo GetMessage('CAT_ADM_CSV_EXP_EXPORT_FILES'); ?>:</label></td>
		<td valign="top" width="60%">
			<input type="hidden" name="export_files" id="export_files_N" value="N">
			<input type="checkbox" name="export_files" id="export_files_Y" value="Y" <? echo (isset($export_files) && $export_files == 'Y' ? 'checked' : ''); ?>>
		</td>
	</tr><?
	$boolHaveClouds = false;
	if ($boolHaveClouds)
	{
		?>
	<tr>
		<td valing="top" width="40%"><label for="export_from_clouds"><? echo GetMessage('CAT_ADM_CSV_EXP_EXPORT_FROM_CLOUDS'); ?>:</label></td>
		<td valign="top" width="60%">
			<input type="hidden" name="export_from_clouds" id="export_from_clouds_N" value="N">
			<input type="checkbox" name="export_from_clouds" id="export_from_clouds_Y" value="Y" <? echo (isset($export_from_clouds) && $export_from_clouds == 'Y' ? 'checked' : ''); ?>>
		</td>
	</tr><?
	}
	else
	{
		?><input type="hidden" name="export_from_clouds" id="export_from_clouds_N" value="N"><?
	}
	?>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("CATI_DATA_FILE_NAME") ?></td>
	</tr>
	<tr>
		<td valign="top" width="40%"><?echo GetMessage("CATI_DATA_FILE_NAME1") ?>:</td>
		<td valign="top" width="60%"><b><? echo htmlspecialcharsex($strCatalogDefaultFolder); ?></b>
			<input type="text" class="typeinput" name="SETUP_FILE_NAME" size="40" value="<?echo htmlspecialcharsbx(strlen($SETUP_FILE_NAME)>0 ? str_replace($strCatalogDefaultFolder, '', $SETUP_FILE_NAME): "export_file_".mt_rand(0, 999999).".csv");?>"><br>
		<small><?echo GetMessage("CATI_DATA_FILE_NAME1_DESC") ?></small>
		</td>
	</tr>

	<?if ($ACTION == "EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')
	{
	?><tr class="heading">
		<td colspan="2"><?echo GetMessage("CATI_SAVE_SCHEME") ?></td>
	</tr>
	<tr>
		<td valign="top" width="40%"><?echo GetMessage("CATI_SSCHEME_NAME") ?>:</td>
		<td valign="top" width="60%"><input type="text" class="typeinput" name="SETUP_PROFILE_NAME" size="40"
			value="<?echo htmlspecialcharsbx($SETUP_PROFILE_NAME)?>"></td>
	</tr><?
	}
}

$tabControl->EndTab();

$tabControl->BeginNextTab();

if ($STEP == 3)
{
	$FINITE = true;
}

$tabControl->EndTab();

$tabControl->Buttons();

?><? echo bitrix_sessid_post();?><?
if ($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')
{
	?><input type="hidden" name="PROFILE_ID" value="<? echo intval($PROFILE_ID); ?>"><?
}

if ($STEP < 3)
{
	?><input type="hidden" name="STEP" value="<? echo intval($STEP)+1; ?>">
	<input type="hidden" name="lang" value="<? echo LANGUAGE_ID; ?>">
	<input type="hidden" name="ACT_FILE" value="<? echo htmlspecialcharsbx($_REQUEST["ACT_FILE"]); ?>">
	<input type="hidden" name="ACTION" value="<? echo htmlspecialcharsbx($ACTION); ?>">
	<?if ($STEP > 1)
	{
		?><input type="hidden" name="IBLOCK_ID" value="<? echo intval($IBLOCK_ID); ?>">
		<input type="hidden" name="SETUP_FIELDS_LIST" value="IBLOCK_ID,SETUP_FILE_NAME,fields_type,delimiter_r,delimiter_other_r,first_line_names,field_needed,field_num,field_code,export_files,export_from_clouds"><?
	}
	if ($STEP > 1)
	{
		?><input type="submit" class="button" name="backButton" value="&lt;&lt; <?echo GetMessage("CATI_BACK") ?>"><?
	}
	?><input type="submit" class="button" value="<?echo ($STEP == 2)?(($ACTION == "EXPORT")?GetMessage("CATI_NEXT_STEP_F"):GetMessage("CET_SAVE")):GetMessage("CATI_NEXT_STEP")." &gt;&gt;" ?>" name="submit_btn"><?
}

$tabControl->End();

?></form>
<script type="text/javascript">
<?if ($STEP < 2):?>
tabControl.SelectTab("edit1");
tabControl.DisableTab("edit2");
tabControl.DisableTab("edit3");
<?elseif ($STEP == 2):?>
tabControl.SelectTab("edit2");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit3");
<?elseif ($STEP == 3):?>
tabControl.SelectTab("edit3");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit2");
<?endif;?>
</script>