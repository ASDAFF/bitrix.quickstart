<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
	return;
}
$arTemplateParameters = array(
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
	'USE_BUY1CLICK' => array(
		'NAME' => getMessage('RS_SLINE.USE_BUY1CLICK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	'ADDITIONAL_PICT_PROP_'.$IBLOCK_ID => array(
		'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => $arProperty,
		'DEFAULT' => '',
		'ADDITIONAL_VALUES' => 'Y',
	)
);

$arOffers = CIBlockPriceTools::GetOffersIBlock($IBLOCK_ID);
$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID']: 0;

if ($OFFERS_IBLOCK_ID)
{
	$arTemplateParameters['OFFERS_PROPERTIES_MAX'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFERS_PROPERTIES_MAX'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
	$arProperty_Offers = array();
	$rsProp = CIBlockProperty::GetList(array('sort'=>'asc', 'name'=>'asc'), array('IBLOCK_ID'=>$OFFERS_IBLOCK_ID, 'ACTIVE'=>'Y'));
	while ($arr=$rsProp->Fetch()) {
		$arr['ID'] = intval($arr['ID']);
		if ($arOffers['OFFERS_PROPERTY_ID'] == $arr['ID'])
			continue;
		$strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];
		if ('' == $arr['CODE'])
			$arr['CODE'] = $arr['ID'];
		$arProperty_Offers[$arr['CODE']] = $strPropName;
	}
	$arTemplateParameters['ADDITIONAL_PICT_PROP_'.$OFFERS_IBLOCK_ID] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('OFFER_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'DEFAULT' => '-',
	);
	$arTemplateParameters['ARTICLE_PROPP_'.$OFFERS_IBLOCK_ID] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('OFFER_ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'DEFAULT' => '-',
	);
	$arTemplateParameters['TREE_PROPSP_'.$OFFERS_IBLOCK_ID] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('OFFER_TREE_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['TREE_COLOR_PROPSP_'.$OFFERS_IBLOCK_ID] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('OFFER_TREE_COLOR_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['TREE_BTN_PROPSP_'.$OFFERS_IBLOCK_ID] = array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('OFFER_TREE_BTN_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '',
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
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_SERVICES'),
		'TYPE' => 'LIST',
		'VALUES' => $arSocialServices,
		'MULTIPLE' => 'Y',
		'DEFAULT' => '',
		'ADDITIONAL_VALUES' => 'Y',
	);
	$arTemplateParameters['SOCIAL_COUNTER'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_COUNTER'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['SOCIAL_COPY'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.SOCIAL_COPY'),
        'TYPE' => 'LIST',
        'VALUES' => $arSocialCopy
	);
	$arTemplateParameters['SOCIAL_LIMIT'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_LIMIT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);
	$arTemplateParameters['SOCIAL_SIZE'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_SIZE'),
        'TYPE' => 'LIST',
        'VALUES' => $arSocialSize
	);
}