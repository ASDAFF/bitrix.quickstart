<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/** @var array $arCurrentValues */

$arTemplateParameters = array(
	'THEME' => array(
		'NAME'              => GetMessage('API_FEX_THEME'),
		'TYPE'              => 'LIST',
		'VALUES'            => Array(
			'gradient' => 'gradient',
		),
		'DEFAULT'           => 'gradient',
		'ADDITIONAL_VALUES' => 'Y',
		'REFRESH'           => 'N',
		'PARENT'            => 'VISUAL',
	),
	'COLOR' => array(
		'NAME'              => GetMessage('API_FEX_COLOR'),
		'TYPE'              => 'LIST',
		'VALUES'            => Array(
			'default' => 'default',
		),
		'DEFAULT'           => 'default',
		'ADDITIONAL_VALUES' => 'Y',
		'REFRESH'           => 'N',
		'PARENT'            => 'VISUAL',
	),
);