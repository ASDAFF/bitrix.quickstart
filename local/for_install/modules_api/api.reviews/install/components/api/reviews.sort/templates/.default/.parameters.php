<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/** @var array $arCurrentValues */

$arTemplateParameters = array(
	'THEME' => array(
		'NAME'              => GetMessage('THEME'),
		'TYPE'              => 'LIST',
		'VALUES'            => GetMessage('THEME_VALUES'),
		'DEFAULT'           => 'bright-blue',
		'ADDITIONAL_VALUES' => 'Y',
		'REFRESH'           => 'N',
		'PARENT'            => 'BASE',
	),
);