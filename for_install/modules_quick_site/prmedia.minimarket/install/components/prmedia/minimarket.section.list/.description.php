<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	'NAME' => Loc::getMessage('PRMEDIA_MM_MSL_NAME'),
	'DESCRIPTION' => Loc::getMessage('PRMEDIA_MM_MSL_DESCRIPTION'),
	'SORT' => 20,
	'CACHE_PATH' => 'Y',
	'PATH' => array(
		'ID' => 'prmedia_minimarket',
		'NAME' => Loc::getMessage('PRMEDIA_MM_MSL_PRMEDIA_MINIMARKET_NAME')
	),
);