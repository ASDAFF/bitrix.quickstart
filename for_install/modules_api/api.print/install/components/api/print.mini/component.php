<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

$MODULE_ID = 'api.print';
$arParams['IBLOCK_ID']              = intval(COption::GetOptionString($MODULE_ID, 'PRINT_IBLOCK_ID'));
$arParams['PRINT_FILE_URL']         = !empty($arParams['PRINT_FILE_URL']) ? trim($arParams['PRINT_FILE_URL']) : '/ts_print.php';
$arParams['CSS_FILE_URL']           = trim($arParams['CSS_FILE_URL']);
$arParams['CHECK_ACTIVE_SECTION']   = $arParams['CHECK_ACTIVE_SECTION'] == 'Y';
$arParams['ENABLE_PDF']             = $arParams['ENABLE_PDF'] == 'Y';
$arParams['INCLUDE_JQUERY']         = $arParams['INCLUDE_JQUERY'] == 'Y';
$arParams['RESIZE_PREVIEW_PICTURE'] = $arParams['RESIZE_PREVIEW_PICTURE'] == 'Y';
$arParams['RESIZE_DETAIL_PICTURE']  = $arParams['RESIZE_DETAIL_PICTURE'] == 'Y';
$arParams['PICTURE_ALIGN']          = trim($arParams['PICTURE_ALIGN']);
$arParams['SET_PICTURE_BORDER']     = $arParams['SET_PICTURE_BORDER'] == 'Y';

$arParams['PPS'] = $arParams['DPS'] = array(
	'ALIGN'  => $arParams['PICTURE_ALIGN'],
	'BORDER' => $arParams['SET_PICTURE_BORDER'],
);

if($arParams['RESIZE_PREVIEW_PICTURE'])
{
	$arParams['PPS']['WIDTH']  = trim($arParams['PREVIEW_PICTURE_WIDTH']);
	$arParams['PPS']['HEIGHT'] = trim($arParams['PREVIEW_PICTURE_HEIGHT']);
}

if($arParams['RESIZE_DETAIL_PICTURE'])
{
	$arParams['DPS']['WIDTH']  = trim($arParams['DETAIL_PICTURE_WIDTH']);
	$arParams['DPS']['HEIGHT'] = trim($arParams['DETAIL_PICTURE_HEIGHT']);
}

if($arParams['INCLUDE_JQUERY'])
	CUtil::InitJSCore('jquery');

if(!is_array($arParams['FIELD_CODE']))
	$arParams['FIELD_CODE'] = array();
foreach($arParams['FIELD_CODE'] as $key => $val)
{
	if(!$val)
		unset($arParams['FIELD_CODE'][$key]);
}

if(!is_array($arParams['PROPERTY_CODE']))
	$arParams['PROPERTY_CODE'] = array();
foreach($arParams['PROPERTY_CODE'] as $k => $v)
{
	if($v === '')
		unset($arParams['PROPERTY_CODE'][$k]);
}

$arParams['FIELD_CODE'] = array_merge(array('ID', 'NAME'), $arParams['FIELD_CODE']);
if(count($arParams['PROPERTY_CODE']) > 0)
	$arParams['FIELD_CODE'][] = 'PROPERTY_*';

if($arParams['IBLOCK_ID'] < 1)
{
	ShowError(GetMessage('EMPTY_IBLOCK_ID'));
	return false;
}

$this->IncludeComponentTemplate();