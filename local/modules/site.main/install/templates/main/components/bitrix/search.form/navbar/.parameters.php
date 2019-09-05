<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arTemplateParameters = array(
	'USE_SUGGEST' => Array(
		'NAME' => GetMessage('TP_BSF_USE_SUGGEST'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'USE_PLACEHOLDER' => Array(
		'NAME' => GetMessage('TP_BSF_USE_PLACEHOLDER'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'USE_REQUIRED' => Array(
		'NAME' => GetMessage('TP_BSF_USE_REQUIRED'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
);