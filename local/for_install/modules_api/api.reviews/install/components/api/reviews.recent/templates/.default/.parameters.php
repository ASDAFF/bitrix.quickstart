<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arCurrentValues */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arTemplateParameters = array(
	 'THEME' => array(
			'NAME'              => Loc::getMessage('THEME'),
			'TYPE'              => 'LIST',
			'VALUES'            => Loc::getMessage('THEME_VALUES'),
			'DEFAULT'           => 'flat',
			'ADDITIONAL_VALUES' => 'Y',
			'PARENT'            => 'BASE',
	 ),
	 'COLOR' => array(
			'NAME'              => Loc::getMessage('COLOR'),
			'TYPE'              => 'LIST',
			'VALUES'            => Loc::getMessage('COLOR_VALUES'),
			'DEFAULT'           => 'orange1',
			'ADDITIONAL_VALUES' => 'Y',
			'PARENT'            => 'BASE',
	 ),
);