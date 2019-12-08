<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('NAME_ADD2BASKET'),
	'DESCRIPTION' => GetMessage('DESCR_ADD2BASKET'),
	'ICON' => '',
	'PATH' => array(
		'ID' => 'alfa_com',
		'SORT' => 2000,
		'NAME' => GetMessage('COMPONENTS_ADD2BASKET'),
		'CHILD' => array(
			'ID' => 'kit',
			'NAME' => GetMessage('KIT_ADD2BASKET'),
			'SORT' => 8000,
		),
	),
);