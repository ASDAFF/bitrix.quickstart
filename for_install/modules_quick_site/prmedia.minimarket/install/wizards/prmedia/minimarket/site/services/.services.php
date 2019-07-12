<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arServices = array(
	'main' => array(
		'NAME' => Loc::getMessage('PRMEDIA_WMM_SERVICES_MAIN_NAME'),
		'STAGES' => array(
			'files.php',
			'template.php'
		)
	),
	'sale' => array(
		'NAME' => Loc::getMessage('PRMEDIA_WMM_SERVICES_SALE_NAME'),
		'STAGES' => array(
			'step1.php',
			'delivery.php',
			'step2.php'
		)
	),
	'iblock' => array(
		'NAME' => Loc::getMessage('PRMEDIA_WMM_SERVICES_IBLOCK_NAME'),
		'STAGES' => array(
			'types.php',
			'catalog_iblock.php'
		)
	)
);