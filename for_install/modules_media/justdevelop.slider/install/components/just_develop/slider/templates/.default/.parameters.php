<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['WIDTH'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IBLOCK_CB_TMPL_WIDTH'),
	'TYPE' => 'STRING',
	'DEFAULT' => 0
);

$arTemplateParameters['HEIGHT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IBLOCK_CB_TMPL_HEIGHT'),
	'TYPE' => 'STRING',
	'DEFAULT' => 0
);
$arTemplateParameters['INTERVAL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IBLOCK_CB_TMPL_INTERVAL'),
	'TYPE' => 'STRING',
	'DEFAULT' => 5
);


?>