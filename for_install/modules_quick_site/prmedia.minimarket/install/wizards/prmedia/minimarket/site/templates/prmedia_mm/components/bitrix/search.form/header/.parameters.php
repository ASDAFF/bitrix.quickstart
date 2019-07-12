<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arTemplateParameters = array(
	'PLACEHOLDER_TEXT' => Array(
		'NAME' => Loc::getMessage('PRMEDIA_MM_SF_PLACEHOLDER_TEXT'),
		'TYPE' => 'TEXT',
		'DEFAULT' => Loc::getMessage('PRMEDIA_MM_SF_PLACEHOLDER_TEXT_DEFAULT')
	),
);