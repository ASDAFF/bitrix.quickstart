<?

use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arCurrentValues */

Loc::loadMessages(__FILE__);

$theme = (array)Loc::getMessage('THEME_VALUES');
$color = (array)Loc::getMessage('COLOR_VALUES_flat');

if($arCurrentValues['THEME']) {
	if(in_array($arCurrentValues['THEME'], $theme))
		$color = Loc::getMessage('COLOR_VALUES_' . $arCurrentValues['THEME']);
}

$arTemplateParameters = array(
	 'THEME' => array(
			'NAME'              => Loc::getMessage('THEME'),
			'TYPE'              => 'LIST',
			'VALUES'            => $theme,
			'DEFAULT'           => 'flat',
			'ADDITIONAL_VALUES' => 'Y',
			'PARENT'            => 'BASE',
			'REFRESH'           => 'Y',
	 ),
	 'COLOR' => array(
			'NAME'              => Loc::getMessage('COLOR'),
			'TYPE'              => 'LIST',
			'VALUES'            => $color,
			'DEFAULT'           => 'orange1',
			'ADDITIONAL_VALUES' => 'Y',
			'PARENT'            => 'BASE',
	 ),
);
