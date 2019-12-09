<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

?><div class="clear"></div><?
global $alfaCTemplate, $alfaCSortType, $alfaCSortToo, $alfaCOutput;
$this->SetViewTarget('sorter');
$APPLICATION->IncludeComponent(
	'redsign:catalog.sorter',
	'catalog',
	Array(
		'ALFA_ACTION_PARAM_NAME' => 'alfaction',
		'ALFA_ACTION_PARAM_VALUE' => 'alfavalue',
		'ALFA_CHOSE_TEMPLATES_SHOW' => 'N',
		'ALFA_DEFAULT_TEMPLATE' => 'catalog_blocks',
		'ALFA_SORT_BY_SHOW' => 'Y',
		'ALFA_SORT_BY_NAME' => array(0=>'sort',1=>'name',2=>'PROPERTY_PROD_PRICE_FALSE',),
		'ALFA_SORT_BY_DEFAULT' => 'sort_asc',
		'ALFA_OUTPUT_OF_SHOW' => 'Y',
		'ALFA_OUTPUT_OF' => array(0=>'16',1=>'20',2=>'40',3=>'80',4=>'',),
		'ALFA_OUTPUT_OF_DEFAULT' => '16',
		'ALFA_OUTPUT_OF_SHOW_ALL' => 'N',
	),
	$component
);
$this->EndViewTarget();
$arElements = $APPLICATION->IncludeComponent(
	'bitrix:search.page',
	'catalog',
	Array(
		'RESTART' => 'N',
		'NO_WORD_LOGIC' => 'N',
		'CHECK_DATES' => 'Y',
		'USE_TITLE_RANK' => 'N',
		'DEFAULT_SORT' => 'rank',
		'FILTER_NAME' => '',
		'arrFILTER' => array(),
		'SHOW_WHERE' => 'N',
		'SHOW_WHEN' => 'N',
		'PAGE_RESULT_COUNT' => '500',
		'AJAX_MODE' => 'N',
		'AJAX_OPTION_JUMP' => 'N',
		'AJAX_OPTION_STYLE' => 'Y',
		'AJAX_OPTION_HISTORY' => 'N',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => '3600',
		'DISPLAY_TOP_PAGER' => 'Y',
		'DISPLAY_BOTTOM_PAGER' => 'Y',
		'PAGER_TITLE' => 'Результаты поиска',
		'PAGER_SHOW_ALWAYS' => 'N',
		'PAGER_TEMPLATE' => 'al',
		'USE_LANGUAGE_GUESS' => 'N',
		'USE_SUGGEST' => 'N',
		'AJAX_OPTION_ADDITIONAL' => '',
		'CATALOG_IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'CATALOG_IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'COUNT_RESULT_NOT_CATALOG' => $arParams['COUNT_RESULT_NOT_CATALOG'],
	),
	$component
);
global $arrFilter;
$arrFilter = array(
	'=ID' => $arElements,
);
?><!-- around_catalog --><?
?><div class="around_catalog"><?
	?><div class="catalog_sidebar"><?
		?><div class="catalog_sidebar_inner"><?
$APPLICATION->IncludeComponent(
	'bitrix:catalog.section.list',
	'catalog',
	Array(
		'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
		'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		'COUNT_ELEMENTS' => $arParams['SECTION_COUNT_ELEMENTS'],
		'TOP_DEPTH' => $arParams["SECTION_TOP_DEPTH"],
		'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
	),
	$component
);


if ($arParams['USE_FILTER'] == 'Y')
{
	$APPLICATION->IncludeComponent(
		"bitrix:catalog.smart.filter",
		"al",
		Array(
            "COMPONENT_TEMPLATE" => "al",
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SECTION_ID" => false,
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SAVE_IN_SESSION" => "Y",
			"FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
			"XML_EXPORT" => "Y",
			"SECTION_TITLE" => "NAME",
			"SECTION_DESCRIPTION" => "DESCRIPTION",
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"SEF_MODE" => $arParams["SEF_MODE"],
			"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
			"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
			"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
            "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],

            "PRICES_GROUPED" => $arParams["FILTER_PRICES_GROUPED"],
            "PRICES_GROUPED_FOR" => $arParams["FILTER_PRICES_GROUPED_FOR"],
            "SCROLL_PROPS" => $arParams["FILTER_SCROLL_PROPS"],
            "OFFER_SCROLL_PROPS" => $arParams["OFFER_FILTER_SCROLL_PROPS"],
            "SEARCH_PROPS" => $arParams["FILTER_SEARCH_PROPS"],
            "OFFER_SEARCH_PROPS" => $arParams["OFFER_FILTER_SEARCH_PROPS"],
            "OFFER_TREE_COLOR_PROPS" => $arParams["OFFER_TREE_COLOR_PROPS"],
            "OFFER_TREE_BTN_PROPS" => $arParams["OFFER_TREE_BTN_PROPS"],
            "FILTER_FIXED" => $arParams["FILTER_FIXED"],
            "TEMPLATE_AJAXID" => $arParams["TEMPLATE_AJAXID"],
			"MODEF_SHOW" => "N",
		),
		$component
	);
}

		?></div><?
	?></div><?
	?><!-- catalog --><?
	?><div class="catalog context-wrap"><?
		$APPLICATION->ShowViewContent('catalog_section_description');
		$APPLICATION->ShowViewContent('catalog_filterin');
if (is_array($arElements) && count($arElements)>0):
?><!-- sorted and navigation --><?
?><div class="around_sorter_and_navigation"><?
$APPLICATION->ShowViewContent('paginator');
$APPLICATION->ShowViewContent('sorter');
?><div class="clear"></div><?
?></div><?
?><!-- /sorted and navigation --><?
		?><div class="catalog_inner"><?
?><div id="ajaxpages_catalog_identifier_search"><?
$IS_AJAXPAGES = 'N';
if ($_REQUEST['ajaxpages']=='Y' && $_REQUEST['ajaxpagesid']=='ajaxpages_catalog_identifier_search')
{
	$APPLICATION->RestartBuffer();
	$IS_AJAXPAGES = 'Y';
}









            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "catalog",
                Array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "ELEMENT_SORT_FIELD" => $alfaCSortType,//$arParams["ELEMENT_SORT_FIELD"],
                    "ELEMENT_SORT_ORDER" => $alfaCSortToo,//$arParams["ELEMENT_SORT_ORDER"],
                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD"],
                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER"],
                    "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                    "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                    "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                    "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                    "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                    "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                    "BASKET_URL" => $arParams["BASKET_URL"],
                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "SET_TITLE" => "N",
                    "MESSAGE_404" => $arParams["MESSAGE_404"],
                    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                    "SHOW_404" => $arParams["SHOW_404"],
                    "FILE_404" => $arParams["FILE_404"],
                    "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                    "PAGE_ELEMENT_COUNT" => $alfaCOutput,//$arParams["PAGE_ELEMENT_COUNT"],
                    "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                    "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                    "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                    "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                    "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

                    "DISPLAY_TOP_PAGER" => "Y",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                    "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                    "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                    "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                    "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                    "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
                    "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],

                    "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                    "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

                    "SECTION_ID" => "",
                    "SECTION_CODE" => "",
                    "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                    "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                    "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                    "ADDITIONAL_PICT_PROP" => $arParams["ADDITIONAL_PICT_PROP"],
                    "OFFER_ADDITIONAL_PICT_PROP" => $arParams["OFFER_ADDITIONAL_PICT_PROP"],
                    'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                    'OFFER_TREE_COLOR_PROPS' => $arParams['OFFER_TREE_COLOR_PROPS'],
                    'OFFER_TREE_BTN_PROPS' => $arParams['OFFER_TREE_BTN_PROPS'],
                    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                    'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                    'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                    'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                    'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
                    'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
                    "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
                    "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),
                    'ADD_TO_BASKET_ACTION' => $basketAction,

                    "TEMPLATE_AJAXID" => $arParams["TEMPLATE_AJAXID"],
                    "USE_AJAXPAGES" => $arParams["USE_AJAXPAGES"],
                    "ICON_MEN_PROP" => $arParams["ICON_MEN_PROP"],
                    "ICON_WOMEN_PROP" => $arParams["ICON_WOMEN_PROP"],
                    "ICON_NOVELTY_PROP" => $arParams["ICON_NOVELTY_PROP"],
                    "NOVELTY_TIME" => $arParams["NOVELTY_TIME"],
                    "ICON_DISCOUNT_PROP" => $arParams["ICON_DISCOUNT_PROP"],
                    "ICON_DEALS_PROP" => $arParams["ICON_DEALS_PROP"],
                    "USE_LIKES" => $arParams["USE_LIKES"],
                    'USE_SHARE' => $arParams['USE_SHARE'],
                    'SOCIAL_SERVICES' => $arParams['LIST_SOCIAL_SERVICES'],
                    'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
                    'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
                    'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
                    'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],
                    "BRAND_LOGO_PROP" => $arParams["BRAND_LOGO_PROP"],
                    "BRAND_PROP" => $arParams["BRAND_PROP"],
                    "ACCESSORIES_PROP" => $arParams["ACCESSORIES_PROP"],
                    "POPUP_DETAIL_VARIABLE" => $arParams["POPUP_DETAIL_VARIABLE"],
                    "ERROR_EMPTY_ITEMS" => "N",
                    "PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
                    'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
                    'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                    'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
                    'COMPOSITE_FRAME' => 'Y',
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "ADD_SECTIONS_CHAIN" => "N",
                ),
                $component
            );
if ($IS_AJAXPAGES == 'Y')
{
	die();
}
			?></div><?
		?></div><?
	?></div><!-- /catalog --><?
	$APPLICATION->ShowViewContent('catalog_search_other');
else:
	ShowError(getMessage('CATALOG_SEARCH_NO_RESULT'));
endif;
?></div><!-- /around_catalog --><?
$APPLICATION->AddChainItem(getMessage('SEARCH_PAGE_TITLE') , $APPLICATION->GetCurPage());