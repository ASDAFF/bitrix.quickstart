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

$arParams['ELEMENT_COUNT'] = (isset($arParams['ELEMENT_COUNT']) ? (int)$arParams['ELEMENT_COUNT'] : 0);
$arParams['SINGLE_COMPONENT'] = (isset($arParams['SINGLE_COMPONENT']) && $arParams['SINGLE_COMPONENT'] == 'Y' ? 'Y' : 'N');

if(!isset($arParams["WIDTH"]) || intval($arParams["WIDTH"]) <= 0)
    $arParams["WIDTH"] = 120;

if(!isset($arParams["HEIGHT"]) || intval($arParams["HEIGHT"]) <= 0)
    $arParams["HEIGHT"] = 50;

if(!isset($arParams["WIDTH_SMALL"]) || intval($arParams["WIDTH_SMALL"]) <= 0)
    $arParams["WIDTH_SMALL"] = 21;

if(!isset($arParams["HEIGHT_SMALL"]) || intval($arParams["HEIGHT_SMALL"]) <= 0)
    $arParams["HEIGHT_SMALL"] = 17;


    $arBrandBlocks = [];
    if (CModule::IncludeModule("highloadblock")) {

        $directoryOrder = array();
        if (isset($fieldsList['UF_SORT']))
            $directoryOrder['UF_SORT'] = 'ASC';
        $directoryOrder['ID'] = 'ASC';

        $arFilter = array(
            'order' => $directoryOrder
        );
        if ($arParams['ELEMENT_COUNT'] > 0)
            $arFilter['limit'] = $arParams['ELEMENT_COUNT'];


        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arParams['HIGH_LOAD_CODE']);
        $entityDataClass = $entity->getDataClass();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arParams['HIGH_LOAD_CODE']);
        $entityDataClass = $entity->getDataClass();
        $rsPropEnums = $entityDataClass::getList($arFilter);
        while ($arEnum = $rsPropEnums->fetch()) {
            $boolPict = true;
            if (!isset($arEnum['UF_NAME'])) {
                $boolName = false;
                break;
            }

            $arEnum['PREVIEW_PICTURE'] = false;
            $arEnum['ID'] = (int)$arEnum['ID'];

            if (!isset($arEnum['UF_FILE']) || (int)$arEnum['UF_FILE'] <= 0)
                $boolPict = false;

            if ($boolPict) {
                $arEnum['PREVIEW_PICTURE'] = CFile::GetFileArray($arEnum['UF_FILE']);
                if (empty($arEnum['PREVIEW_PICTURE']))
                    $boolPict = false;
            }

            $descrExists = (isset($arEnum['UF_DESCRIPTION']) && (string)$arEnum['UF_DESCRIPTION'] !== '');
            if ($boolPict) {
                if ($descrExists) {
                    $width = $arParams["WIDTH_SMALL"];
                    $height = $arParams["HEIGHT_SMALL"];
                    $type = "PIC_TEXT";
                } else {
                    $width = $arParams["WIDTH"];
                    $height = $arParams["HEIGHT"];
                    $type = "ONLY_PIC";
                }

                $arEnum['PREVIEW_PICTURE']['WIDTH'] = (int)$arEnum['PREVIEW_PICTURE']['WIDTH'];
                $arEnum['PREVIEW_PICTURE']['HEIGHT'] = (int)$arEnum['PREVIEW_PICTURE']['HEIGHT'];
                if (
                    $arEnum['PREVIEW_PICTURE']['WIDTH'] > $width
                    || $arEnum['PREVIEW_PICTURE']['HEIGHT'] > $height
                ) {
                    $arEnum['PREVIEW_PICTURE']['src'] = CFile::GetPath(
                        $arEnum['PREVIEW_PICTURE']['ID']
                    );

                    $arEnum['PREVIEW_PICTURE']['SRC'] = $arEnum['PREVIEW_PICTURE']['src'];
                    $arEnum['PREVIEW_PICTURE']['WIDTH'] = $arEnum['PREVIEW_PICTURE']['width'];
                    $arEnum['PREVIEW_PICTURE']['HEIGHT'] = $arEnum['PREVIEW_PICTURE']['height'];
                }
            } elseif ($descrExists) {
                $type = "ONLY_TEXT";
            } else //Nothing to show
            {
                continue;
            }
            $arBrandBlocks[] = array(
                'TYPE' => $type,
                'NAME' => (isset($arEnum['UF_NAME']) ? $arEnum['UF_NAME'] : false),
                'LINK' => (isset($arEnum['UF_LINK']) && '' != $arEnum['UF_LINK'] ? $arEnum['UF_LINK'] : false),
                'DESCRIPTION' => ($descrExists ? $arEnum['UF_DESCRIPTION'] : false),
                'FULL_DESCRIPTION' => (isset($arEnum['UF_FULL_DESCRIPTION']) && (string)$arEnum['UF_FULL_DESCRIPTION'] !== '' ? $arEnum['UF_FULL_DESCRIPTION'] : false),
                'PICT' => ($boolPict ? $arEnum['PREVIEW_PICTURE'] : false)
            );
        }

    }
    $arResult["BRAND_BLOCKS"] = $arBrandBlocks;

    $this->includeComponentTemplate();
