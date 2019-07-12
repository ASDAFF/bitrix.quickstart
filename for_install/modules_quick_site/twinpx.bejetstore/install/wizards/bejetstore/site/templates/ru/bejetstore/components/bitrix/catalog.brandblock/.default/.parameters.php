<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['WIDTH'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IBLOCK_CB_TMPL_WIDTH'),
	'TYPE' => 'STRING',
	'DEFAULT' => 120
);

$arTemplateParameters['HEIGHT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IBLOCK_CB_TMPL_HEIGHT'),
	'TYPE' => 'STRING',
	'DEFAULT' => 50
);

$arTemplateParameters['WIDTH_SMALL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IBLOCK_CB_TMPL_WIDTH_SMALL'),
	'TYPE' => 'STRING',
	'DEFAULT' => 21
);

$arTemplateParameters['HEIGHT_SMALL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IBLOCK_CB_TMPL_HEIGHT_SMALL'),
	'TYPE' => 'STRING',
	'DEFAULT' => 17
);

?>