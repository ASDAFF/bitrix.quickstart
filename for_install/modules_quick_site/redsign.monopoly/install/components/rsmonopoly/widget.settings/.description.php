<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('RS.MONOPOLY.WIDGET_SETTINGS.NAME'),
	'DESCRIPTION' => GetMessage('RS.MONOPOLY.WIDGET_SETTINGS.DESCRIPTION'),
	'ICON' => '',
	'PATH' => array(
		'ID' => 'alfa_com',
		'SORT' => 2000,
		'NAME' => GetMessage('RS.MONOPOLY.WIDGET_SETTINGS.PATH_NAME_REDSIGN'),
		'CHILD' => array(
			'ID' => 'monopoly',
			'NAME' => GetMessage('RS.MONOPOLY.WIDGET_SETTINGS.NAMEPATH_NAME_MONOPOLY'),
			'SORT' => 8000,
		),
	),
);