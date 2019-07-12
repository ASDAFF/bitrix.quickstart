<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arTemplateParameters = array(
	'RSFLYAWAY_PROP_ARTICLE' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_ARTICLE'),
		'TYPE' => 'STRING',
	),
    'RSFLYAWAY_PROP_SKU_ARTICLE' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_SKU_ARTICLE'),
		'TYPE' => 'STRING',
	),
    'RSFLYAWAY_AVAL_BASKET' => array(
    'NAME' => Loc::getMessage('RS.FLYAWAY.AVAL_BASKET'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
  ),
);
