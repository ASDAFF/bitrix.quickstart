<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

/** @global array $arCurrentValues */

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arTemplateParameters = array(
	'SHOW_CATALOG' => array(
		'NAME' => Loc::getMessage('PRMEDIA_MM_MH_SHOW_CATALOG'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	)
);
if ($arCurrentValues['SHOW_CATALOG'] === 'Y')
{
	$arMenuItems = array();
	$menu = new CMenu('top');
	$menu->Init('/');
	foreach ($menu->arMenu as $menuItem)
	{
		$arMenuItems[$menuItem[1]] = $menuItem[0];
	}

	$arTemplateParameters['CATALOG_MENU_ITEM'] = array(
		'NAME' => Loc::getMessage('PRMEDIA_MM_MH_CATALOG_MENU_ITEM'),
		'TYPE' => 'LIST',
		'VALUES' => $arMenuItems
	);
}

$arTemplateParameters['SHOW_SEARCH'] = array(
	'NAME' => Loc::getMessage('PRMEDIA_MM_MH_SHOW_SEARCH'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);