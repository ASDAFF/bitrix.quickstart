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

$arParams['CACHE_GROUPS'] = (isset($arParams['CACHE_GROUPS']) && $arParams['CACHE_GROUPS'] == 'N' ? 'N' : 'Y');
if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600000;

if(!isset($arParams["WIDTH"]) || intval($arParams["WIDTH"]) < 0)
	$arParams["WIDTH"] = 0;

if(!isset($arParams["HEIGHT"]) || intval($arParams["HEIGHT"]) < 0)
	$arParams["HEIGHT"] = 0;

if(!isset($arParams["INTERVAL"]) || intval($arParams["INTERVAL"]) <= 0)
	$arParams["INTERVAL"] = 5;
	
if(!isset($arParams["USE_JQUERY"]) || $arParams["USE_JQUERY"] != "Y")
	$arParams["USE_JQUERY"] = 'N';
	
if($arParams["USE_JQUERY"] != 'N')
	$arParams["USE_JQUERY"] = 'Y';
	
//Let's cache it
$additionalCache = $arParams["CACHE_GROUPS"]==="N"? false: array($USER->GetGroups());
if ($this->StartResultCache(false, $additionalCache))
{
	if (!\Bitrix\Main\Loader::includeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_CBB_IBLOCK_NOT_INSTALLED"));
		return false;
	}

	if (!\Bitrix\Main\Loader::includeModule('highloadblock'))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_CBB_HLIBLOCK_NOT_INSTALLED"));
		return false;
	}

	//Handle case when ELEMENT_CODE used
	
	$arSlideBlocks = array();
	$hlblocks = array();
	$reqParams = array();

	
		$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
				array(
					"filter" => array(
						'TABLE_NAME' => $arParams["IBLOCK_ID"]
					)
				)
			)->fetch();

		$boolName = true;
		$boolPict = true;
		if(!empty($hlblock))
		{
			$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
			$entityDataClass = $entity->getDataClass();
			$fieldsList = $entityDataClass::getMap();
			$directoryOrder = array();
			if (isset($fieldsList['UF_SORT']))
			{
				$directoryOrder['UF_SORT'] = 'ASC';
			}
			$directoryOrder['ID'] = 'ASC';

			$arFilter = array(
				'order' => $directoryOrder
			);
			

			$rsPropEnums = $entityDataClass::getList($arFilter);

			while ($arEnum = $rsPropEnums->fetch())
			{
				$boolPict = true;
				
				$arEnum['PREVIEW_PICTURE'] = false;
				$arEnum['ID'] = intval($arEnum['ID']);

				if (!isset($arEnum['UF_FILE']) || strlen($arEnum['UF_FILE']) <= 0)
				{
					$boolPict = false;
					break;
				}

				if ($boolPict)
				{
					$arEnum['PREVIEW_PICTURE'] = CFile::GetFileArray($arEnum['UF_FILE']);
					if (empty($arEnum['PREVIEW_PICTURE']))
						$boolPict = false;
				}

				$descrExists = (isset($arEnum['UF_BTNAME']) && strlen($arEnum['UF_BTNAME']) > 0);
				if ($boolPict && ($arParams["WIDTH"] != 0 && $arParams["HEIGHT"] != 0))
				{
					
						$width = $arParams["WIDTH"];
						$height = $arParams["HEIGHT"];
						
					
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
					}
				}
				else
				{
				$width = '';
				$height = '';
				}
				
				$arSlideBlocks[$arEnum['ID']] = array(
					'HEAD' => (isset($arEnum['UF_HEAD']) ? $arEnum['UF_HEAD'] : false),
					'LINK' => (isset($arEnum['UF_LINK']) && '' != $arEnum['UF_LINK'] ? $arEnum['UF_LINK'] : false),
					'BTNAME' => ($descrExists ? $arEnum['UF_BTNAME'] : false),
					'DESC' => (isset($arEnum['UF_DESC']) && '' != $arEnum['UF_DESC'] ? $arEnum['UF_DESC'] : false),
					'PICT' => (
						array(
							'SRC' => $arEnum['PREVIEW_PICTURE']['SRC'],
							'WIDTH' => $width,
							'HEIGHT' => $height
						)
						
					)
				);
			}
		

			$arResult["ITEMS"] = $arSlideBlocks;
		}
	$this->IncludeComponentTemplate();
}
?>