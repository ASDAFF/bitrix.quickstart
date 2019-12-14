<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$basketAction = (isset($arParams['DETAIL_ADD_TO_BASKET_ACTION']) ? $arParams['DETAIL_ADD_TO_BASKET_ACTION'] : array());

$request = Application::getInstance()->getContext()->getRequest();

$sOfferCode = $request->get('offer');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $request->get('AJAX_CALL') == 'Y') {

	if ($request->get('action') == 'get_element_json') {

		$APPLICATION->IncludeComponent(
			'bitrix:catalog.element',
			'json',
			array (
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
                "OFFERS_FIELD_CODE" => array_merge(
                    $arParams["DETAIL_OFFERS_FIELD_CODE"],
                    array(
                        'NAME',
                        'DETAIL_PAGE_URL',
                    )
                ),
                "OFFERS_PROPERTY_CODE" => array_merge(
                    $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
                    array()
                ),
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
                'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                "OFFER_TREE_COLOR_PROPS" => $arParams["OFFER_TREE_COLOR_PROPS"],
                "OFFER_TREE_BTN_PROPS" => $arParams["OFFER_TREE_BTN_PROPS"],
                'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                'SHOW_MAX_QUANTITY' => $arParams['DETAIL_SHOW_MAX_QUANTITY'],
                'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
                'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

                "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
                "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
                'ADD_TO_BASKET_ACTION' => $basketAction,
                'DISPLAY_COMPARE' => (isset($arParams['USE_COMPARE']) ? $arParams['USE_COMPARE'] : ''),
                'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
                'SHOW_BASIS_PRICE' => (isset($arParams['DETAIL_SHOW_BASIS_PRICE']) ? $arParams['DETAIL_SHOW_BASIS_PRICE'] : 'Y'),

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

                "ICON_MEN_PROP" => $arParams["ICON_MEN_PROP"],
                "ICON_WOMEN_PROP" => $arParams["ICON_WOMEN_PROP"],
                "ICON_NOVELTY_PROP" => $arParams["ICON_NOVELTY_PROP"],
                "ICON_DISCOUNT_PROP" => $arParams["ICON_DISCOUNT_PROP"],
                "ICON_DEALS_PROP" => $arParams["ICON_DEALS_PROP"],
                'OFFERS_SELECTED' => 0 < strlen($sOfferCode) ? $sOfferCode : false,

                "ADDITIONAL_PICT_PROP" => $arParams["ADDITIONAL_PICT_PROP"],
                "OFFER_ADDITIONAL_PICT_PROP" => $arParams["OFFER_ADDITIONAL_PICT_PROP"],
                "ARTICLE_PROP" => $arParams["ARTICLE_PROP"],
                "OFFER_ARTICLE_PROP" => $arParams["OFFER_ARTICLE_PROP"],
                "DELIVERY_PROP" => $arParams["DELIVERY_PROP"],
                "BRAND_PROP" => $arParams["BRAND_PROP"],
                "BRAND_IBLOCK_ID" => $arParams["BRAND_IBLOCK_ID"],
                "BRAND_IBLOCK_BRAND_PROP" => $arParams["BRAND_IBLOCK_BRAND_PROP"],
                "BRAND_LOGO_PROP" => $arParams["BRAND_LOGO_PROP"],
                "ACCESSORIES_PROP" => $arParams["ACCESSORIES_PROP"],

                "USE_QUANTITY_AND_STORES" => $arParams["USE_QUANTITY_AND_STORES"],

                // catalog.store.amount
                "USE_STORE" => $arParams["USE_STORE"],
                "STORE_PATH" => $arParams["STORE_PATH"],
                "MAIN_TITLE" => $arParams["MAIN_TITLE"],
                "USE_MIN_AMOUNT" =>  $arParams["USE_MIN_AMOUNT"],
                "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
                "STORES" => $arParams["STORES"],
                "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
                "SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
                "USER_FIELDS" => $arParams["USER_FIELDS"],
                "FIELDS" => $arParams["FIELDS"],
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);

	} else if ($request->get('POPUP_GALLERY') == 'Y') {

        $APPLICATION->RestartBuffer();
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.element",
            'popupgallery',
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
                'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                "OFFER_TREE_COLOR_PROPS" => $arParams["OFFER_TREE_COLOR_PROPS"],
                "OFFER_TREE_BTN_PROPS" => $arParams["OFFER_TREE_BTN_PROPS"],
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
                "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
                "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
                'ADD_TO_BASKET_ACTION' => $basketAction,
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

                "ICON_MEN_PROP" => $arParams["ICON_MEN_PROP"],
                "ICON_WOMEN_PROP" => $arParams["ICON_WOMEN_PROP"],
                "ICON_NOVELTY_PROP" => $arParams["ICON_NOVELTY_PROP"],
                "ICON_DISCOUNT_PROP" => $arParams["ICON_DISCOUNT_PROP"],
                "ICON_DEALS_PROP" => $arParams["ICON_DEALS_PROP"],
                'USE_LIKES' => $arParams['USE_LIKES'],
                'USE_FAVORITE' => $arParams['USE_FAVORITE'],
                'USE_BUY1CLICK' => $arParams['USE_BUY1CLICK'],
                'USE_SHARE' => $arParams['USE_SHARE'],
                'SOCIAL_SERVICES' => $arParams['DETAIL_SOCIAL_SERVICES'],
                'LIST_SOCIAL_SERVICES' => $arParams['LIST_SOCIAL_SERVICES'],
                'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
                'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
                'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
                'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],


                "OFFER_ID" => intval($_REQUEST['offer_id']),
                "ADDITIONAL_PICT_PROP" => $arParams["ADDITIONAL_PICT_PROP"],
                "OFFER_ADDITIONAL_PICT_PROP" => $arParams["OFFER_ADDITIONAL_PICT_PROP"],
                "ARTICLE_PROP" => $arParams["ARTICLE_PROP"],
                "OFFER_ARTICLE_PROP" => $arParams["OFFER_ARTICLE_PROP"],

                "BRAND_PROP" => $arParams["BRAND_PROP"],
                "BRAND_IBLOCK_ID" => $arParams["BRAND_IBLOCK_ID"],
                "BRAND_IBLOCK_BRAND_PROP" => $arParams["BRAND_IBLOCK_BRAND_PROP"],
                "BRAND_LOGO_PROP" => $arParams["BRAND_LOGO_PROP"],
            ),
            $component,
            array('HIDE_ICONS' => 'Y')
        );
        die();

    }
}

$this->setFrameMode(true);

$ElementID = $APPLICATION->IncludeComponent(
	'bitrix:catalog.element',
	'catalog',
	Array(
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
        'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
        "OFFER_TREE_COLOR_PROPS" => $arParams["OFFER_TREE_COLOR_PROPS"],
        "OFFER_TREE_BTN_PROPS" => $arParams["OFFER_TREE_BTN_PROPS"],
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
        "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
        "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
        'ADD_TO_BASKET_ACTION' => $basketAction,
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

        "ICON_MEN_PROP" => $arParams["ICON_MEN_PROP"],
        "ICON_WOMEN_PROP" => $arParams["ICON_WOMEN_PROP"],
        "ICON_NOVELTY_PROP" => $arParams["ICON_NOVELTY_PROP"],
        "ICON_DISCOUNT_PROP" => $arParams["ICON_DISCOUNT_PROP"],
        "ICON_DEALS_PROP" => $arParams["ICON_DEALS_PROP"],
        'USE_LIKES' => $arParams['USE_LIKES'],
        'USE_FAVORITE' => $arParams['USE_FAVORITE'],
        'USE_BUY1CLICK' => $arParams['USE_BUY1CLICK'],
        'USE_SHARE' => $arParams['USE_SHARE'],
        'SOCIAL_SERVICES' => $arParams['DETAIL_SOCIAL_SERVICES'],
        'LIST_SOCIAL_SERVICES' => $arParams['LIST_SOCIAL_SERVICES'],
        'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
        'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
        'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
        'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],
        'OFFERS_SELECTED' => 0 < strlen($sOfferCode) ? $sOfferCode : false,

        "ADDITIONAL_PICT_PROP" => $arParams["ADDITIONAL_PICT_PROP"],
        "OFFER_ADDITIONAL_PICT_PROP" => $arParams["OFFER_ADDITIONAL_PICT_PROP"],
        "ARTICLE_PROP" => $arParams["ARTICLE_PROP"],
        "OFFER_ARTICLE_PROP" => $arParams["OFFER_ARTICLE_PROP"],
        "DELIVERY_PROP" => $arParams["DELIVERY_PROP"],
        "BRAND_PROP" => $arParams["BRAND_PROP"],
        "BRAND_IBLOCK_ID" => $arParams["BRAND_IBLOCK_ID"],
        "BRAND_IBLOCK_BRAND_PROP" => $arParams["BRAND_IBLOCK_BRAND_PROP"],
        "BRAND_LOGO_PROP" => $arParams["BRAND_LOGO_PROP"],
        "ACCESSORIES_PROP" => $arParams["ACCESSORIES_PROP"],
        "POPUP_DETAIL_VARIABLE" => $arParams["POPUP_DETAIL_VARIABLE"],
        "USE_KREDIT" => $arParams["USE_KREDIT"],
        "KREDIT_URL" => $arParams["KREDIT_URL"],
        "USE_PICTURE_ZOOM" => $arParams["USE_PICTURE_ZOOM"],
        "USE_PICTURE_GALLERY" => $arParams["USE_PICTURE_GALLERY"],
        "FILTER_NAME" => $arParams["FILTER_NAME"],
        "USE_QUANTITY_AND_STORES" => $arParams["USE_QUANTITY_AND_STORES"],

        "SIZE_TABLE_USER_FIELD_CODE" => $arParams["SIZE_TABLE_USER_FIELD_CODE"],
        "TAB_IBLOCK_PROPS" => $arParams["TAB_IBLOCK_PROPS"],
        "LINKED_ITEMS_PROPS" => $arParams["LINKED_ITEMS_PROPS"],

        // forum.topic.review
        "USE_REVIEW" => $arParams["USE_REVIEW"],
        "REVIEWS_URL_TEMPLATES_DETAIL" => $arParams["POST_FIRST_MESSAGE"]==="Y"? $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"] :"",
        "SHOW_LINK_TO_FORUM" => $arParams["SHOW_LINK_TO_FORUM"],
        "REVIEW_AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
        "FORUM_ID" => $arParams["FORUM_ID"],
        "URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],
        "MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
        "PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
        "USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],

        // catalog.section
        "ALSO_BUY_ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
        "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
        "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
        "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
        "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
        "LIST_OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
        "LIST_OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
        "LIST_OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
        "LIST_PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],

        // catalog.store.amount
        "USE_STORE" => $arParams["USE_STORE"],
        "STORE_PATH" => $arParams["STORE_PATH"],
        "MAIN_TITLE" => $arParams["MAIN_TITLE"],
        "USE_MIN_AMOUNT" =>  $arParams["USE_MIN_AMOUNT"],
        "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
        "STORES" => $arParams["STORES"],
        "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
        "SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
        "USER_FIELDS" => $arParams["USER_FIELDS"],
        "FIELDS" => $arParams["FIELDS"],
	),
	$component
);?>


<?php
$APPLICATION->ShowViewContent('rs_detail-linked_items');

$arRecomData = array();
$recomCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
$obCache = new CPHPCache();
if ($obCache->InitCache(36000, serialize($recomCacheID), "/catalog/recommended"))
{
    $arRecomData = $obCache->GetVars();
}
elseif ($obCache->StartDataCache())
{
    if (Loader::includeModule("catalog"))
    {
        $arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
        $arRecomData['OFFER_IBLOCK_ID'] = (!empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);
        $arRecomData['IBLOCK_LINK'] = '';
        $arRecomData['ALL_LINK'] = '';
        $rsProps = CIBlockProperty::GetList(
            array('SORT' => 'ASC', 'ID' => 'ASC'),
            array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'PROPERTY_TYPE' => 'E', 'ACTIVE' => 'Y')
        );
        $found = false;
        while ($arProp = $rsProps->Fetch())
        {
            if ($found)
            {
                break;
            }
            if ($arProp['CODE'] == '')
            {
                $arProp['CODE'] = $arProp['ID'];
            }
            $arProp['LINK_IBLOCK_ID'] = intval($arProp['LINK_IBLOCK_ID']);
            if ($arProp['LINK_IBLOCK_ID'] != 0 && $arProp['LINK_IBLOCK_ID'] != $arParams['IBLOCK_ID'])
            {
                continue;
            }
            if ($arProp['LINK_IBLOCK_ID'] > 0)
            {
                if ($arRecomData['IBLOCK_LINK'] == '')
                {
                    $arRecomData['IBLOCK_LINK'] = $arProp['CODE'];
                    $found = true;
                }
            }
            else
            {
                if ($arRecomData['ALL_LINK'] == '')
                {
                    $arRecomData['ALL_LINK'] = $arProp['CODE'];
                }
            }
        }
        if ($found)
        {
            if(defined("BX_COMP_MANAGED_CACHE"))
            {
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache("/catalog/recommended");
                $CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
                $CACHE_MANAGER->EndTagCache();
            }
        }
    }
    $obCache->EndDataCache($arRecomData);
}
?>

<?php if (!empty($arRecomData)): ?>
	<?php if (ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N')): ?>
		<?$APPLICATION->IncludeComponent(
            "bitrix:catalog.bigdata.products",
            "al",
            array(
                "LINE_ELEMENT_COUNT" => 5,
                "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                "BASKET_URL" => $arParams["BASKET_URL"],
                "ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action")."_cbdp",
                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                "SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
                "SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                "PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                "SHOW_NAME" => "Y",
                "SHOW_IMAGE" => "Y",
                "MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
                "MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
                "MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
                "MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
                "PAGE_ELEMENT_COUNT" => 5,
                "SHOW_FROM_SECTION" => "N",
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "DEPTH" => "2",
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                "SECTION_ELEMENT_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                "SECTION_ELEMENT_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                "ID" => $ElementID,
                //"LABEL_PROP_".$arParams['IBLOCK_ID'] => "-",
                "BRAND_PROP_".$arParams['IBLOCK_ID'] => $arParams['BRAND_PROP'],
                "ICON_NOVELTY_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_NOVELTY_PROP'],
                "ICON_DEALS_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DEALS_PROP'],
                "ICON_DISCOUNT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DISCOUNT_PROP'],
                "ICON_MEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_MEN_PROP'],
                "ICON_WOMEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_WOMEN_PROP'],
                "PROPERTY_CODE_".$arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
                "PROPERTY_CODE_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                "CART_PROPERTIES_".$arParams["IBLOCK_ID"] => $arParams["PRODUCT_PROPERTIES"],
                "CART_PROPERTIES_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFERS_CART_PROPERTIES"],
                "ADDITIONAL_PICT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP'],
                "ADDITIONAL_PICT_PROP_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADDITIONAL_PICT_PROP'],
                "OFFER_TREE_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
                "OFFER_TREE_COLOR_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_TREE_COLOR_PROPS'],
                "OFFER_TREE_BTN_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_TREE_BTN_PROPS'],
                "RCM_TYPE" => (isset($arParams['BIG_DATA_RCM_TYPE']) ? $arParams['BIG_DATA_RCM_TYPE'] : ''),

                'USE_DELETE' => $arParams['USE_DELETE'],
                'USE_LIKES' => $arParams['USE_LIKES'],
                'USE_SHARE' => $arParams['USE_SHARE'],
                'SOCIAL_SERVICES' => $arParams['LIST_SOCIAL_SERVICES'],
                'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
                'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
                'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
                'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],
                'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
                'COMPOSITE_MODE_REQUEST' => 'N',
                "USE_PRICE_COUNT" => $arParams['USE_PRICE_COUNT'],
                "SECTION_TITLE" => getMessage('RS_SLINE.BC_AL.BIGDATA_TITLE'),
			),
			$component
		);?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($arParams["USE_ALSO_BUY"] == "Y" && ModuleManager::isModuleInstalled("sale") && !empty($arRecomData)): ?>
	<?$APPLICATION->IncludeComponent(
        "bitrix:sale.recommended.products",
        "al",
        array(
            "ID" => $ElementID,

            "MIN_BUYES" => $arParams["ALSO_BUY_MIN_BUYES"],
            "ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
            "LINE_ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
            "DETAIL_URL" => $arParams["DETAIL_URL"],
            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action")."_srp",
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "PAGE_ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "PRICE_CODE" => $arParams["PRICE_CODE"],
            "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
            "SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
            "PROPERTY_CODE_".$arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE'],
            "CART_PROPERTIES_".$arParams['IBLOCK_ID'] => $arParams['PRODUCT_PROPERTIES'],
            "BRAND_PROP_".$arParams['IBLOCK_ID'] => $arParams['BRAND_PROP'],
            "ICON_NOVELTY_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_NOVELTY_PROP'],
            "ICON_DEALS_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DEALS_PROP'],
            "ICON_DISCOUNT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DISCOUNT_PROP'],
            "ICON_MEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_MEN_PROP'],
            "ICON_WOMEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_WOMEN_PROP'],
            "OFFER_TREE_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
            "OFFER_TREE_COLOR_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_TREE_COLOR_PROPS'],
            "OFFER_TREE_BTN_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_TREE_BTN_PROPS'],
            "ADDITIONAL_PICT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP'],
            "ADDITIONAL_PICT_PROP_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADDITIONAL_PICT_PROP'],

            'USE_DELETE' => $arParams['USE_DELETE'],
            'USE_LIKES' => $arParams['USE_LIKES'],
            'USE_SHARE' => $arParams['USE_SHARE'],
            'SOCIAL_SERVICES' => $arParams['LIST_SOCIAL_SERVICES'],
            'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
            'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
            'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
            'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],
            'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
            'COMPOSITE_MODE_REQUEST' => 'N',
            "USE_PRICE_COUNT" => $arParams['USE_PRICE_COUNT'],
		),
		$component
	);?>
<?php endif; ?>

<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.viewed.products",
	"al",
	array(
		"DETAIL_URL" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
		"BASKET_URL" => $arParams['BASKET_URL'],
		"ACTION_VARIABLE" => (!empty($arParams['ACTION_VARIABLE']) ? $arParams['ACTION_VARIABLE'] : "action")."_cbdp",
		"PRODUCT_ID_VARIABLE" => $arParams['PRODUCT_ID_VARIABLE'],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams['PRODUCT_QUANTITY_VARIABLE'],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
		"PRODUCT_PROPS_VARIABLE" => $arParams['PRODUCT_PROPS_VARIABLE'],
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
		"SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
		"SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
		"PRICE_CODE" => $arParams['PRICE_CODE'],
		"SHOW_PRICE_COUNT" => $arParams['SHOW_PRICE_COUNT'],
		"PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
		"PRICE_VAT_INCLUDE" => $arParams['PRICE_VAT_INCLUDE'],
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
		"SHOW_NAME" => "Y",
		"SHOW_IMAGE" => "Y",
		"MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
		"MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
		"MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
		"MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
		"PAGE_ELEMENT_COUNT" => 5,
		"SHOW_FROM_SECTION" => "N",
		"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"DEPTH" => "2",
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
		"SHOW_PRODUCTS_".$arParams['IBLOCK_ID'] => "Y",
		"ADDITIONAL_PICT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP'],
		//"LABEL_PROP_".$arParams['IBLOCK_ID'] => "-",
		"BRAND_PROP_".$arParams['IBLOCK_ID'] => $arParams['BRAND_PROP'],
		"ICON_NOVELTY_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_NOVELTY_PROP'],
		"ICON_DEALS_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DEALS_PROP'],
		"ICON_DISCOUNT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DISCOUNT_PROP'],
		"ICON_MEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_MEN_PROP'],
		"ICON_WOMEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_WOMEN_PROP'],

		"HIDE_NOT_AVAILABLE" => $arParams['HIDE_NOT_AVAILABLE'],
		"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
		"CURRENCY_ID" => $arParams['CURRENCY_ID'],
		"SECTION_ID" => $arResult['VARIABLES']['SECTION_ID'],
		"SECTION_CODE" => $arResult['VARIABLES']['SECTION_CODE'],
		"SECTION_ELEMENT_ID" => $arResult['VARIABLES']['SECTION_ID'],
		"SECTION_ELEMENT_CODE" => $arResult['VARIABLES']['SECTION_CODE'],
		"ID" => $ElementID,
		"PROPERTY_CODE_".$arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE'],
		"CART_PROPERTIES_".$arParams['IBLOCK_ID'] => $arParams['PRODUCT_PROPERTIES'],
        "OFFER_TREE_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_TREE_PROPS'],
		"OFFER_TREE_COLOR_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_TREE_COLOR_PROPS'],
		"ADDITIONAL_PICT_PROP_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADDITIONAL_PICT_PROP'],
		"DEPTH" => "2",
		'USE_DELETE' => $arParams['USE_DELETE'],
		'USE_LIKES' => $arParams['USE_LIKES'],
		'USE_SHARE' => $arParams['USE_SHARE'],
        'SOCIAL_SERVICES' => $arParams['LIST_SOCIAL_SERVICES'],
        'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
        'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
        'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
        'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],
		'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
		'COMPOSITE_MODE_REQUEST' => 'N',
        "LINE_ELEMENT_COUNT" => '5',
        "USE_PRICE_COUNT" => $arParams['USE_PRICE_COUNT'],
	),
	$component
);?>
