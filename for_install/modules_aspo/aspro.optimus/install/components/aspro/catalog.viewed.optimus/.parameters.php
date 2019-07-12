<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
	'PARAMETERS' => array(
		'TITLE_BLOCK' => array(
			'NAME' => GetMessage('T_TITLE_BLOCK'),
			'TYPE' => 'TEXT',
			'DEFAULT' => GetMessage('T_TITLE_BLOCK_DEFAULT'),
		),
		'SHOW_MEASURE' => array(
			'NAME' => GetMessage('T_SHOW_MEASURE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
	)
);