<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/** @var array $arCurrentValues */

$arTemplateParameters = array(
	'TEMPLATE_STYLE' => array(
		'NAME'              => GetMessage('TEMPLATE_STYLE'),
		'TYPE'              => 'LIST',
		'VALUES'            => Array(
			'uikit'        => 'uikit',
			'uikit-light'  => 'uikit-light',
			'uikit-chosen' => 'uikit-chosen',
			'bootstrap3'   => 'Bootstrap 3',
		),
		'DEFAULT'           => 'uikit',
		'ADDITIONAL_VALUES' => 'Y',
		'REFRESH'           => 'N',
		'PARENT'            => 'VISUAL',
	),

);