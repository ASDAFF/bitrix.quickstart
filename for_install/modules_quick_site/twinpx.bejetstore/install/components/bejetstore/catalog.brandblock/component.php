<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Loader;

$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arParams['ELEMENT_CODE'] = ($arParams["ELEMENT_ID"] > 0 ? '' : trim($arParams['ELEMENT_CODE']));

$arParams['CACHE_GROUPS'] = (isset($arParams['CACHE_GROUPS']) && $arParams['CACHE_GROUPS'] == 'N' ? 'N' : 'Y');
if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600000;

if(!isset($arParams["WIDTH"]) || intval($arParams["WIDTH"]) <= 0)
	$arParams["WIDTH"] = 120;

if(!isset($arParams["HEIGHT"]) || intval($arParams["HEIGHT"]) <= 0)
	$arParams["HEIGHT"] = 50;

if(!isset($arParams["WIDTH_SMALL"]) || intval($arParams["WIDTH_SMALL"]) <= 0)
	$arParams["WIDTH_SMALL"] = 21;

if(!isset($arParams["HEIGHT_SMALL"]) || intval($arParams["HEIGHT_SMALL"]) <= 0)
	$arParams["HEIGHT_SMALL"] = 17;

//Let's cache it
$additionalCache = $arParams["CACHE_GROUPS"]==="N"? false: array($USER->GetGroups());
if ($this->StartResultCache(false, $additionalCache))
{
	if (!Loader::includeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_CBB_IBLOCK_NOT_INSTALLED"));
		return false;
	}

	if (!Loader::includeModule('highloadblock'))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_CBB_HLIBLOCK_NOT_INSTALLED"));
		return false;
	}

	//Handle case when ELEMENT_CODE used
	if($arParams["ELEMENT_ID"] <= 0)
	{
		$arParams["ELEMENT_ID"] = CIBlockFindTools::GetElementID(
			$arParams["ELEMENT_ID"],
			$arParams["ELEMENT_CODE"],
			false,
			false,
			array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"IBLOCK_LID" => SITE_ID,
				"IBLOCK_ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
			)
		);
		$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
	}
	$arResult['ID'] = $arParams["ELEMENT_ID"];

	$arBrandBlocks = array();

	/*if(CModule::includeModule("iblock")){
		if($arParams["BRAND_ID"]){
			$rsBrand = CIBlockElement::GetByID($arParams["BRAND_ID"]);
			if($arBrand = $rsBrand->Fetch()){
				if($arBrand["PREVIEW_PICTURE"]){
					$rsFile = CFile::GetByID($arBrand["PREVIEW_PICTURE"]);
					$arFile = $rsFile->Fetch();
					$arBrandBlocks["0"] = array(
						'TYPE' => "brand",
						'LINK' => "",
						'DESCRIPTION' => "",
						'FULL_DESCRIPTION' => "",
						'PICT' => array(
							'SRC' => "/upload/".$arFile["SUBDIR"]."/".$arFile["FILE_NAME"]
						)
					);
				}
			}
		}
	}*/

	// Show only linked to element brands
	if($arResult['ID'] > 0)
	{
		$rsProps = CIBlockElement::GetProperty(
			$arParams['IBLOCK_ID'],
			$arResult['ID'],
			"sort",
			"asc",
			array(
				'CODE' => $arParams['PROP_CODE'],
				'ACTIVE' => 'Y'
			)
		);
	}
	else // Show all rows from table
	{
		$rsProps = CIBlockProperty::GetList(
			array("SORT" => "ASC", "ID" => "ASC"),
			array(
				"IBLOCK_ID" => $arParams['IBLOCK_ID'],
				'CODE' => $arParams['PROP_CODE'],
				"ACTIVE" => "Y"
			)
		);
	}

	$hlblocks = array();
	$reqParams = array();

	while($arProp = $rsProps->Fetch())
	{
		if(!isset($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']))
			continue;

		if(!isset($hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']]))
		{
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
				array(
					"filter" => array(
						'TABLE_NAME' => $arProp['USER_TYPE_SETTINGS']['TABLE_NAME']
					)
				)
			)->fetch();

			$hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']] = $hlblock;
		}
		else
		{
			$hlblock = $hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']];
		}

		if (isset($hlblock['ID']))
		{
			if(!isset($reqParams[$hlblock['ID']]))
			{
				$reqParams[$hlblock['ID']] = array();
				$reqParams[$hlblock['ID']]['HLB'] = $hlblock;
			}

			$reqParams[$hlblock['ID']]['VALUES'][] = $arProp['VALUE'];
		}
	}

	foreach ($reqParams as $params)
	{
		$boolName = true;
		$boolPict = true;

		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($params['HLB']);
		$entityDataClass = $entity->getDataClass();
		$fieldsList = $entityDataClass::getMap();
		if (count($fieldsList) === 1 && isset($fieldsList['ID']))
		{
			$fieldsList = $entityDataClass::getEntity()->getFields();
		}
		$directoryOrder = array();
		if (isset($fieldsList['UF_SORT']))
		{
			$directoryOrder['UF_SORT'] = 'ASC';
		}
		$directoryOrder['ID'] = 'ASC';

		$arFilter = array(
			'order' => $directoryOrder
		);
		if($arResult['ID'] > 0)
		{
			$arFilter['filter'] = array(
				'UF_XML_ID' => $params['VALUES']
			);
		}

		$rsPropEnums = $entityDataClass::getList($arFilter);

		while ($arEnum = $rsPropEnums->fetch())
		{
			$boolPict = true;
			if (!isset($arEnum['UF_NAME']))
			{
				$boolName = false;
				break;
			}

			$arEnum['PREVIEW_PICTURE'] = false;
			$arEnum['ID'] = intval($arEnum['ID']);

			if (!isset($arEnum['UF_FILE']) || strlen($arEnum['UF_FILE']) <= 0)
				$boolPict = false;

			if ($boolPict)
			{
				$arEnum['PREVIEW_PICTURE'] = CFile::GetFileArray($arEnum['UF_FILE']);
				if (empty($arEnum['PREVIEW_PICTURE']))
					$boolPict = false;
			}

			//echo "<pre>";print_r($arEnum);echo "</pre>";

			$descrExists = (isset($arEnum['UF_DESCRIPTION']) && strlen($arEnum['UF_DESCRIPTION']) > 0);
			if ($boolPict)
			{
				if ($descrExists)
				{
					$width = $arParams["WIDTH_SMALL"];
					$height = $arParams["HEIGHT_SMALL"];
					$type = "PIC_TEXT";
				}
				else
				{
					$width = $arParams["WIDTH"];
					$height = $arParams["HEIGHT"];
					$type = "ONLY_PIC";
				}

				if(intval($arEnum['PREVIEW_PICTURE']['WIDTH']) > intval($arEnum['PREVIEW_PICTURE']['HEIGHT'])){
					if (
						intval($arEnum['PREVIEW_PICTURE']['WIDTH']) > $width
						||
						intval($arEnum['PREVIEW_PICTURE']['HEIGHT']) > $height
						)
					{
						$arEnum['PREVIEW_PICTURE'] = CFile::ResizeImageGet(
							$arEnum['PREVIEW_PICTURE'],
							array("width" => 999999, "height" => $height),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);

						$arEnum['PREVIEW_PICTURE']['SRC'] = $arEnum['PREVIEW_PICTURE']['src'];
						$arEnum['PREVIEW_PICTURE']['WIDTH'] = $arEnum['PREVIEW_PICTURE']['width'];
						$arEnum['PREVIEW_PICTURE']['HEIGHT'] = $arEnum['PREVIEW_PICTURE']['height'];
					}
				}elseif(intval($arEnum['PREVIEW_PICTURE']['HEIGHT']) > intval($arEnum['PREVIEW_PICTURE']['WIDTH'])){
					if (
						intval($arEnum['PREVIEW_PICTURE']['WIDTH']) > $width
						||
						intval($arEnum['PREVIEW_PICTURE']['HEIGHT']) > $height
						)
					{
						$arEnum['PREVIEW_PICTURE'] = CFile::ResizeImageGet(
							$arEnum['PREVIEW_PICTURE'],
							array("width" => $width, "height" => 999999),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);

						$arEnum['PREVIEW_PICTURE']['SRC'] = $arEnum['PREVIEW_PICTURE']['src'];
						$arEnum['PREVIEW_PICTURE']['WIDTH'] = $arEnum['PREVIEW_PICTURE']['width'];
						$arEnum['PREVIEW_PICTURE']['HEIGHT'] = $arEnum['PREVIEW_PICTURE']['height'];
					}
				}else{
					if (
						intval($arEnum['PREVIEW_PICTURE']['WIDTH']) > $width
						||
						intval($arEnum['PREVIEW_PICTURE']['HEIGHT']) > $height
						)
					{
						$arEnum['PREVIEW_PICTURE'] = CFile::ResizeImageGet(
							$arEnum['PREVIEW_PICTURE'],
							array("width" => $width, "height" => $height),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);

						$arEnum['PREVIEW_PICTURE']['SRC'] = $arEnum['PREVIEW_PICTURE']['src'];
						$arEnum['PREVIEW_PICTURE']['WIDTH'] = $arEnum['PREVIEW_PICTURE']['width'];
						$arEnum['PREVIEW_PICTURE']['HEIGHT'] = $arEnum['PREVIEW_PICTURE']['height'];
					}
				}				
			}
			elseif ($descrExists)
			{
				$type = "ONLY_TEXT";
			}
			else //Nothing to show
			{
				continue;
			}

			$arBrandBlocks[$arEnum['ID']] = array(
				'TYPE' => $type,
				'NAME' => (isset($arEnum['UF_NAME']) ? $arEnum['UF_NAME'] : false),
				'LINK' => (isset($arEnum['UF_LINK']) && '' != $arEnum['UF_LINK'] ? $arEnum['UF_LINK'] : false),
				'DESCRIPTION' => ($descrExists ? $arEnum['UF_DESCRIPTION'] : false),
				'FULL_DESCRIPTION' => (isset($arEnum['UF_FULL_DESCRIPTION']) && '' !== $arEnum['UF_FULL_DESCRIPTION'] ? $arEnum['UF_FULL_DESCRIPTION'] : false),
				'PICT' => ($boolPict ? $arEnum['PREVIEW_PICTURE'] : false)
			);
		}
	}

	$arResult["BRAND_BLOCKS"] = $arBrandBlocks;
	$this->IncludeComponentTemplate();
}
?>