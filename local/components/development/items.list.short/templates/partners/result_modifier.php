<?
use \Bitrix\Iblock\SectionTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arResult['ITEMS_EXISTS'] = !empty($arResult['ITEMS']);

// Собираем информацию о разделах
$arSections = array();
foreach($arResult['ITEMS'] as $arItem){
	$arSections[$arItem['IBLOCK_SECTION_ID']]['ITEMS'][] = $arItem;
}

$rsSections = SectionTable::getList(
	array(
		'filter' => array('ID' => array_keys($arSections)),
		'select' => array('NAME', 'ID', 'CODE')
	)
);

while($arSection = $rsSections->fetch() ){
	$arSections[$arSection['ID']]['NAME'] = $arSection['NAME'];
	$arSections[$arSection['ID']]['CODE'] = $arSection['CODE'];
}

$arResult['ITEMS'] = $arSections;