<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('RS.FLYAWAY.FORMS_SETTINGS.NAME'),
	'DESCRIPTION' => GetMessage('RS.FLYAWAY.FORMS_SETTINGS.DESCRIPTION'),
	'ICON' => '',
	'PATH' => array(
		'ID' => 'alfa_com',
		'SORT' => 2000,
		'NAME' => GetMessage('RS.FLYAWAY.FORMS_SETTINGS.PATH_NAME_REDSIGN'),
		'CHILD' => array(
			'ID' => 'FLYAWAY',
			'NAME' => GetMessage('RS.FLYAWAY.FORMS_SETTINGS.NAMEPATH_NAME_FLYAWAY'),
			'SORT' => 8000,
		),
	),
);