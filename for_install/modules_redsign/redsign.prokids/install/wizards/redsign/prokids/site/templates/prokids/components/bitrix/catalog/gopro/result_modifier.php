<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

// sorter
if( empty($arParams['SORTER_TEMPLATE_NAME_1']) || $arParams['SORTER_TEMPLATE_NAME_1']=='' ) {
	$arParams['SORTER_TEMPLATE_NAME_1'] = GetMessage('SORTER_TEMPLATE_NAME_showcase');
}
if( empty($arParams['SORTER_TEMPLATE_NAME_2']) || $arParams['SORTER_TEMPLATE_NAME_2']=='' ) {
	$arParams['SORTER_TEMPLATE_NAME_2'] = GetMessage('SORTER_TEMPLATE_NAME_gallery');
}
if( empty($arParams['SORTER_TEMPLATE_NAME_3']) || $arParams['SORTER_TEMPLATE_NAME_3']=='' ) {
	$arParams['SORTER_TEMPLATE_NAME_3'] = GetMessage('SORTER_TEMPLATE_NAME_table');
}
if( empty($arParams['SORTER_DEFAULT_TEMPLATE']) || $arParams['SORTER_DEFAULT_TEMPLATE']=='' ) {
	$arParams['SORTER_DEFAULT_TEMPLATE'] = 'showcase';
}
// mods
if( empty($arParams['MODS_BLOCK_NAME']) || $arParams['MODS_BLOCK_NAME']=='' ) {
	$arParams['MODS_BLOCK_NAME'] = GetMessage('MODS_BLOCK_NAME');
}
// bigdata
if( empty($arParams['BIGDATA_BLOCK_NAME']) || $arParams['BIGDATA_BLOCK_NAME']=='' ) {
	$arParams['BIGDATA_BLOCK_NAME'] = GetMessage('BIGDATA_BLOCK_NAME');
}