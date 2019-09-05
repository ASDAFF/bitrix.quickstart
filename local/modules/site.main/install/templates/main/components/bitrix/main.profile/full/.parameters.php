<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arTemplateParameters = array(
	'SHOW_PERSONAL' => array(
		'NAME' => GetMessage('SHOW_PERSONAL'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_WORK' => array(
		'NAME' => GetMessage('SHOW_WORK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_TZ' => array(
		'NAME' => GetMessage('SHOW_TZ'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_FORUM' => array(
		'NAME' => GetMessage('SHOW_FORUM'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_BLOG' => array(
		'NAME' => GetMessage('SHOW_BLOG'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_LEARNING' => array(
		'NAME' => GetMessage('SHOW_LEARNING'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_ADMIN' => array(
		'NAME' => GetMessage('SHOW_ADMIN'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
);