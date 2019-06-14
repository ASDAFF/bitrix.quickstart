<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule("api.yashare"))
	return;

$arComponentParameters = array(
	'GROUPS'     => array(
		'GROUP_TWITTER'  => array(
			'NAME' => Loc::getMessage('GROUP_TWITTER'),
			'SORT' => 500,
		),
		'GENERAL_SHARE'  => array(
			'NAME' => Loc::getMessage('GROUP_GENERAL_SHARE'),
			'SORT' => 510,
		),
		'SEPARATE_SHARE'  => array(
			'NAME' => Loc::getMessage('GROUP_SEPARATE_SHARE'),
			'SORT' => 520,
		),
	),
	'PARAMETERS' => array(

		'QUICKSERVICES' => Array(
			'PARENT'            => 'BASE',
			'NAME'              => Loc::getMessage('QUICKSERVICES'),
			'VALUES'            => Loc::getMessage('QUICKSERVICES_VALUES'),
			'TYPE'              => 'LIST',
			"MULTIPLE"          => "Y",
			"SIZE"              => 10,
			"ADDITIONAL_VALUES" => "N",
			'DEFAULT'           => array('vkontakte', 'facebook', 'odnoklassniki', 'moimir', 'gplus', 'twitter', 'viber', 'whatsapp'),
		),
		'LANG'          => array(
			'PARENT'            => 'BASE',
			'NAME'              => Loc::getMessage('LANG'),
			'VALUES'            => Loc::getMessage('LANG_VALUES'),
			'TYPE'              => 'LIST',
			"ADDITIONAL_VALUES" => "N",
			"MULTIPLE"          => "N",
			'DEFAULT'           => array('ru'),
		),
		'SIZE'          => array(
			'PARENT'            => 'BASE',
			'NAME'              => Loc::getMessage('SIZE'),
			'VALUES'            => Loc::getMessage('SIZE_VALUES'),
			'TYPE'              => 'LIST',
			"ADDITIONAL_VALUES" => "N",
			"MULTIPLE"          => "N",
			'DEFAULT'           => array('m'),
		),
		'TYPE'          => Array(
			'PARENT'            => 'BASE',
			'NAME'              => Loc::getMessage('TYPE'),
			'VALUES'            => Loc::getMessage('TYPE_VALUES'),
			'TYPE'              => 'LIST',
			"ADDITIONAL_VALUES" => "N",
			"MULTIPLE"          => "N",
			"REFRESH"           => "Y",
			'DEFAULT'           => array('counter'),
		),
		'UNUSED_CSS'    => array(
			'PARENT'  => 'BASE',
			'NAME'    => Loc::getMessage('UNUSED_CSS'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
		),

	  //GROUP_TWITTER
		'twitter_hashtags' => array(
			 'PARENT'  => 'GROUP_TWITTER',
			 'NAME'    => Loc::getMessage('twitter_hashtags'),
			 'TYPE'    => 'STRING',
		),

		//GENERAL_SHARE
		'DATA_TITLE' => array(
			'PARENT'  => 'GENERAL_SHARE',
			'NAME'    => Loc::getMessage('DATA_TITLE'),
			'TYPE'    => 'STRING',
		),
		'DATA_URL' => array(
			'PARENT'  => 'GENERAL_SHARE',
			'NAME'    => Loc::getMessage('DATA_URL'),
			'TYPE'    => 'STRING',
		),
		'DATA_IMAGE' => array(
			'PARENT'  => 'GENERAL_SHARE',
			'NAME'    => Loc::getMessage('DATA_IMAGE'),
			'TYPE'    => 'STRING',
		),
		'DATA_DESCRIPTION' => array(
			'PARENT'  => 'GENERAL_SHARE',
			'NAME'    => Loc::getMessage('DATA_DESCRIPTION'),
			'TYPE'    => 'STRING',
		),

		//SEPARATE_SHARE
		'SHARE_SERVICES' => Array(
			'PARENT'            => 'SEPARATE_SHARE',
			'NAME'              => Loc::getMessage('SHARE_SERVICES'),
			'VALUES'            => Loc::getMessage('QUICKSERVICES_VALUES'),
			'TYPE'              => 'LIST',
			"MULTIPLE"          => "Y",
			"SIZE"              => 10,
			"ADDITIONAL_VALUES" => "N",
			'DEFAULT'           => "",
			'REFRESH'           => "Y",
		),
	),
);

//limit
if($arCurrentValues['TYPE'] == 'limit')
{
	$arComponentParameters['PARAMETERS']['LIMIT'] = array(
		'PARENT'  => 'BASE',
		'NAME'    => Loc::getMessage('LIMIT'),
		'TYPE'    => 'STRING',
		'DEFAULT' => Loc::getMessage('LIMIT_DEFAULT'),
	);
	$arComponentParameters['PARAMETERS']['COPY'] = array(
		'PARENT'            => 'BASE',
		'NAME'              => Loc::getMessage('COPY'),
		'VALUES'            => Loc::getMessage('COPY_VALUES'),
		'TYPE'              => 'LIST',
		"ADDITIONAL_VALUES" => "N",
		"MULTIPLE"          => "N",
		"REFRESH"           => "N",
		'DEFAULT'           => array('first'),
	);
	$arComponentParameters['PARAMETERS']['POPUP_DIRECTION'] = array(
		'PARENT'            => 'BASE',
		'NAME'              => Loc::getMessage('POPUP_DIRECTION'),
		'VALUES'            => Loc::getMessage('POPUP_DIRECTION_VALUES'),
		'TYPE'              => 'LIST',
		"ADDITIONAL_VALUES" => "N",
		"MULTIPLE"          => "N",
		"REFRESH"           => "N",
		'DEFAULT'           => array('bottom'),
	);
	$arComponentParameters['PARAMETERS']['POPUP_POSITION'] = array(
		'PARENT'            => 'BASE',
		'NAME'              => Loc::getMessage('POPUP_POSITION'),
		'VALUES'            => Loc::getMessage('POPUP_POSITION_VALUES'),
		'TYPE'              => 'LIST',
		"ADDITIONAL_VALUES" => "N",
		"MULTIPLE"          => "N",
		"REFRESH"           => "N",
		'DEFAULT'           => array('inner'),
	);
}


if($arCurrentValues['SHARE_SERVICES'])
{
	$arServiceLang = Loc::getMessage('QUICKSERVICES_VALUES');

	$i = 0;
	foreach($arCurrentValues['SHARE_SERVICES'] as $service)
	{
		$arComponentParameters['GROUPS'][ $service ] = array(
			'NAME' => $arServiceLang[$service],
			'SORT' => 1000 + $i++,
		);

		$arComponentParameters['PARAMETERS'][$service.'_title'] = array(
			'PARENT'  => $service,
			'TYPE'    => 'STRING',
			'NAME'    => $service.':title',
		);
		$arComponentParameters['PARAMETERS'][$service.'_url'] = array(
			'PARENT'  => $service,
			'TYPE'    => 'STRING',
			'NAME'    => $service.':url',
		);
		$arComponentParameters['PARAMETERS'][$service.'_description'] = array(
			'PARENT'  => $service,
			'TYPE'    => 'STRING',
			'NAME'    => $service.':description',
		);
		$arComponentParameters['PARAMETERS'][$service.'_image'] = array(
			'PARENT'  => $service,
			'TYPE'    => 'STRING',
			'NAME'    => $service.':image',
		);
	}
}