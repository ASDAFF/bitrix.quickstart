<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

/** @global array $arCurrentValues */

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

// include required modules
if (!\Bitrix\Main\Loader::includeModule('iblock'))
{
	return;
}

// iblock types and iblocks
$arTypes = CIBlockParameters::GetIBlockTypes();
$arBlocks = array();
$arFilter = array(
	'SITE_ID' => $_REQUEST['site'],
	'TYPE' => ($arCurrentValues['IBLOCK_TYPE_ID'] != '-' ? $arCurrentValues['IBLOCK_TYPE_ID'] : '')
);
$rsBlock = CIBlock::GetList(false, $arFilter);
while ($arBlock = $rsBlock->Fetch())
{
	$arBlocks[$arBlock['ID']] = '[' . $arBlock['ID'] . '] ' . $arBlock['NAME'];
}

$arComponentParameters = array(
	'PARAMETERS' => array(
		'IBLOCK_TYPE_ID' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('IBLOCK_TYPE_ID'),
			'TYPE' => 'LIST',
			'VALUES' => $arTypes,
			'REFRESH' => 'Y'
		),
		'IBLOCK_ID' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('IBLOCK_ID'),
			'TYPE' => 'LIST',
			'VALUES' => $arBlocks,
			'DEFAULT' => '={$_REQUEST["ID"]}',
			'REFRESH' => 'Y'
		),
		'SECTION_URL' => CIBlockParameters::GetPathTemplateParam(
			'SECTION',
			'SECTION_URL',
			Loc::getMessage('SECTION_URL'),
			'',
			'URL_TEMPLATES'
		),
		'TOP_DEPTH' => array(
			'PARENT' => 'DATA_SOURCE',
			'NAME' => Loc::getMessage('TOP_DEPTH'),
			'TYPE' => 'LIST',
			'VALUES' => array(1 => 1, 2 => 2),
			'DEFAULT' => 2
		),
		'CACHE_TIME' => array(
			'DEFAULT' => 36000000
		),
		'CACHE_GROUPS' => array(
			'PARENT' => 'CACHE_SETTINGS',
			'NAME' => Loc::getMessage('CACHE_GROUPS'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y'
		)
	)
);