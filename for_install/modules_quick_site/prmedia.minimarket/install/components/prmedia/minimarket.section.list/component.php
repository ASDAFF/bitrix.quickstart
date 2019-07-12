<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CCacheManager $CACHE_MANAGER
 * @global CDatabase $DB
 * @param CBitrixComponent $this
 * @param array $this->arParams
 * @param array $this->arResult
 */

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

global $APPLICATION;
$currentPage = $APPLICATION->GetCurPage(false);

$this->arResult = array(
	'SECTION_LIST' => array()
);

$selectParams = array(
	'order' => array(
		'left_margin' => 'asc'
	),
	'filter' => array(
		'ACTIVE' => 'Y',
		'GLOBAL_ACTIVE' => 'Y',
		'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
		'CNT_ACTIVE' => 'Y',
		'<=DEPTH_LEVEL' => $this->arParams['TOP_DEPTH']
	),
	'select' => array(
		'ID', 'NAME', 'DEPTH_LEVEL', 'SECTION_PAGE_URL'
	)
);
$rsSection = CIBlockSection::GetList($selectParams['order'], $selectParams['filter'], false, $selectParams['select']);
if (!empty($this->arParams['SECTION_URL']))
{
	$rsSection->SetUrlTemplates(false, $this->arParams['SECTION_URL']);
}

$index = 0;
while ($arSection = $rsSection->GetNext())
{
	if ($arSection['DEPTH_LEVEL'] == 1)
	{
		$index++;
		if (strpos($currentPage, $arSection['SECTION_PAGE_URL']) !== false)
		{
			$arSection['SELECTED'] = 'Y';
		}
		$this->arResult['SECTION_LIST'][$index] = $arSection;
	}
	else
	{
		if (strpos($currentPage, $arSection['SECTION_PAGE_URL']) !== false)
		{
			$arSection['SELECTED'] = 'Y';
			unset($this->arResult['SECTION_LIST'][$index]['SELECTED']);
		}
		$this->arResult['SECTION_LIST'][$index]['SECTION_LIST'][] = $arSection;
	}
}

$this->IncludeComponentTemplate();