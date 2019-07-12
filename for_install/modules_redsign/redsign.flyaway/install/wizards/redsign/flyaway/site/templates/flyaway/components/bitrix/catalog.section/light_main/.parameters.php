<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!CModule::IncludeModule('iblock'))
	return;
if (!CModule::IncludeModule('redsign.flyaway'))
	return;
if (!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RSFLYAWAY_PROP_PRICE' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_PRICE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['N'],
	),
	'RSFLYAWAY_PROP_DISCOUNT' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_DISCOUNT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['N'],
	),
	'RSFLYAWAY_PROP_CURRENCY' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_CURRENCY'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSFLYAWAY_PROP_PRICE_DECIMALS' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_PRICE_DECIMALS'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'0' => '0',
			'1' => '1',
			'2' => '2',
		),
		'DEFAULT' => '0',
	),
	'RSFLYAWAY_PROP_QUANTITY' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_QUANTITY'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['N'],
	),
	'RSFLYAWAY_PROP_MORE_PHOTO' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
	'RSFLYAWAY_PROP_ARTICLE' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_ARTICLE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSFLYAWAY_PROP_OFF_POPUP' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_OFF_POPUP'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
	'RSFLYAWAY_TITLE_SECTION' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.TITLE_SECTION'),
		'TYPE' => 'STRING',
		'VALUE' => '',
	),
);
