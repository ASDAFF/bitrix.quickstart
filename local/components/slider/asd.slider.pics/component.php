<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!function_exists('__asd_pics_slider_inc'))
{
	function __asd_pics_slider_inc()
	{
		static $i = 0;
		return ++$i;
	}
}

if ($arParams['TIME'] <= 0)
	$arParams['TIME'] = 5;
$arParams['TIME'] = intval($arParams['TIME']);

if ($this->StartResultCache(false))
{
	if (!CModule::IncludeModule('iblock'))
	{
		$this->AbortResultCache();
		return;
	}

	$arResult = array();

	$arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y');
	if ($arParams['SORT'] == 'ASC')
		$arSort = array('SORT' => 'ASC', 'ID' => 'DESC');
	else
		$arSort = array('RAND' => 'ASC');
	$arSelect = array('NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE');

	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, $arParams['COUNT']>0 ? array('nTopCount' => $arParams['COUNT']) : false, $arSelect);
	while ($arElements = $rsElements->GetNext())
	{
		if ($arParams['PIC_FROM'] == 'DETAIL_PICTURE')
			$arElements['PICTURE'] = CFIle::GetFileArray($arElements['DETAIL_PICTURE']);
		else
			$arElements['PICTURE'] = CFIle::GetFileArray($arElements['PREVIEW_PICTURE']);
		$arResult[] = $arElements;
	}

	$this->IncludeComponentTemplate();
}
?>