<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	'BLOCK_TITLE' => array(
		'NAME' => GetMessage('MSG_BLOCK_TITLE'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'LVL1_COUNT' => array(
		'NAME' => GetMessage('MSG_LVL1_COUNT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '10',
	),
	'LVL2_COUNT' => array(
		'NAME' => GetMessage('MSG_LVL2_COUNT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '5',
	),
	'ELLIPSIS_NAMES' => array(
		'NAME' => GetMessage('MSG_ELLIPSIS_NAMES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
);