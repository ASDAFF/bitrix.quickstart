<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

CJSCore::Init(array('currency'));

if (!\Bitrix\Main\Loader::includeModule('redsign.flyaway')) {
	return;
}

global $HIDE_SIDEBAR;
$HIDE_SIDEBAR = true;

if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
	$ELEMENT_ID = IntVal($_REQUEST['element_id']);

	if ($_REQUEST['AJAX_CALL'] == 'Y' && $_REQUEST['action'] == 'get_element_json') {
		if ($ELEMENT_ID < 1) {
				$arJson = array( 'TYPE' => 'ERROR', 'MESSAGE' => 'Element id is empty' );
				echo json_encode($arJson);
				die();
		}

		global $APPLICATION,$JSON;

		$ElementID=$APPLICATION->IncludeComponent(
			'bitrix:catalog.element',
			'json',
			Array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
				'META_KEYWORDS' => $arParams['DETAIL_META_KEYWORDS'],
				'META_DESCRIPTION' => $arParams['DETAIL_META_DESCRIPTION'],
				'BROWSER_TITLE' => $arParams['DETAIL_BROWSER_TITLE'],
				'BASKET_URL' => $arParams['BASKET_URL'],
				'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
				'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
				'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
				'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
				'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
				'CACHE_TYPE' => $arParams['CACHE_TYPE'],
				'CACHE_TIME' => $arParams['CACHE_TIME'],
				'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
				'SET_TITLE' => $arParams['SET_TITLE'],
				'SET_STATUS_404' => $arParams['SET_STATUS_404'],
				'PRICE_CODE' => $arParams['PRICE_CODE'],
				'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
				'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
				'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
				'PRICE_VAT_SHOW_VALUE' => $arParams['PRICE_VAT_SHOW_VALUE'],
				'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
				'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],
				'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
				'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
				'LINK_IBLOCK_TYPE' => $arParams['LINK_IBLOCK_TYPE'],
				'LINK_IBLOCK_ID' => $arParams['LINK_IBLOCK_ID'],
				'LINK_PROPERTY_SID' => $arParams['LINK_PROPERTY_SID'],
				'LINK_ELEMENTS_URL' => $arParams['LINK_ELEMENTS_URL'],
				'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
				'OFFERS_FIELD_CODE' => $arParams['DETAIL_OFFERS_FIELD_CODE'],
				'OFFERS_PROPERTY_CODE' => $arParams['DETAIL_OFFERS_PROPERTY_CODE'],
				'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
				'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
				'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
				'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
				'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
				'OFFER_TREE_COLOR_PROPS' => $arParams['OFFER_TREE_COLOR_PROPS'],
				'OFFER_TREE_BTN_PROPS' => $arParams['OFFER_TREE_BTN_PROPS'],
				'ELEMENT_ID' => $ELEMENT_ID,
				'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
				'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
				'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
				'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
				'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
				'CURRENCY_ID' => $arParams['CURRENCY_ID'],
				'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
				// flyaway params
				"RSFLYAWAY_PROP_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_MORE_PHOTO"],
				"RSFLYAWAY_PROP_SKU_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_SKU_MORE_PHOTO"],
				// store
				'USE_STORE' => $arParams['USE_STORE'],
				'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
				'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
    } elseif($_REQUEST['AJAX_CALL'] == 'Y' && $_REQUEST['action'] == 'add2basket') {
        global $APPLICATION,$JSON;
        $APPLICATION->RestartBuffer();
        if($ELEMENT_ID<1) {
            $arJson = array( 'TYPE' => 'ERROR', 'MESSAGE' => 'Element id is empty' );
                echo json_encode($arJson);
                die();
        }

        $APPLICATION->IncludeComponent(
            "bitrix:sale.basket.basket.line",
            "add2basket",
            array(
                "PATH_TO_BASKET" => $arParams['BASKET_URL'],
                "PATH_TO_PERSONAL" => "/personal/",
                "PATH_TO_ORDER" => "/personal/order/make",
                "SHOW_PERSONAL_LINK" => "Y",
                "SHOW_NUM_PRODUCTS" => "Y",
                "SHOW_TOTAL_PRICE" => "Y",
                "SHOW_EMPTY_VALUES" => "Y",
                "SHOW_PRODUCTS" => "Y",
                "POSITION_FIXED" => "Y",
                "SHOW_DELAY" => "Y",
                "SHOW_NOTAVAIL" => "Y",
                "SHOW_SUBSCRIBE" => "Y",
                "SHOW_IMAGE" => "Y",
                "SHOW_PRICE" => "Y",
                "SHOW_SUMMARY" => "Y",
                "RSFLYAWAY_SHOW_ELEMENT" => $ELEMENT_ID
            )
        );

        die();
    } elseif ($arParams['USE_COMPARE'] == 'Y' && $_REQUEST['AJAX_CALL'] == 'Y' && ($_REQUEST['action'] == 'ADD_TO_COMPARE_LIST' || $_REQUEST['action'] == 'DELETE_FROM_COMPARE_LIST')) {
		// +++++++++++++++++++++++++++++++ add2compare +++++++++++++++++++++++++++++++ //
		global $APPLICATION, $JSON;

		$APPLICATION->IncludeComponent(
			'bitrix:catalog.compare.list',
			'json',
			array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'NAME' => $arParams['COMPARE_NAME'],
				'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
				'COMPARE_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
				'IS_AJAX_REQUEST' => 'Y',
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);

		$APPLICATION->RestartBuffer();

		if (SITE_CHARSET != 'utf-8') {
			$data = $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8');
			$json_str_utf = json_encode($data);
			$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
			echo $json_str;
		} else {
			echo json_encode($JSON);
		}

		die();
	}
	elseif ($_REQUEST['AJAX_CALL'] == 'Y' && $_REQUEST['action'] == 'UPDATE_FAVORITE') {
		global $JSON;

		$res = RSFavoriteAddDel($ELEMENT_ID);

		$APPLICATION->IncludeComponent('redsign:favorite.list','json',array());
		$APPLICATION->RestartBuffer();

		if ($res == 2) {
			$arJson = array('TYPE'=>'OK','MESSAGE'=>'Element add2favorite','ACTION'=>'ADD','HTMLBYID'=>$JSON['HTMLBYID']);
		}
		else if ($res == 1) {
			$arJson = array('TYPE'=>'OK','MESSAGE'=>'Element removed from favorite','ACTION'=>'REMOVE','HTMLBYID'=>$JSON['HTMLBYID']);
		}
		else {
			$arJson = array('TYPE'=>'ERROR','MESSAGE'=>'Bad request');
		}

		if (SITE_CHARSET != 'utf-8') {
			$data = $APPLICATION->ConvertCharsetArray($arJson, SITE_CHARSET, 'utf-8');
			$json_str_utf = json_encode($data);
			$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
			echo $json_str;
		} else {
			echo json_encode($arJson);
		}
		die();

	}
	elseif ($_REQUEST['action'] == 'UPDATE_FAVORITE') {
		$res = RSFavoriteAddDel($ELEMENT_ID);
	}
}

// popup gallery
require_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/popupgallery_catalog.php');

$arParams['HEAD_TYPE'] = RsFlyaway::getSettings('headType', 'type1');

$ElementID = $APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"flyaway",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
        "META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
        "META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
        "BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
        "SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
        "BASKET_URL" => $arParams["BASKET_URL"],
        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
        "CHECK_SECTION_ID_VARIABLE" => (isset($arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"]) ? $arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"] : ''),
        "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
        "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
        "MESSAGE_404" => $arParams["MESSAGE_404"],
        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
        "SHOW_404" => $arParams["SHOW_404"],
        "FILE_404" => $arParams["FILE_404"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
        "PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
        "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
        "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
        "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
        "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
        "LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
        "LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
        "LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
        "LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],

        "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
        "OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
        "OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
        "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
        "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
        "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
        "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],

        "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
        "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
        'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
        'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
        'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
        "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],

        'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
        'LABEL_PROP' => $arParams['LABEL_PROP'],
        'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
        'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
        'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
        'SHOW_MAX_QUANTITY' => $arParams['DETAIL_SHOW_MAX_QUANTITY'],
        'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
        'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
        'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
        'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
        'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
        'USE_VOTE_RATING' => $arParams['DETAIL_USE_VOTE_RATING'],
        'VOTE_DISPLAY_AS_RATING' => (isset($arParams['DETAIL_VOTE_DISPLAY_AS_RATING']) ? $arParams['DETAIL_VOTE_DISPLAY_AS_RATING'] : ''),
        'USE_COMMENTS' => $arParams['DETAIL_USE_COMMENTS'],
        'BLOG_USE' => (isset($arParams['DETAIL_BLOG_USE']) ? $arParams['DETAIL_BLOG_USE'] : ''),
        'BLOG_URL' => (isset($arParams['DETAIL_BLOG_URL']) ? $arParams['DETAIL_BLOG_URL'] : ''),
        'BLOG_EMAIL_NOTIFY' => (isset($arParams['DETAIL_BLOG_EMAIL_NOTIFY']) ? $arParams['DETAIL_BLOG_EMAIL_NOTIFY'] : ''),
        'VK_USE' => (isset($arParams['DETAIL_VK_USE']) ? $arParams['DETAIL_VK_USE'] : ''),
        'VK_API_ID' => (isset($arParams['DETAIL_VK_API_ID']) ? $arParams['DETAIL_VK_API_ID'] : 'API_ID'),
        'FB_USE' => (isset($arParams['DETAIL_FB_USE']) ? $arParams['DETAIL_FB_USE'] : ''),
        'FB_APP_ID' => (isset($arParams['DETAIL_FB_APP_ID']) ? $arParams['DETAIL_FB_APP_ID'] : ''),
        'BRAND_USE' => (isset($arParams['DETAIL_BRAND_USE']) ? $arParams['DETAIL_BRAND_USE'] : 'N'),
        'BRAND_PROP_CODE' => (isset($arParams['DETAIL_BRAND_PROP_CODE']) ? $arParams['DETAIL_BRAND_PROP_CODE'] : ''),
        'DISPLAY_NAME' => (isset($arParams['DETAIL_DISPLAY_NAME']) ? $arParams['DETAIL_DISPLAY_NAME'] : ''),
        'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
        'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
        "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
        "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
        "DISPLAY_PREVIEW_TEXT_MODE" => (isset($arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE']) ? $arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] : ''),
        "DETAIL_PICTURE_MODE" => (isset($arParams['DETAIL_DETAIL_PICTURE_MODE']) ? $arParams['DETAIL_DETAIL_PICTURE_MODE'] : ''),
        'ADD_TO_BASKET_ACTION' => $basketAction,
        'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
        'DISPLAY_COMPARE' => (isset($arParams['USE_COMPARE']) ? $arParams['USE_COMPARE'] : ''),
        'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
        'SHOW_BASIS_PRICE' => (isset($arParams['DETAIL_SHOW_BASIS_PRICE']) ? $arParams['DETAIL_SHOW_BASIS_PRICE'] : 'Y'),
        'BACKGROUND_IMAGE' => (isset($arParams['DETAIL_BACKGROUND_IMAGE']) ? $arParams['DETAIL_BACKGROUND_IMAGE'] : ''),
        'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
        'SET_VIEWED_IN_COMPONENT' => (isset($arParams['DETAIL_SET_VIEWED_IN_COMPONENT']) ? $arParams['DETAIL_SET_VIEWED_IN_COMPONENT'] : ''),

        "USE_GIFTS_DETAIL" => $arParams['USE_GIFTS_DETAIL']?: 'Y',
        "USE_GIFTS_MAIN_PR_SECTION_LIST" => $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST']?: 'Y',
        "GIFTS_SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
        "GIFTS_SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
        "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
        "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
        "GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
        "GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
        "GIFTS_SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
        "GIFTS_SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
        "GIFTS_MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

        "GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
        "GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

        'OFFER_TREE_COLOR_PROPS' => $arParams['OFFER_TREE_COLOR_PROPS'],
        'OFFER_TREE_BTN_PROPS' => $arParams['OFFER_TREE_BTN_PROPS'],
        // flyaway
        'PROPS_TABS' => $arParams['PROPS_TABS'],
        'DETAIL_TABS_VIEW' => $arParams['DETAIL_TABS_VIEW'],
        'USE_BLOCK_MODS' => $arParams['USE_BLOCK_MODS'],
        "RSFLYAWAY_SHOW_DELIVERY" => $arParams["RSFLYAWAY_SHOW_DELIVERY"],
        "RSFLYAWAY_DELIVERY_MODE" => $arParams["RSFLYAWAY_DELIVERY_MODE"],


        "RSFLYAWAY_PROP_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_MORE_PHOTO"],
        "RSFLYAWAY_PROP_SKU_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_SKU_MORE_PHOTO"],
        "RSFLYAWAY_PROP_ARTICLE" => $arParams["RSFLYAWAY_PROP_ARTICLE"],
        "RSFLYAWAY_PROP_SKU_ARTICLE" => $arParams['RSFLYAWAY_PROP_SKU_ARTICLE'],
        "RSFLYAWAY_PROP_BRAND" => $arParams["RSFLYAWAY_PROP_BRAND"],
        "RSFLYAWAY_HIDE_BASKET_POPUP" => $arParams["RSFLYAWAY_HIDE_BASKET_POPUP"],
        "RSFLYAWAY_SHOW_DELIVERY_PAYMENT_INFO" => $arParams["RSFLYAWAY_SHOW_DELIVERY_PAYMENT_INFO"],
        "RSFLYAWAY_DELIVERY_LINK" => $arParams['RSFLYAWAY_DELIVERY_LINK'],
        "RSFLYAWAY_PAYMENT_LINK" => $arParams['RSFLYAWAY_PAYMENT_LINK'],
        'USE_CUSTOM_COLLECTION' => $arParams['USE_CUSTOM_COLLECTION'],
        // store
        'USE_STORE' => $arParams['USE_STORE'],
        'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
        'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
        'MAIN_TITLE' => $arParams['MAIN_TITLE'],
        'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
        "STORES_FIELDS" => $arParams['FIELDS'],
        "RSFLYAWAY_TAB_DELIVERY" => $arParams['RSFLYAWAY_TAB_DELIVERY'],

        // Comments
    	'USE_COMMENTS' => $arParams['DETAIL_USE_COMMENTS'],
    	'BLOG_URL' => (isset($arParams['DETAIL_BLOG_URL']) ? $arParams['DETAIL_BLOG_URL'] : ''),
    	'BLOG_EMAIL_NOTIFY' => (isset($arParams['DETAIL_BLOG_EMAIL_NOTIFY']) ? $arParams['DETAIL_BLOG_EMAIL_NOTIFY'] : ''),

        // Additional measure
        'RSFLYAWAY_PROP_ADDITIONAL_MEASURE' => $arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE'],
        'RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO' => $arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO']
	),
	$component
);

?><div class="row"><?

//MODS
if(isset($arParams['USE_BLOCK_MODS']) && $arParams['USE_BLOCK_MODS'] == 'Y') {
    $obCache = new CPHPCache();
    if($obCache->InitCache(36000, serialize($arFilter) ,'/iblock/catalog')) {
        $arCurIBlock = $obCache->GetVars();
    } elseif($obCache->StartDataCache()) {
        $arCurIBlock = CIBlockPriceTools::GetOffersIBlock($arParams['IBLOCK_ID']);
        if(defined('BX_COMP_MANAGED_CACHE')) {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache('/iblock/catalog');
            if($arCurIBlock) {
                $CACHE_MANAGER->RegisterTag('iblock_id_'.$arParams['IBLOCK_ID']);
            }
            $CACHE_MANAGER->EndTagCache();
        } else {
            if(!$arCurIBlock) {
                $arCurIBlock = array();
            }
        }
        $obCache->EndDataCache($arCurIBlock);
    }

    ?><div class="col col-xs-12 col-sm-12 col-md-9 col-lg-10"><?
        global $modFilter,$JSON;
            ?><div class="mods"><!-- mods --><?
                $modFilter = array('PROPERTY_'.$arCurIBlock['OFFERS_PROPERTY_ID']=>$ElementID);
                $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    'flyaway',
                    array(
                        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                        'IBLOCK_ID' => $arCurIBlock['OFFERS_IBLOCK_ID'],
                        'ELEMENT_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],//$arParams['ELEMENT_SORT_FIELD'],
                        'ELEMENT_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],//$arParams['ELEMENT_SORT_ORDER'],
                        'ELEMENT_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],//$arParams['ELEMENT_SORT_FIELD2'],
                        'ELEMENT_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],//$arParams['ELEMENT_SORT_ORDER2'],
                        'PROPERTY_CODE' => $arParams['LIST_OFFERS_PROPERTY_CODE'],
                        'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
                        'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
                        'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
                        'INCLUDE_SUBSECTIONS' => 'N',
                        'BASKET_URL' => $arParams['BASKET_URL'],
                        'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                        'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                        'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                        'FILTER_NAME' => 'modFilter',
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME'],
                        'CACHE_FILTER' => $arParams['CACHE_FILTER'],
                        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                        'SET_TITLE' => 'N',
                        'SET_STATUS_404' => 'N',
                        'DISPLAY_COMPARE' => 'Y',
                        'PAGE_ELEMENT_COUNT' => '100',
                        'LINE_ELEMENT_COUNT' => $arParams['LINE_ELEMENT_COUNT'],
                        'PRICE_CODE' => $arParams['PRICE_CODE'],
                        'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                        'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

                        'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                        'USE_PRODUCT_QUANTITY' => $arParams['~USE_PRODUCT_QUANTITY'],
                        'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['OFFERS_CART_PROPERTIES']) ? $arParams['OFFERS_CART_PROPERTIES'] : ''),
                        'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
                        'PRODUCT_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],

                        'DISPLAY_TOP_PAGER' => 'N',
                        'DISPLAY_BOTTOM_PAGER' => 'N',
                        'PAGER_TITLE' => $arParams['PAGER_TITLE'],
                        'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
                        'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
                        'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
                        'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
                        'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        'DISPLAY_COMPARE' => 'N',
                        'RSFLYAWAY_USE_FAVORITE' => 'N',
                        // flyaway
                        "RSFLYAWAY_PROP_PRICE" => $arParams["RSFLYAWAY_PROP_PRICE"],
                        "RSFLYAWAY_PROP_DISCOUNT" => $arParams["RSFLYAWAY_PROP_DISCOUNT"],
                        "RSFLYAWAY_PROP_CURRENCY" => $arParams["RSFLYAWAY_PROP_CURRENCY"],
                        "RSFLYAWAY_PROP_PRICE_DECIMALS" => $arParams["RSFLYAWAY_PROP_PRICE_DECIMALS"],
                        "RSFLYAWAY_PROP_QUANTITY" => $arParams["RSFLYAWAY_PROP_QUANTITY"],
                        "RSFLYAWAY_PROP_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_MORE_PHOTO"],
                        "RSFLYAWAY_PROP_ARTICLE" => $arParams["RSFLYAWAY_PROP_ARTICLE"],
                        "RSFLYAWAY_PROP_BRAND" => $arParams["RSFLYAWAY_PROP_BRAND"],
                        "HEAD_TYPE" => $arParams["HEAD_TYPE"],
                        // store
                        'USE_STORE' => $arParams['USE_STORE'],
                        'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
                        'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
                        'MAIN_TITLE' => $arParams['MAIN_TITLE'],
                        'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
                        "STORES_FIELDS" => $arParams['FIELDS'],
                        "RSFLYAWAY_TEMPLATE" => 'list_little',
                        "CONTENT_TITLE" => $arParams['MODS_BLOCK_NAME']
                    ),
                    $component,
                    array('HIDE_ICONS'=>'Y')
                );
            ?></div><!-- /mods --><?
    ?></div><?
}
if(empty($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] == 'Y') {
    ?><div class="col col-xs-12 "><?
		$APPLICATION->IncludeComponent(
			"bitrix:catalog.bigdata.products",
			"flyaway",
			array(
				"RCM_TYPE" => isset($arParams['BIG_DATA_RCM_TYPE']) ? $arParams['BIG_DATA_RCM_TYPE'] : 'any',
				"ID" => $ElementID,
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],

				"SHOW_FROM_SECTION" => "N",
				"SECTION_ELEMENT_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_ELEMENT_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],

				"SHOW_DISCOUNT_PERCENT" => "Y",
				"PRODUCT_SUBSCRIPTION" => "Y",
				"SHOW_NAME" => "Y",
				"SHOW_IMAGE" => "Y",
				"MESS_BTN_BUY" => $arParams["MESS_BTN_BUY"],
				"MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
				"MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
				"PAGE_ELEMENT_COUNT" => 100,
				"LINE_ELEMENT_COUNT" => 100,

				"DETAIL_URL" => "",

				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],

				"SHOW_OLD_PRICE" => "Y",
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],

				"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
				"PROPERTY_CODE_".$arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
				"ADDITIONAL_PICT_PROP_".$arParams["IBLOCK_ID"] => '', // TODO

                //flyaway
				"RSFLYAWAY_PROP_PRICE" => $arParams["RSFLYAWAY_PROP_PRICE"],
				"RSFLYAWAY_PROP_DISCOUNT" => $arParams["RSFLYAWAY_PROP_DISCOUNT"],
				"RSFLYAWAY_PROP_CURRENCY" => $arParams["RSFLYAWAY_PROP_CURRENCY"],
				"RSFLYAWAY_PROP_PRICE_DECIMALS" => $arParams["RSFLYAWAY_PROP_PRICE_DECIMALS"],
				"RSFLYAWAY_PROP_QUANTITY" => $arParams["RSFLYAWAY_PROP_QUANTITY"],
				"RSFLYAWAY_PROP_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_MORE_PHOTO"],
				"RSFLYAWAY_PROP_ARTICLE" => $arParams["RSFLYAWAY_PROP_ARTICLE"],
				"RSFLYAWAY_PROP_BRAND" => $arParams["RSFLYAWAY_PROP_BRAND"],
				"HEAD_TYPE" => $arParams["HEAD_TYPE"],
				// store
				'USE_STORE' => $arParams['USE_STORE'],
				'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
				'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
				'MAIN_TITLE' => $arParams['MAIN_TITLE'],
				'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
				"STORES_FIELDS" => $arParams['FIELDS'],
				"RSFLYAWAY_TEMPLATE" => 'list_little',
			)
		);
    ?></div><?
}
?>
<div class="col col-xs-12 col-sm-12 col-md-9 col-lg-10"><?
	$APPLICATION->IncludeComponent(
		"bitrix:catalog.viewed.products",
		"flyaway",
		array(
			'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'SHOW_FROM_SECTION' => "Y",
			'SECTION_ID' => $arResult["ROOT_SECTION_ID"],
			'SHOW_PRODUCTS_'.$arParams["IBLOCK_ID"] => "Y",
			'DEPTH' => 100,
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
			'PRICE_CODE' =>  $arParams['PRICE_CODE'],
			'CACHE_TYPE' => $arParams['CACHE_TYPE'],
			'CURRENCY_ID' => $arParams['CURRENCY_ID'],
			'RSFLYAWAY_USE_FAVORITE' => $arParams['RSFLYAWAY_USE_FAVORITE']
		)
	);?>

</div>
<?php $APPLICATION->ShowViewContent('comments'); ?>
</div>
