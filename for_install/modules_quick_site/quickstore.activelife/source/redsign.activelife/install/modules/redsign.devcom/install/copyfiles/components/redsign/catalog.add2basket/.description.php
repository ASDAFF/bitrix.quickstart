<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('ALFA_COM_NAME_ADD2BASKET'),
	'DESCRIPTION' => GetMessage('ALFA_COM_DESCR_ADD2BASKET'),
	'ICON' => '',
	'PATH' => array(
		'ID' => 'alfa_com',
		'SORT' => 2000,
		'NAME' => GetMessage('ALFA_COM_COMPONENTS_ADD2BASKET'),
		'CHILD' => array(
			'ID' => 'devcom',
			'NAME' => GetMessage('ALFA_COM_DEV_COM_ADD2BASKET'),
			'SORT' => 8000,
		),
	),
);