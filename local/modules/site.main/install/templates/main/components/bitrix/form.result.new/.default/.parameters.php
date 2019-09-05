<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arTemplateParameters = array(
	'HIDE_TITLE' => Array(
		'NAME' => GetMessage('HIDE_TITLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SCROLL_TO' => Array(
		'NAME' => GetMessage('SCROLL_TO'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'POPUP_MODE' => Array(
		'NAME' => GetMessage('POPUP_MODE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	)
);