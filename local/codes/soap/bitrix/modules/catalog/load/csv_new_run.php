<?
//<title>CSV Export (new)</title>
__IncludeLang(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/data_export.php"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");

global $USER;
$bTmpUserCreated = false;
if (!isset($USER) || !(($USER instanceof CUser) && ('CUser' == get_class($USER))))
{
	$bTmpUserCreated = true;
	if (isset($USER))
	{
		$USER_TMP = $USER;
		unset($USER);
	}

	$USER = new CUser();
}

CCatalogDiscountSave::Disable();

if (!function_exists('__sortCSVOrder'))
{
	function __sortCSVOrder($a, $b)
	{
		if ($a['SORT'] == $b['SORT'])
		{
			return ($a['ID'] < $b['ID'] ? -1 : 1);
		}
		else
		{
			return ($a['SORT'] < $b['SORT'] ? -1 : 1);
		}
	}
}
if (!function_exists('__CSVArrayMultiply'))
{
	function __CSVArrayMultiply(&$arResult, $arTuple, $arTemp = array())
	{
		if (empty($arTuple))
			$arResult[] = $arTemp;
		else
		{
			$head = array_shift($arTuple);
			$arTemp[] = false;
			if (is_array($head))
			{
				if (empty($head))
				{
					$arTemp[count($arTemp)-1] = "";
					__CSVArrayMultiply($arResult, $arTuple, $arTemp);
				}
				else
				{
					foreach ($head as &$value)
					{
						$arTemp[count($arTemp)-1] = $value;
						__CSVArrayMultiply($arResult, $arTuple, $arTemp);
					}
					if (isset($value))
						unset($value);
				}
			}
			else
			{
				$arTemp[count($arTemp)-1] = $head;
				__CSVArrayMultiply($arResult, $arTuple, $arTemp);
			}
		}
	}
}

if (!function_exists('__CSVExportFile'))
{
	function __CSVExportFile($intFileID, $strExportPath, $strFilePath, $strExportFromClouds = 'Y')
	{
		if ('Y' != $strExportFromClouds)
			$strExportFromClouds = 'N';

		$arFile = CFile::GetFileArray($intFileID);
		if ($arFile)
		{
			if ('N' == $strExportFromClouds && 0 < $arFile["HANDLER_ID"])
			{
				return serialize($arFile);
			}
			else
			{
				$arTempFile = CFile::MakeFileArray($intFileID);
				if (isset($arTempFile["tmp_name"]) && $arTempFile["tmp_name"] != "")
				{
					$strFile = $arFile["SUBDIR"]."/".$arFile["FILE_NAME"];
					$strNewFile = str_replace("//", "/", $strExportPath.$strFilePath.$strFile);
						CheckDirPath($_SERVER['DOCUMENT_ROOT'].$strNewFile);

					if (@copy($arTempFile["tmp_name"], $_SERVER['DOCUMENT_ROOT'].$strNewFile))
						return $strFilePath.$strFile;
				}
			}
		}
		return '';
	}
}

$strCatalogDefaultFolder = COption::GetOptionString("catalog", "export_default_path", CATALOG_DEFAULT_EXPORT_PATH);

$NUM_CATALOG_LEVELS = intval(COption::GetOptionString("catalog", "num_catalog_levels", 3));
if (0 >= $NUM_CATALOG_LEVELS)
	$NUM_CATALOG_LEVELS = 3;

$strExportErrorMessage = "";
$arRunErrors = array();

$IBLOCK_ID = intval($IBLOCK_ID);
if ($IBLOCK_ID<=0)
{
	$arRunErrors[] = GetMessage("CATI_NO_IBLOCK");
}
else
{
	$arIBlockres = CIBlock::GetList(Array("sort"=>"asc"), Array("ID"=>$IBLOCK_ID, 'CHECK_PERMISSIONS' => 'N'));
	if (!($arIBlock = $arIBlockres->Fetch()))
	{
		$arRunErrors[] = GetMessage("CATI_NO_IBLOCK");
	}
}

$boolCatalog = false;
if (empty($arRunErrors))
{
	$arCatalog = CCatalog::GetByID($IBLOCK_ID);
	if (!empty($arCatalog))
		$boolCatalog = true;
}

if (empty($arRunErrors))
{
	$csvFile = new CCSVData();

	if (!isset($fields_type) || ($fields_type != "F" && $fields_type != "R"))
	{
		$arRunErrors[] = GetMessage("CATI_NO_FORMAT");
	}

	$csvFile->SetFieldsType($fields_type);

	$first_line_names = (isset($first_line_names) && $first_line_names == 'Y' ? true : false);
	$csvFile->SetFirstHeader($first_line_names);

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
		$arRunErrors[] = GetMessage("CATI_NO_DELIMITER");
	}

	if (empty($arRunErrors))
	{
		$csvFile->SetDelimiter($delimiter_r_char);
	}

	if (!isset($export_files) || $export_files != 'Y')
		$export_files = 'N';
	if (!isset($export_from_clouds) || $export_from_clouds != 'Y')
		$export_from_clouds = 'N';

	if (!isset($SETUP_FILE_NAME) || strlen($SETUP_FILE_NAME) <= 0)
	{
		$arRunErrors[] = GetMessage("CATI_NO_SAVE_FILE");
	}
	elseif (preg_match(BX_CATALOG_FILENAME_REG,$SETUP_FILE_NAME))
	{
		$arRunErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
	}
	else
	{
		$SETUP_FILE_NAME = Rel2Abs("/", $SETUP_FILE_NAME);
		if (strtolower(substr($SETUP_FILE_NAME, strlen($SETUP_FILE_NAME)-4)) != ".csv")
			$SETUP_FILE_NAME .= ".csv";
		if (0 !== strpos($SETUP_FILE_NAME, $strCatalogDefaultFolder))
		{
			$arRunErrors[] = GetMessage('CES_ERROR_PATH_WITHOUT_DEFAUT');
		}
		else
		{
			CheckDirPath($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME);

			if (!($fp = fopen($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, "wb")))
			{
				$arRunErrors[] = GetMessage("CATI_CANNOT_CREATE_FILE");
			}
			@fclose($fp);
			if ('Y' == $export_files)
			{
				$strExportPath = GetDirPath($SETUP_FILE_NAME);
				$strFilePath = str_replace($strExportPath,'',substr($SETUP_FILE_NAME,0,-4)).'_files/';
				if (!CheckDirPath($_SERVER['DOCUMENT_ROOT'].$strExportPath.$strFilePath))
				{
					$arRunErrors[] = str_replace('#PATH#', $strExportPath.$strFilePath, GetMessage('CATI_NO_RIGHTS_EXPORT_FILES_PATH'));
					$export_files = 'N';
				}
			}
		}
	}

	$bFieldsPres = (!empty($field_needed) && is_array($field_needed) && in_array('Y', $field_needed));
	if ($bFieldsPres && (empty($field_code) || !is_array($field_code)))
	{
		$bFieldsPres = false;
	}
	if (!$bFieldsPres)
	{
		$arRunErrors[] = GetMessage("CATI_NO_FIELDS");
	}

	$num_rows_writed = 0;

	if (empty($arRunErrors))
	{
		global $defCatalogAvailGroupFields, $defCatalogAvailProdFields, $defCatalogAvailPriceFields, $defCatalogAvailValueFields, $defCatalogAvailQuantityFields;
		global $arCatalogAvailProdFields, $arCatalogAvailGroupFields, $arCatalogAvailPriceFields, $arCatalogAvailValueFields, $arCatalogAvailQuantityFields;

		$intCount = 0; // count of all available fields, props, section fields, prices
		$arSortFields = array(); // array for order
		$selectArray = array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"); // selected element fields
		$bNeedGroups = false; // sections need?
		$bNeedPrices = false; // prices need?
		$bNeedProducts = false; // product properties need?
		$bNeedProps = false; // element props need?
		$arGroupProps = array(); // section fields array (no user props)
		$arElementProps = array(); // element props
		$arCatalogGroups = array(); // prices
		$arProductFields = array(); // product properties
		$bNeedCounts = false; // price ranges
		$arCountFields = array(); // price ranges fields
		$arValueCodes = array();
		$arNeedFields = array(); // result order

		// Prepare arrays for product loading
		$strAvailProdFields = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailProdFields);
		$arAvailProdFields = explode(",", $strAvailProdFields);
		$arAvailProdFields_names = array();
		foreach ($arCatalogAvailProdFields as &$arOneCatalogAvailProdFields)
		{
			if (in_array($arOneCatalogAvailProdFields['value'],$arAvailProdFields))
			{
				$arAvailProdFields_names[$arOneCatalogAvailProdFields['value']] = array(
					"field" => $arOneCatalogAvailProdFields["field"],
					"important" => $arOneCatalogAvailProdFields["important"],
				);
				$mxSelKey = array_search($arOneCatalogAvailProdFields['value'], $field_code);
				if (!(false === $mxSelKey || empty($field_needed[$mxSelKey]) || 'Y' != $field_needed[$mxSelKey]))
				{
					$arSortFields[$arOneCatalogAvailProdFields['value']] = array(
						'CODE' => $arOneCatalogAvailProdFields['value'],
						'ID' => $intCount,
						'SORT' => (!empty($field_num[$mxSelKey]) && 0 < intval($field_num[$mxSelKey]) ? intval($field_num[$mxSelKey]) : ($intCount+1)*10),
					);
					$selectArray[] = $arOneCatalogAvailProdFields["field"];
				}
				$intCount++;
			}
		}
		if (isset($arOneCatalogAvailProdFields))
			unset($arOneCatalogAvailProdFields);

		$rsProps = CIBlockProperty::GetList(array("SORT"=>"ASC", "ID"=>"ASC"), array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y", 'CHECK_PERMISSIONS' => 'N'));
		while ($arProp = $rsProps->Fetch())
		{
			$mxSelKey = array_search('IP_PROP'.$arProp['ID'], $field_code);
			if (!(false === $mxSelKey || empty($field_needed[$mxSelKey]) || 'Y' != $field_needed[$mxSelKey]))
			{
				$arSortFields['IP_PROP'.$arProp['ID']] = array(
					'CODE' => 'IP_PROP'.$arProp['ID'],
					'ID' => $intCount,
					'SORT' => (!empty($field_num[$mxSelKey]) && 0 < intval($field_num[$mxSelKey]) ? intval($field_num[$mxSelKey]) : ($intCount+1)*10),
				);
				$bNeedProps = true;
				$arElementProps[] = $arProp['ID'];
			}
			$intCount++;
		}
		if ($bNeedProps)
			$arElementProps = array_values(array_unique($arElementProps));

		// Prepare arrays for groups loading
		$strAvailGroupFields = COption::GetOptionString("catalog", "allowed_group_fields", $defCatalogAvailGroupFields);
		$arAvailGroupFields = explode(",", $strAvailGroupFields);
		$arAvailGroupFields_names = array();
		foreach ($arCatalogAvailGroupFields as &$arOneCatalogAvailGroupFields)
		{
			if (in_array($arOneCatalogAvailGroupFields['value'],$arAvailGroupFields))
			{
				$arAvailGroupFields_names[$arOneCatalogAvailGroupFields['value']] = array(
					"field" => $arOneCatalogAvailGroupFields["field"],
					"important" => $arOneCatalogAvailGroupFields["important"],
				);
			}
		}
		if (isset($arOneCatalogAvailGroupFields))
			unset($arOneCatalogAvailGroupFields);
		if (!empty($arAvailGroupFields_names))
		{
			$arAvailGroupFieldsList = array_keys($arAvailGroupFields_names);
			for ($i = 0; $i < $NUM_CATALOG_LEVELS; $i++)
			{
				foreach ($arAvailGroupFieldsList as &$strKey)
				{
					$mxSelKey = array_search($strKey.$i, $field_code);
					if (!(false === $mxSelKey || empty($field_needed[$mxSelKey]) || 'Y' != $field_needed[$mxSelKey]))
					{
						$arSortFields[$strKey.$i] = array(
							'CODE' => $strKey.$i,
							'ID' => $intCount,
							'SORT' => (!empty($field_num[$mxSelKey]) && 0 < intval($field_num[$mxSelKey]) ? intval($field_num[$mxSelKey]) : ($intCount+1)*10),
						);
						$bNeedGroups = true;
						$arGroupProps[$i][] = $strKey;
					}
					$intCount++;
				}
				if (isset($strKey))
					unset($strKey);
				if (!empty($arGroupProps[$i]))
					$arGroupProps[$i] = array_values(array_unique($arGroupProps[$i]));
			}
			unset($arAvailGroupFieldsList);
		}

		if ($boolCatalog)
		{
			// Prepare arrays for product loading (for catalog)
			$strAvailPriceFields = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailPriceFields);
			$arAvailPriceFields = explode(",", $strAvailPriceFields);
			$arAvailPriceFields_names = array();
			foreach ($arCatalogAvailPriceFields as &$arOneCatalogAvailPriceFields)
			{
				if (in_array($arOneCatalogAvailPriceFields['value'],$arAvailPriceFields))
				{
					$arAvailPriceFields_names[$arOneCatalogAvailPriceFields['value']] = array(
						"field" => $arOneCatalogAvailPriceFields["field"],
						"important" => $arOneCatalogAvailPriceFields["important"]
					);

					$mxSelKey = array_search($arOneCatalogAvailPriceFields['value'], $field_code);
					if (!(false === $mxSelKey || empty($field_needed[$mxSelKey]) || 'Y' != $field_needed[$mxSelKey]))
					{
						$arSortFields[$arOneCatalogAvailPriceFields['value']] = array(
							'CODE' => $arOneCatalogAvailPriceFields['value'],
							'ID' => $intCount,
							'SORT' => (!empty($field_num[$mxSelKey]) && 0 < intval($field_num[$mxSelKey]) ? intval($field_num[$mxSelKey]) : ($intCount+1)*10),
						);
						$bNeedProducts = true;
						$arProductFields[] = $arOneCatalogAvailPriceFields['value'];
					}
					$intCount++;
				}
			}
			if (isset($arOneCatalogAvailPriceFields))
				unset($arOneCatalogAvailPriceFields);
			if ($bNeedProducts)
				$arProductFields = array_values(array_unique($arProductFields));

			// Prepare arrays for price loading
			$strAvailCountFields = $defCatalogAvailQuantityFields;
			$arAvailCountFields = explode(",", $strAvailCountFields);
			$arAvailCountFields_names = array();
			foreach ($arCatalogAvailQuantityFields as &$arOneCatalogAvailQuantityFields)
			{
				if (in_array($arOneCatalogAvailQuantityFields['value'], $arAvailCountFields))
				{
					$arAvailCountFields_names[$arOneCatalogAvailQuantityFields['value']] = array(
						"field" => $arOneCatalogAvailQuantityFields["field"],
						"important" => $arOneCatalogAvailQuantityFields["important"]
					);
					$mxSelKey = array_search($arOneCatalogAvailQuantityFields['value'], $field_code);
					if (!(false === $mxSelKey || empty($field_needed[$mxSelKey]) || 'Y' != $field_needed[$mxSelKey]))
					{
						$arSortFields[$arOneCatalogAvailQuantityFields['value']] = array(
							'CODE' => $arOneCatalogAvailQuantityFields['value'],
							'ID' => $intCount,
							'SORT' => (!empty($field_num[$mxSelKey]) && 0 < intval($field_num[$mxSelKey]) ? intval($field_num[$mxSelKey]) : ($intCount+1)*10),
						);
						$bNeedCounts = true;
						$arCountFields[] = $arOneCatalogAvailQuantityFields['value'];
					}
					$intCount++;
				}
			}
			if (isset($arOneCatalogAvailQuantityFields))
				unset($arOneCatalogAvailQuantityFields);

			$strAvailValueFields = COption::GetOptionString("catalog", "allowed_price_fields", $defCatalogAvailValueFields);
			$arAvailValueFields = explode(",", $strAvailValueFields);
			$arAvailValueFields_names = array();
			foreach ($arCatalogAvailValueFields as &$arOneCatalogAvailValueFields)
			{
				if (in_array($arOneCatalogAvailValueFields['value'],$arAvailValueFields))
				{
					$arValueCodes[] = $arOneCatalogAvailValueFields['value'].'_';
					$arAvailValueFields_names[$arOneCatalogAvailValueFields['value']] = array(
						"field" => $arOneCatalogAvailValueFields["field"],
						"important" => $arOneCatalogAvailValueFields["important"]
					);
				}
			}
			if (isset($arOneCatalogAvailValueFields))
				unset($arOneCatalogAvailValueFields);
			if (!empty($arValueCodes))
				$arValueCodes = array_values(array_unique($arValueCodes));

			if (!empty($arAvailValueFields_names))
			{
				$arAvailValueFieldsList = array_keys($arAvailValueFields_names);
				$rsPriceTypes = CCatalogGroup::GetList(array("SORT" => "ASC"), array());
				while ($arPriceType = $rsPriceTypes->Fetch())
				{
					foreach ($arAvailValueFieldsList as &$strKey)
					{
						$mxSelKey = array_search($strKey.'_'.$arPriceType['ID'], $field_code);
						if (!(false === $mxSelKey || empty($field_needed[$mxSelKey]) || 'Y' != $field_needed[$mxSelKey]))
						{
							$arSortFields[$strKey.'_'.$arPriceType['ID']] = array(
								'CODE' => $strKey.'_'.$arPriceType['ID'],
								'ID' => $intCount,
								'SORT' => (!empty($field_num[$mxSelKey]) && 0 < intval($field_num[$mxSelKey]) ? intval($field_num[$mxSelKey]) : ($intCount+1)*10),
							);
							$bNeedPrices = true;
							$arCatalogGroups[] = intval($arPriceType['ID']);
						}
						$intCount++;
					}
					if (isset($strKey))
						unset($strKey);
				}
				unset($arAvailValueFieldsList);
				if ($bNeedPrices)
					$arCatalogGroups = array_values(array_unique($arCatalogGroups));
			}
			if (!$bNeedPrices)
			{
				$bNeedCounts = false;
				$arCountFields = array();
			}
		}
		uasort($arSortFields, '__sortCSVOrder');

		$arCacheSections = array();
		$arCacheChains = array();
		$arCacheResultSections = array();

		$arNeedFields = array_keys($arSortFields);

		if ($first_line_names)
		{
			$csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, $arNeedFields);
		}

		$arUserTypeFormat = false;
		$dbIBlockElement = CIBlockElement::GetList(
				array(),
				array("IBLOCK_ID" => $IBLOCK_ID, 'CHECK_PERMISSIONS' => 'N'),
				false,
				false,
				$selectArray
			);
		while ($obIBlockElement = $dbIBlockElement->GetNextElement())
		{
			$arIBlockElement = $obIBlockElement->GetFields();
			if (array_key_exists("PREVIEW_PICTURE", $arIBlockElement))
			{
				if ('Y' == $export_files)
				{
					$arIBlockElement["~PREVIEW_PICTURE"] = __CSVExportFile($arIBlockElement['PREVIEW_PICTURE'],$strExportPath,$strFilePath);
				}
				else
				{
					$arIBlockElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arIBlockElement["PREVIEW_PICTURE"]);
					if ($arIBlockElement["PREVIEW_PICTURE"])
						$arIBlockElement["~PREVIEW_PICTURE"] = $arIBlockElement["PREVIEW_PICTURE"]["SRC"];
				}
			}
			if (array_key_exists("DETAIL_PICTURE", $arIBlockElement))
			{
				if ('Y' == $export_files)
				{
					$arIBlockElement["~DETAIL_PICTURE"] = __CSVExportFile($arIBlockElement['DETAIL_PICTURE'],$strExportPath,$strFilePath);
				}
				else
				{
					$arIBlockElement["DETAIL_PICTURE"] = CFile::GetFileArray($arIBlockElement["DETAIL_PICTURE"]);
					if ($arIBlockElement["DETAIL_PICTURE"])
						$arIBlockElement["~DETAIL_PICTURE"] = $arIBlockElement["DETAIL_PICTURE"]["SRC"];
				}
			}
			if($bNeedProps)
				$arProperties = $obIBlockElement->GetProperties();
			else
				$arProperties = array();

			if($arUserTypeFormat === false)
			{
				$arUserTypeFormat = array();
				foreach($arProperties as $prop_id => $arProperty)
				{
					if (in_array($arProperty["ID"],$arElementProps))
					{
						$arUserTypeFormat[$arProperty["ID"]] = false;
						if(strlen($arProperty["USER_TYPE"]))
						{
							$arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
							if(array_key_exists("GetPublicViewHTML", $arUserType))
								$arUserTypeFormat[$arProperty["ID"]] = $arUserType["GetPublicViewHTML"];
						}
					}
				}
			}

			$arPropsValues = array();
			foreach($arProperties as $prop_id => $arProperty)
			{
				if (in_array($arProperty["ID"],$arElementProps))
				{
					if($arUserTypeFormat[$arProperty["ID"]])
					{
						if(is_array($arProperty["VALUE"]))
						{
							$arValues = array();
							foreach($arProperty["VALUE"] as $value)
								$arValues[] = call_user_func_array($arUserTypeFormat[$arProperty["ID"]],
									array(
										$arProperty,
										array("VALUE" => $value),
										array("MODE" => "CSV_EXPORT"),
									));
						}
						else
						{
							$arValues = call_user_func_array($arUserTypeFormat[$arProperty["ID"]],
								array(
									$arProperty,
									array("VALUE" => $arProperty["VALUE"]),
									array("MODE" => "CSV_EXPORT"),
								));
						}
					}
					elseif($arProperty["PROPERTY_TYPE"] == "F")
					{
						if(is_array($arProperty["~VALUE"]))
						{
							$arValues = array();
							foreach($arProperty["~VALUE"] as $file_id)
							{
								if ('Y' == $export_files)
								{
									$arValues[] = __CSVExportFile($file_id,$strExportPath,$strFilePath);
								}
								else
								{
									$file = CFile::GetFileArray($file_id);
									if($file)
										$arValues[] = $file["SRC"];
								}
							}
						}
						elseif($arProperty["~VALUE"] > 0)
						{
							if ('Y' == $export_files)
							{
								$arValues = __CSVExportFile($arProperty["~VALUE"],$strExportPath,$strFilePath);
							}
							else
							{
								$file = CFile::GetFileArray($arProperty["~VALUE"]);
								if($file)
									$arValues = $file["SRC"];
								else
									$arValues = "";
							}
						}
						else
						{
							$arValues = "";
						}
					}
					else
					{
						$arValues = $arProperty["~VALUE"];
					}
					$arPropsValues[$arProperty["ID"]] = $arValues;
				}
			}

			$arResSections = array();
			if($bNeedGroups)
			{
				$i = 0;
				$rsSections = CIBlockElement::GetElementGroups($arIBlockElement["ID"],true);
				while($arSection = $rsSections->Fetch())
				{
					if (!array_key_exists($arSection['ID'],$arCacheChains))
					{
						$arPath = array();
						$j = 0;
						$rsPath = CIBlockSection::GetNavChain($IBLOCK_ID, $arSection["ID"]);
						while($arPathSection = $rsPath->Fetch())
						{
							if (!empty($arGroupProps[$j]))
							{
								foreach ($arGroupProps[$j] as &$key)
								{
									$field = $arAvailGroupFields_names[$key]['field'];
									if ('IC_PICTURE' == $key || 'IC_DETAIL_PICTURE' == $key)
									{
										if ('Y' == $export_files)
										{
											$arPathSection[$field] = __CSVExportFile($arPathSection[$field],$strExportPath,$strFilePath);
										}
										else
										{
											$arPathSection[$field] = CFile::GetFileArray($arPathSection[$field]);
											if ($arPathSection[$field])
											{
												$arPathSection[$field] = $arPathSection[$field]["SRC"];
											}
											else
											{
												$arPathSection[$field] = '';
											}
										}
									}
									$arPath['~'.$key.$j] = $arPathSection[$field];
								}
								if (isset($key))
									unset($key);
								$arPathSection['IBLOCK_SECTION_ID'] = intval($arPathSection['IBLOCK_SECTION_ID']);
								$arCacheChains[$arPathSection['ID']] = $arPathSection['IBLOCK_SECTION_ID'];
								$arCacheSections[$arPathSection['ID']] = $arPath;
							}
							$j++;
						}
					}

					$arPath = array();
					if (!array_key_exists($arSection['ID'],$arCacheResultSections))
					{
						$intCurSect = $arSection['ID'];
						while (array_key_exists($intCurSect,$arCacheChains))
						{
							$arPath = array_merge($arPath,$arCacheSections[$intCurSect]);
							$intCurSect = $arCacheChains[$intCurSect];
						}
						$arCacheResultSections[$arSection['ID']] = $arPath;
					}
					else
					{
						$arPath = $arCacheResultSections[$arSection['ID']];
					}
					$arResSections[$i] = $arPath;
					$i++;
				}
				if(empty($arResSections))
					$arResSections[] = array();
			}
			else
			{
				$arResSections[] = array();
			}

			$arResPrices = array();
			if ($boolCatalog && $bNeedPrices)
			{
				$arResPricesMap = array();
				$mapIndex = -1;

				$dbProductPrice = CPrice::GetList(
						array(),
						array("PRODUCT_ID" => $arIBlockElement["ID"],'CATALOG_GROUP_ID' => $arCatalogGroups),
						false,
						false,
						array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO", "EXTRA_ID")
					);
				while ($arProductPrice = $dbProductPrice->Fetch())
				{
					if (!array_key_exists($arProductPrice["QUANTITY_FROM"]."-".$arProductPrice["QUANTITY_TO"], $arResPricesMap))
					{
						$mapIndex++;
						$arResPricesMap[$arProductPrice["QUANTITY_FROM"]."-".$arProductPrice["QUANTITY_TO"]] = $mapIndex;
					}
					$intDiap = $arResPricesMap[$arProductPrice["QUANTITY_FROM"]."-".$arProductPrice["QUANTITY_TO"]];
					foreach ($arAvailValueFields_names as $key => $value)
					{
						$arResPrices[$intDiap][$value['field'].'_'.$arProductPrice["CATALOG_GROUP_ID"]] = $arProductPrice[$value['field']];
					}
					$arResPrices[$intDiap]['QUANTITY_FROM'] = $arProductPrice["QUANTITY_FROM"];
					$arResPrices[$intDiap]['QUANTITY_TO'] = $arProductPrice["QUANTITY_TO"];
					//$arResPrices[$arResPricesMap[$arProductPrice["QUANTITY_FROM"]."-".$arProductPrice["QUANTITY_TO"]]][IntVal($arProductPrice["CATALOG_GROUP_ID"])] = $arProductPrice;
				}
				if (empty($arResPrices))
					$arResPrices[] = array();
			}
			else
			{
				$arResPrices[] = array();
			}

			$arResProducts = array();
			if ($boolCatalog && $bNeedProducts)
			{
				$arProduct = CCatalogProduct::GetByID($arIBlockElement["ID"]);
				if (!empty($arProduct))
				{
					foreach ($arAvailPriceFields_names as $key => $value)
					{
						$arResProducts[$value['field']] = $arProduct[$value['field']];
					}
				}
			}

			$arResFields = array();
			foreach($arResSections as $arPath)
			{
				foreach ($arResPrices as $arPrice)
				{
					$arTuple = array();
					foreach($arNeedFields as $field_name)
					{
						if (strncmp($field_name, "IE_", 3) == 0)
							$arTuple[] = $arIBlockElement["~".substr($field_name, 3)];
						elseif (strncmp($field_name, "IP_PROP", 7) == 0)
							$arTuple[] = $arPropsValues[intval(substr($field_name, 7))];
						elseif (strncmp($field_name, "IC_", 3) == 0)
						{
							$strKey = $field_name;
							$arTuple[] = (array_key_exists('~'.$strKey,$arPath) ? $arPath['~'.$strKey] : '');
						}
						elseif (strncmp($field_name, 'CV_', 3) == 0)
						{
							$strKey = substr($field_name,3);
							$arTuple[] = (array_key_exists($strKey,$arPrice) ? $arPrice[$strKey] : '');
						}
						elseif (strncmp($field_name, 'CP_', 3) == 0)
						{
							$arTuple[] = (!empty($arResProducts) ? $arResProducts[substr($field_name, 3)] : '');
						}
					}
					__CSVArrayMultiply($arResFields, $arTuple);
				}
			}

			foreach($arResFields as $arTuple)
			{
				$csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, $arTuple);
				$num_rows_writed++;
			}
		}
	}
}

if (!empty($arRunErrors))
	$strExportErrorMessage = implode('<br />',$arRunErrors);

CCatalogDiscountSave::Enable();

if ($bTmpUserCreated)
{
	unset($USER);
	if (isset($USER_TMP))
	{
		$USER = $USER_TMP;
		unset($USER_TMP);
	}
}
?>