<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arNameTemplate = array(
	'' => Loc::getMessage('NAME_TEMPLATE_SITE'),
	'#LAST_NAME# #NAME#' => Loc::getMessage('NAME_TEMPLATE_L_N'),
	'#LAST_NAME# #NAME# #SECOND_NAME#' => Loc::getMessage('NAME_TEMPLATE_L_N_S'),
	'#LAST_NAME#, #NAME# #SECOND_NAME#' => Loc::getMessage('NAME_TEMPLATE_L,_N_S'),
	'#NAME# #SECOND_NAME# #LAST_NAME#' => Loc::getMessage('NAME_TEMPLATE_N_S_L'),
	'#NAME_SHORT# #SECOND_NAME_SHORT# #LAST_NAME#' => Loc::getMessage('NAME_TEMPLATE_NS_SS_L'),
	'#NAME_SHORT# #LAST_NAME#' => Loc::getMessage('NAME_TEMPLATE_NS_L'),
	'#LAST_NAME# #NAME_SHORT#' => Loc::getMessage('NAME_TEMPLATE_L_NS'),
	'#LAST_NAME# #NAME_SHORT# #SECOND_NAME_SHORT#' => Loc::getMessage('NAME_TEMPLATE_L_NS_SS'),
	'#LAST_NAME#, #NAME_SHORT#' => Loc::getMessage('NAME_TEMPLATE_L,_NS'),
	'#LAST_NAME#, #NAME_SHORT# #SECOND_NAME_SHORT#' => Loc::getMessage('NAME_TEMPLATE_L,_NS_SS'),
	'#NAME# #LAST_NAME#' => Loc::getMessage('NAME_TEMPLATE_N_L'),
	'#NAME# #SECOND_NAME_SHORT# #LAST_NAME#' => Loc::getMessage('NAME_TEMPLATE_N_SS_L')
);

$arComponentParameters = array(
	'PARAMETERS' => array(
		'PATH_TO_PROFILE' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('PATH_TO_PROFILE'),
			'TYPE' => 'STRING'
		),
		'NAME_TEMPLATE' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('NAME_TEMPLATE'),
			'TYPE' => 'LIST',
			'VALUES' => $arNameTemplate,
			'DEFAULT' => ''
		),
		'NAME_TEMPLATE_LOGIN' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('NAME_TEMPLATE_LOGIN'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y'
		),
		'PATH_TO_AUTH' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('PATH_TO_AUTH'),
			'TYPE' => 'STRING'
		),
		'PATH_TO_REGISTER' => array(
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('PATH_TO_REGISTER'),
			'TYPE' => 'STRING'
		),
		'CACHE_TIME' => array(
			'DEFAULT' => '7200'
		)
	)
);