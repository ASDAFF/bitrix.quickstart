<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!Bitrix\Main\Loader::includeModule('iblock')
	|| !Bitrix\Main\Loader::includeModule('catalog')){
	return;
}

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
while ($arr = $rsIBlock->Fetch()){
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
}



$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$IBLOCK_ID = intval($arCurrentValues['IBLOCK_ID']);
$arProperty = array();
if(0 < intval($IBLOCK_ID)){
	$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'));
	while($arr = $rsProp->Fetch()){
		$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$arTemplateParameters = array(
	'LINKED_ITEMS_PROPS' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.LINKED_ITEMS_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
		'MULTIPLE' => 'Y',
	),
	'TAB_IBLOCK_PROPS' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.TAB_IBLOCK_PROPS'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arProperty,
		'DEFAULT' => '',
		'ADDITIONAL_VALUES' => 'Y',
	),
	'SIZE_TABLE_USER_FIELD_CODE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.SIZE_TABLE_USER_FIELD_CODE'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'UF_SIZE_TABLE',
	),
	'USE_KREDIT' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.USE_KREDIT'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'KREDIT_URL' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.KREDIT_URL'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'USE_PICTURE_ZOOM' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.USE_PICTURE_ZOOM'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'USE_PICTURE_GALLERY' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.USE_PICTURE_GALLERY'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'USE_BUY1CLICK' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.USE_BUY1CLICK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'ICON_MEN_PROP' => array(
		'NAME' => getMessage('RS_SLINE.ICON_MEN_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'ICON_WOMEN_PROP' => array(
		'NAME' => getMessage('RS_SLINE.ICON_WOMEN_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'ICON_NOVELTY_PROP' => array(
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
	'ICON_DISCOUNT_PROP' => array(
		'NAME' => getMessage('RS_SLINE.ICON_DISCOUNT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'ICON_DEALS_PROP' => array(
		'NAME' => getMessage('RS_SLINE.ICON_DEALS_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'ADDITIONAL_PICT_PROP' => array(
		'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'ARTICLE_PROP' => array(
		'NAME' => getMessage('RS_SLINE.ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'DELIVERY_PROP' => array(
		'NAME' => getMessage('RS_SLINE.DELIVERY_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'BRAND_PROP' => array(
		'NAME' => getMessage('RS_SLINE.BRAND_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
		'REFRESH' => 'Y'
	),
	'BRAND_LOGO_PROP' => array(
		'NAME' => getMessage('RS_SLINE.BRAND_LOGO_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'ACCESSORIES_PROP' => array(
		'NAME' => getMessage('RS_SLINE.ACCESSORIES_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'USE_QUANTITY_AND_STORES' => array(
		'NAME' => getMessage('RS_SLINE.USE_QUANTITY_AND_STORES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'LIKES_COUNT_PROP' => array(
		'NAME' => getMessage('RS_SLINE.LIKES_COUNT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '',
	),
	'USE_COMPARE' => array(
		'NAME' => getMessage('RS_SLINE.USE_COMPARE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'USE_LIKES' => array(
		'NAME' => getMessage('RS_SLINE.USE_LIKES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'USE_SHARE' => array(
		'NAME' => getMessage('RS_SLINE.USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'SMART_FILTER_NAME' => array(
		'NAME' => getMessage('RS_SLINE.SMART_FILTER_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'arrFilter',
	),
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
);

if ($arCurrentValues['BRAND_PROP'] != '-') {
	$arTemplateParameters['BRAND_IBLOCK_ID'] = array(
		'NAME' => getMessage('RS_SLINE.BRAND_IBLOCK_ID'),
		'TYPE' => 'LIST',
		'VALUES' => $arIBlock,
		'DEFAULT' => '',
        'REFRESH' => 'Y',
	);
	$BRAND_IBLOCK_ID = intval($arCurrentValues['BRAND_IBLOCK_ID']);
	if($BRAND_IBLOCK_ID){
		$arBrandProperty = array();
		if(0 < intval($IBLOCK_ID)){
			$rsBrandProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $BRAND_IBLOCK_ID, 'ACTIVE' => 'Y'));
			while($arr = $rsBrandProp->Fetch()){
				$arBrandProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
			}
		}
		$arTemplateParameters['BRAND_IBLOCK_BRAND_PROP'] = array(
			'NAME' => getMessage('RS_SLINE.BRAND_IBLOCK_BRAND_PROP'),
			'TYPE' => 'LIST',
			'VALUES' => array_merge($defaultListValues, $arBrandProperty),
			'DEFAULT' => '-',
		);
	}
}
if('Y' == $arCurrentValues['SHOW_SECTION_PICTURE']){
	$arTemplateParameters['SECTION_PICTURE_WIDTH'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => getMessage('RS_SLINE.SECTION_PICTURE_WIDTH'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
	$arTemplateParameters['SECTION_PICTURE_HEIGHT'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => getMessage('RS_SLINE.SECTION_PICTURE_HEIGHT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
}

$arOffers = CIBlockPriceTools::GetOffersIBlock($IBLOCK_ID);
$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID']: 0;

if($OFFERS_IBLOCK_ID){
	$arProperty_Offers = array();
	$rsProp = CIBlockProperty::GetList(array('sort'=>'asc', 'name'=>'asc'), array('IBLOCK_ID'=>$OFFERS_IBLOCK_ID, 'ACTIVE'=>'Y'));
	while($arr=$rsProp->Fetch()){
		$arr['ID'] = intval($arr['ID']);
		if ($arOffers['OFFERS_PROPERTY_ID'] == $arr['ID'])
			continue;
		$strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];
		if ('' == $arr['CODE'])
			$arr['CODE'] = $arr['ID'];
		$arProperty_Offers[$arr['CODE']] = $strPropName;
	}

	$arTemplateParameters['OFFER_ADDITIONAL_PICT_PROP'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_ARTICLE_PROP'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_TREE_PROPS'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_TREE_COLOR_PROPS'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_COLOR_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_TREE_BTN_PROPS'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_BTN_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
}

if ($arCurrentValues['USE_SHARE'] == 'Y') {

    $arSocialServices = array(
        'blogger' => getMessage('RS_SLINE.SOCIAL_SERVICES.BLOGGER'),
        'delicious' => getMessage('RS_SLINE.SOCIAL_SERVICES.DELICIOUS'),
        'digg' => getMessage('RS_SLINE.SOCIAL_SERVICES.DIGG'),
        'evernote' => getMessage('RS_SLINE.SOCIAL_SERVICES.EVERNOTE'),
        'facebook' => getMessage('RS_SLINE.SOCIAL_SERVICES.FACEBOOK'),
        'gplus' => getMessage('RS_SLINE.SOCIAL_SERVICES.GPLUS'),
        'linkedin' => getMessage('RS_SLINE.SOCIAL_SERVICES.LINKEDIN'),
        'lj' => getMessage('RS_SLINE.SOCIAL_SERVICES.LJ'),
        'moimir' => getMessage('RS_SLINE.SOCIAL_SERVICES.MOIMIR'),
        'odnoklassniki' => getMessage('RS_SLINE.SOCIAL_SERVICES.ODNOKLASSNIKI'),
        'pinterest' => getMessage('RS_SLINE.SOCIAL_SERVICES.PINTEREST'),
        'pocket' => getMessage('RS_SLINE.SOCIAL_SERVICES.POCKET'),
        'qzone' => getMessage('RS_SLINE.SOCIAL_SERVICES.QZONE'),
        'reddit' => getMessage('RS_SLINE.SOCIAL_SERVICES.REDDIT'),
        'renren' => getMessage('RS_SLINE.SOCIAL_SERVICES.RENREN'),
        'sinaWeibo ' => getMessage('RS_SLINE.SOCIAL_SERVICES.SINA_WEIBO'),
        'surfingbird' => getMessage('RS_SLINE.SOCIAL_SERVICES.SURFINGBIRD'),
        'telegram' => getMessage('RS_SLINE.SOCIAL_SERVICES.TELEGRAM'),
        'tencentWeibo' => getMessage('RS_SLINE.SOCIAL_SERVICES.TENCENT_WEIBO'),
        'tumblr' => getMessage('RS_SLINE.SOCIAL_SERVICES.TUMBLR'),
        'twitter' => getMessage('RS_SLINE.SOCIAL_SERVICES.TWITTER'),
        'viber' => getMessage('RS_SLINE.SOCIAL_SERVICES.VIBER'),
        'vkontakte' => getMessage('RS_SLINE.SOCIAL_SERVICES.VKONTAKTE'),
        'whatsapp' => getMessage('RS_SLINE.SOCIAL_SERVICES.WHATSAPP'),
    );

    $arSocialCopy = array(
        'first' => getMessage('RS_SLINE.SOCIAL_COPY.FIRST'),
        'last' => getMessage('RS_SLINE.SOCIAL_COPY.LAST'),
        'hidden' => getMessage('RS_SLINE.SOCIAL_COPY.HIDDEN'),
    );
    $arSocialSize = array(
        'm' => getMessage('RS_SLINE.SOCIAL_SIZE.M'),
        's' => getMessage('RS_SLINE.SOCIAL_SIZE.S'),
    );
	$arTemplateParameters['SOCIAL_SERVICES'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_SERVICES'),
		'TYPE' => 'LIST',
		'VALUES' => $arSocialServices,
		'MULTIPLE' => 'Y',
		'DEFAULT' => '',
		'ADDITIONAL_VALUES' => 'Y',
	);
	$arTemplateParameters['SOCIAL_COUNTER'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_COUNTER'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['SOCIAL_COPY'] = array(
        'NAME' => getMessage('RS_SLINE.SOCIAL_COPY'),
        'TYPE' => 'LIST',
        'VALUES' => $arSocialCopy
	);
	$arTemplateParameters['SOCIAL_LIMIT'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_LIMIT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
	$arTemplateParameters['SOCIAL_SIZE'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_SIZE'),
        'TYPE' => 'LIST',
        'VALUES' => $arSocialSize
	);
}