<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!\Bitrix\Main\Loader::includeModule('iblock')
	|| !\Bitrix\Main\Loader::includeModule('catalog')) {
	return;
}
$arSocialServices = array(
	'blogger' => getMessage('RS_SLINE.SOCIAL_SERVICES.BLOGGER'),
	'delicious' => getMessage('RS_SLINE.SOCIAL_SERVICES.DELICIOUS'),
	'diary' => getMessage('RS_SLINE.SOCIAL_SERVICES.DIARY'),
	'digg' => getMessage('RS_SLINE.SOCIAL_SERVICES.DIGG'),
	'evernote' => getMessage('RS_SLINE.SOCIAL_SERVICES.EVERNOTE'),
	'facebook' => getMessage('RS_SLINE.SOCIAL_SERVICES.FACEBOOK'),
	'friendfeed' => getMessage('RS_SLINE.SOCIAL_SERVICES.FRIENDFEED'),
	'gplus' => getMessage('RS_SLINE.SOCIAL_SERVICES.GPLUS'),
	'juick' => getMessage('RS_SLINE.SOCIAL_SERVICES.JUICK'),
	'liveinternet' => getMessage('RS_SLINE.SOCIAL_SERVICES.LIVEINTERNET'),
	'linkedin' => getMessage('RS_SLINE.SOCIAL_SERVICES.LINKEDIN'),
	'lj' => getMessage('RS_SLINE.SOCIAL_SERVICES.LJ'),
	'moikrug' => getMessage('RS_SLINE.SOCIAL_SERVICES.MOIKRUG'),
	'moimir' => getMessage('RS_SLINE.SOCIAL_SERVICES.MOIMIR'),
	'myspace' => getMessage('RS_SLINE.SOCIAL_SERVICES.MYSPACE'),
	'odnoklassniki' => getMessage('RS_SLINE.SOCIAL_SERVICES.ODNOKLASSNIKI'),
	'pinterest' => getMessage('RS_SLINE.SOCIAL_SERVICES.PINTEREST'),
	'surfingbird' => getMessage('RS_SLINE.SOCIAL_SERVICES.SURFINGBIRD'),
	'tutby' => getMessage('RS_SLINE.SOCIAL_SERVICES.TUTBY'),
	'twitter' => getMessage('RS_SLINE.SOCIAL_SERVICES.TWITTER'),
	'vkontakte' => getMessage('RS_SLINE.SOCIAL_SERVICES.VKONTAKTE'),
	'yazakladki' => getMessage('RS_SLINE.SOCIAL_SERVICES.YAZAKLADKI'),
);

$arSocialServicesSkins = array(
	'default' => getMessage('RS_SLINE.SOCIAL_SKIN.DEFAULT'),
	'dark' => getMessage('RS_SLINE.SOCIAL_SKIN.DARK'),
	'counter' => getMessage('RS_SLINE.SOCIAL_SKIN.COUNTER'),
);

$arSocialServicesPopupSkins = array(
	'none' => getMessage('RS_SLINE.SOCIAL_POPUP_TYPE.NONE'),
	'button' => getMessage('RS_SLINE.SOCIAL_POPUP_TYPE.BUTTON'),
	'link' => getMessage('RS_SLINE.SOCIAL_POPUP_TYPE.LINK'),
	'icon' => getMessage('RS_SLINE.SOCIAL_POPUP_TYPE.ICON'),
	'big' =>  getMessage('RS_SLINE.SOCIAL_POPUP_TYPE.BIG'),
);

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$IBLOCK_ID = $arCurrentValues['IBLOCK_ID'];
$arProperty = array();
if (0 < intval($IBLOCK_ID)) {
	$rsProp = CIBlockProperty::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array('IBLOCK_ID'=>$IBLOCK_ID, 'ACTIVE'=>'Y'));
	while($arr=$rsProp->Fetch()) {
		$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$arTemplateParameters = array(
	'RS_SECONDARY_ACTION_VARIABLE' => array(
		'NAME' => getMessage('RS_SLINE.SECONDARY_ACTION_VARIABLE'),
		'TYPE' => 'STRING',
	),
	'RS_TAB_ID' => array(
		'NAME' => getMessage('RS_SLINE.TAB_ID'),
		'TYPE' => 'STRING',
	),
	// PRICES
	'SHOW_OLD_PRICE' => array(
		'PARENT' => 'PRICES',
		'NAME' => getMessage('RS_SLINE.SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_DISCOUNT_PERCENT' => array(
		'PARENT' => 'PRICES',
		'NAME' => getMessage('RS_SLINE.SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	),
	'ADDITIONAL_PICT_PROP' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),/*
	'ARTICLE_PROP' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.ITEM_ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),*/
	'ICON_NOVELTY_PROP' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.ICON_NOVELTY_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'NOVELTY_TIME' => array(
		'NAME' => getMessage('RS_SLINE.NOVELTY_TIME'),
		'TYPE' => 'STRING',
		'DEFAULT' => '720',
	),
	'ICON_DEALS_PROP' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.ICON_DEALS_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'ICON_DISCOUNT_PROP' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.ICON_DISCOUNT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'ICON_HITS_PROP' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.ICON_HITS_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'USE_FAVORITE' => array(
		'NAME' => getMessage('RS_SLINE.USE_FAVORITE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'USE_SHARE' => array(
		'NAME' => getMessage('RS_SLINE.USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y',
	),
/*
	'USE_AJAXPAGES' => array(
		'PARENT' => 'PAGER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.USE_AJAXPAGES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y',
	),
	'USE_STORE' => array(
		'NAME' => getMessage('RS_SLINE.USE_STORE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y',
	),
*/
	'SHOW_SECTION_URL' => array(
		'NAME' => getMessage('RS_SLINE.SHOW_SECTION_URL'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'TEMPLATE_AJAXID' => array(
		'PARENT' => 'PAGER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.TEMPLATE_AJAXID'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'COMPARE_URL' => array(
		'NAME' => getMessage('RS_SLINE.COMPARE_URL'),
		'TYPE' => 'STRING',
		'DEFAULT' => '/catalog/compare/',
	),
);
/*
if ('Y' == $arCurrentValues['USE_AJAXPAGES']) {
	$arTemplateParameters['USE_AUTO_AJAXPAGES'] = array(
		'PARENT' => 'PAGER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.USE_AUTO_AJAXPAGES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['TEMPLATE_AJAXID'] = array(
		'PARENT' => 'PAGER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.TEMPLATE_AJAXID'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'ajaxpages_catalog_id',
	);
}

if ('Y' == $arCurrentValues['USE_STORE']) {
	$arTemplateParameters['USE_MIN_AMOUNT'] = array(
		'NAME' => getMessage('RS_SLINE.USE_MIN_AMOUNT'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	);
	$arTemplateParameters['MIN_AMOUNT'] = array(
		'NAME' => getMessage('RS_SLINE.MIN_AMOUNT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
	$arTemplateParameters['STORE_TITLE'] = array(
		'NAME' => getMessage('RS_SLINE.STORE_TITLE'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
}
*/
if ('Y' == $arCurrentValues['USE_SHARE']) {
	$arTemplateParameters['SOCIAL_SERVICES'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_SERVICES'),
		'TYPE' => 'LIST',
		'VALUES' => $arSocialServices,
		'MULTIPLE' => 'Y',
		'DEFAULT' => '',
		'ADDITIONAL_VALUES' => 'Y',
	);
	$arTemplateParameters['SOCIAL_SKIN'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_SKIN'),
		'TYPE' => 'LIST',
		'VALUES' => $arSocialServicesSkins,
		'DEFAULT' => 'default',
		'ADDITIONAL_VALUES' => 'Y',
	);
	$arTemplateParameters['SOCIAL_POPUP_TYPE'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_POPUP_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $arSocialServicesPopupSkins,
		'DEFAULT' => 'none',
		'ADDITIONAL_VALUES' => 'Y',
		'REFRESH' => 'Y',
	);
}

$arOffers = CIBlockPriceTools::GetOffersIBlock($IBLOCK_ID);
$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID']: 0;

if ($OFFERS_IBLOCK_ID) {
	$arProperty_Offers = array();
	$rsProp = CIBlockProperty::GetList(array('sort'=>'asc', 'name'=>'asc'), array('IBLOCK_ID'=>$OFFERS_IBLOCK_ID, 'ACTIVE'=>'Y'));
	while($arr=$rsProp->Fetch()) {
		$arr['ID'] = intval($arr['ID']);
		if ($arOffers['OFFERS_PROPERTY_ID'] == $arr['ID'])
			continue;
		$strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];
		if ('' == $arr['CODE'])
			$arr['CODE'] = $arr['ID'];
		$arProperty_Offers[$arr['CODE']] = $strPropName;
	}
	$arTemplateParameters['OFFER_ADDITIONAL_PICT_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.OFFER_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'DEFAULT' => '-',
	);
	/*
	$arTemplateParameters['OFFER_ARTICLE_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.OFFER_ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'DEFAULT' => '-',
	);
	*/
	$arTemplateParameters['OFFER_TREE_PROPS'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_TREE_COLOR_PROPS'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_COLOR_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_TREE_BTN_PROPS'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_BTN_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '',
	);
}