<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arTemplateParameters */
/** @var array $arCurrentValues */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arTemplateParameters = array(
	'TEMPLATE_THEME' => array(
		'PARENT'            => 'THEME_SETTINGS',
		'TYPE'              => 'LIST',
		'NAME'              => Loc::getMessage('API_FD_TPL_PARAM_TEMPLATE_STYLE'),
		'VALUES'            => Loc::getMessage('API_FD_TPL_PARAM_TEMPLATE_STYLE_VALUES'),
		'DEFAULT'           => 'modern',
		'REFRESH'           => 'N',
		'ADDITIONAL_VALUES' => 'Y',
	),
	'TEMPLATE_COLOR' => array(
		'PARENT'            => 'THEME_SETTINGS',
		'TYPE'              => 'LIST',
		'NAME'              => Loc::getMessage('API_FD_TPL_PARAM_TEMPLATE_COLOR'),
		'VALUES'            => Loc::getMessage('API_FD_TPL_PARAM_TEMPLATE_COLOR_VALUES'),
		'DEFAULT'           => 'blue1',
		'REFRESH'           => 'N',
		'ADDITIONAL_VALUES' => 'Y',
	),
	'TEMPLATE_BG_COLOR' => array(
		'PARENT'            => 'THEME_SETTINGS',
		'TYPE'              => 'COLORPICKER',
		'NAME'              => Loc::getMessage('API_FD_TPL_PARAM_TEMPLATE_BG_COLOR'),
		'DEFAULT'           => '',
	),
);