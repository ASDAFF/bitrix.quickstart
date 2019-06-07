<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/** @var array $arCurrentValues */

$arTemplateParameters = array(
	'THEME' => array(
		'NAME'              => GetMessage('API_SP_THEME'),
		'TYPE'              => 'LIST',
		'VALUES'            => GetMessage('API_SP_THEME_VALUES'),
		'DEFAULT'           => 'list',
		'ADDITIONAL_VALUES' => 'Y',
		'REFRESH'           => 'N',
		'PARENT'            => 'VISUAL',
	),
);