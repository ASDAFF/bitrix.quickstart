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
$this->setFrameMode(true);

use \Bitrix\Main\Application,
    \Bitrix\Main\Localization\Loc;


$request = Application::getInstance()->getContext()->getRequest();
?>

<?$ElementID = $APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"",
	Array(
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"META_KEYWORDS" => $arParams["META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
		"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
		"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"USE_SHARE" => $arParams["USE_SHARE"],
		"SHARE_HIDE" => $arParams["SHARE_HIDE"],
		"SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
		"SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
		"SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
		"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),

		"CATALOG_IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
		"CATALOG_IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
		"CATALOG_BRAND_PROP" => $arParams["CATALOG_BRAND_PROP"],
        "CATALOG_FILTER_NAME" => $arParams["CATALOG_FILTER_NAME"],

		"BRAND_PROP" => $arParams["BRAND_PROP"],
	),
	$component
);?>

<?php if ($ElementID > 0): ?>
<div class="catalog">

    <?php if ($useSorter) \Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID('catalog'); ?>

    <?php //$this->SetViewTarget('catalog_sidebar'); ?>
    
    <?php ob_start(); ?>
    <div class="fixsidebar">
    <?php
    $sHtmlContent = ob_get_clean();
    $APPLICATION->AddViewContent('catalog_sidebar', $sHtmlContent, 100);
    ?>
    
        <?php // $APPLICATION->ShowViewContent('brands-catalog.sections.list'); ?>
        
    <?php ob_start(); ?>
    
        <?php if ($arParams['USE_FILTER'] == 'Y'): ?>
            <?$APPLICATION->IncludeComponent(
                    'bitrix:catalog.smart.filter',
                    'flyaway',
                    array(
                        "IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
                        "SECTION_ID" => $request->get('section'),
                        "FILTER_NAME" => $arParams["CATALOG_FILTER_NAME"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SAVE_IN_SESSION" => "N",
                        "XML_EXPORT" => "Y",
                        "SECTION_TITLE" => "NAME",
                        "SECTION_DESCRIPTION" => "DESCRIPTION",
                        "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                        "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                        "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                        "SEF_MODE" => 'N', //$arParams["SEF_MODE"],
                        //"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
                        //"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                        "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],

                        'FILTER_PROP_SEARCH' => $arParams['FILTER_PROP_SEARCH'],
                        'PAGER_PARAMS_NAME' => $arParams["PAGER_PARAMS_NAME"],
                        'USE_AJAX' => $arParams['FILTER_USE_AJAX'],
                        'TEMPLATE_AJAX_ID' => "js-ajax-section",
                        "BRAND_PROP" => $arParams["CATALOG_BRAND_PROP"],
                    ),
                    $component
            );?>
        <?php endif; ?>
    </div>
    
    <?php
    $sHtmlContent = ob_get_clean();
    $APPLICATION->AddViewContent('catalog_sidebar', $sHtmlContent, 400);
    ?>

    <?php // $this->EndViewTarget(); ?>


    <div class="catalog-content">
        <div class="row">
            <?/*
            <div class="col col-md-12 hidden-xs">
                <?$APPLICATION->ShowViewContent('catalog_section_descr');?>
            </div>
            */?>
            <?php if($useSorter): ?>
                <div class="col col-md-12">
                    <?/*$APPLICATION->IncludeComponent(
                        "bitrix:catalog.compare.list",
                        "flyaway",
                        array(
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "NAME" => $arParams["COMPARE_NAME"],
                            "COMPONENT_TEMPLATE" => "flyaway",
                            "AJAX_MODE" => "N",
                            "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                            "COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
                            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"]
                        ),
                        $component,
                        array('HIDE_ICONS'=>'Y')
                    );*/?>

                    <?php
                    global $alfaCTemplate, $alfaCSortType, $alfaCSortToo, $alfaCOutput;
                    $APPLICATION->IncludeComponent(
                        "redsign:catalog.sorter",
                        "flyaway",
                        array(
                            "COMPONENT_TEMPLATE" => "flyaway",
                            "ALFA_ACTION_PARAM_NAME" => "alfaction",
                            "ALFA_ACTION_PARAM_VALUE" => "alfavalue",
                            "ALFA_CHOSE_TEMPLATES_SHOW" => $arParams['RSFLYAWAY_SORTER_SHOW_TEMPLATE'],
                            "ALFA_SORT_BY_SHOW" => $arParams['RSFLYAWAY_SORTER_SHOW_SORTING'],
                            "ALFA_SHORT_SORTER" => "N",
                            "ALFA_OUTPUT_OF_SHOW" => $arParams['RSFLYAWAY_SORTER_SHOW_PAGE_COUNT'],
                            "ALFA_CNT_TEMPLATES" => "4",
                            "ALFA_DEFAULT_TEMPLATE" => $arParams['RSFLYAWAY_SORTER_TEMPLATE_DEFAULT'],
                            "ALFA_SORT_BY_NAME" => array("PROPERTY_PRICE_FALSE", "name", "sort"),
                            "ALFA_SORT_BY_DEFAULT" => "PROPERTY_PRICE_asc",
                            "ALFA_OUTPUT_OF" => array("5", "10", "15", "20", "25"),
                            "ALFA_OUTPUT_OF_DEFAULT" => "15",
                            "ALFA_OUTPUT_OF_SHOW_ALL" => "N",
                            "ALFA_CNT_TEMPLATES_0" => "",
                            "ALFA_CNT_TEMPLATES_NAME_0" => "list_little",
                            "ALFA_CNT_TEMPLATES_1" => "",
                            "ALFA_CNT_TEMPLATES_NAME_1" => "list",
                            "ALFA_CNT_TEMPLATES_2" => "",
                            "ALFA_CNT_TEMPLATES_NAME_2" => "showcase",
                            "ALFA_CNT_TEMPLATES_3" => "",
                            "ALFA_CNT_TEMPLATES_NAME_3" => "showcase_mob",
                            "USE_FILTER" => $arParams['USE_FILTER'],
                            "USE_AJAX" => $arParams['SORTER_USE_AJAX'],
                            "TEMPLATE_AJAX_ID" => "js-ajax-section",
                        ),
                        $component,
                        array('HIDE_ICONS'=>'Y')
                    );
                    ?>
                </div>
            <?php endif; ?>

            <div class="col col-md-12">
                <?php
                $viewMobileVer = "N";
                if ($alfaCTemplate == "showcase_mob") {
                    $viewMobileVer = "Y";
                }

                $isAjax = ($_REQUEST['isAjax'] == 'Y' ? 'Y' : 'N');
                $onlyElements = (($isAjax && $_REQUEST['action'] == 'updateElements') ? 'Y' : 'N');

                $intSectionID = 0;
                ?>

                <?$intSectionID = $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "flyaway",
                    array(
                        "IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
                        "ELEMENT_SORT_FIELD" => ( $useSorter ? $alfaCSortType : $arParams["ELEMENT_SORT_FIELD"] ),
                        "ELEMENT_SORT_ORDER" => ( $useSorter ? $alfaCSortToo : $arParams["ELEMENT_SORT_ORDER"] ),
                        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                        "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                        "PROPERTY_CODE" => $arParams["CATALOG_PROPERTY_CODE"],
                        //"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                        //"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                        //"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                        //"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                        "INCLUDE_SUBSECTIONS" => 'Y', // $arParams["INCLUDE_SUBSECTIONS"],
                        'SHOW_ALL_WO_SECTION' => 'Y',
                        "BASKET_URL" => $arParams["BASKET_URL"],
                        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                        "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                        "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                        "FILTER_NAME" => $arParams["CATALOG_FILTER_NAME"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SET_TITLE" => 'N', //$arParams["SET_TITLE"],
                        //"MESSAGE_404" => $arParams["MESSAGE_404"],
                        //"SET_STATUS_404" => $arParams["SET_STATUS_404"],
                        //"SHOW_404" => $arParams["SHOW_404"],
                        //"FILE_404" => $arParams["FILE_404"],
                        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                        "PAGE_ELEMENT_COUNT" => ( $useSorter ? $alfaCOutput : $arParams["PAGE_ELEMENT_COUNT"] ),
                        //"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                        "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                        "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                        "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                        "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

                        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
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
                        "OFFERS_FIELD_CODE" => $arParams["CATALOG_OFFERS_FIELD_CODE"],
                        "OFFERS_PROPERTY_CODE" => $arParams["CATALOG_OFFERS_PROPERTY_CODE"],
                        "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                        "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                        "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                        "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                        "OFFERS_LIMIT" => $arParams["CATALOG_OFFERS_LIMIT"],

                        "SECTION_ID" => $request->get('section'),
                        "SECTION_CODE" => "",
                        "SECTION_URL" => '', //$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                        "DETAIL_URL" => '', //$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                        "USE_MAIN_ELEMENT_SECTION" => 'N',$arParams["USE_MAIN_ELEMENT_SECTION"],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

                        'LABEL_PROP' => $arParams['LABEL_PROP'],
                        'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                        'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

                        // offers
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
                        "ADD_SECTIONS_CHAIN" => 'N', //(isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),
                        'ADD_TO_BASKET_ACTION' => $basketAction,

                        'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                        'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                        'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
                        //'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                        'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),

                        // ajaxpages
                        'IS_AJAX' => $isAjax,
                        'AJAX_ID_SECTION' => "js-ajax-section",
                        'AJAX_ID_ELEMENTS' => "js-ajax-elements",
                        'AJAX_ONLY_ELEMENTS' => $onlyElements,
                        // store
                        'USE_STORE' => $arParams['USE_STORE'],
                        'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
                        'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
                        'MAIN_TITLE' => $arParams['MAIN_TITLE'],
                        'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
                        "STORES_FIELDS" => $arParams['FIELDS'],
                        // flyaway
                        'SHOW_ERROR_EMPTY_ITEMS' => $arParams['SHOW_ERROR_EMPTY_ITEMS'],
                        "SHOW_SECTION_URL" => $arParams["SHOW_SECTION_URL"],
                        "RSFLYAWAY_PROP_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_MORE_PHOTO"],
                        "RSFLYAWAY_SKU_PROP_MORE_PHOTO" => $arParams["RSFLYAWAY_SKU_PROP_MORE_PHOTO"],
                        "RSFLYAWAY_PROP_ARTICLE" => $arParams["RSFLYAWAY_PROP_ARTICLE"],
                        "RSFLYAWAY_PROP_SKU_ARTICLE" => $arParams['RSFLYAWAY_PROP_SKU_ARTICLE'],
                        "RSFLYAWAY_PROP_BRAND" => $arParams["RSFLYAWAY_PROP_BRAND"],
                        "RSFLYAWAY_PROP_OFF_POPUP" => $arParams["RSFLYAWAY_PROP_OFF_POPUP"],
                        "RSFLYAWAY_HIDE_BASKET_POPUP" => $arParams["RSFLYAWAY_HIDE_BASKET_POPUP"],

                        "SIDEBAR" => $arResult["SIDEBAR"],
                        "RSFLYAWAY_TEMPLATE" => $alfaCTemplate,
                        "RSFLYAWAY_USE_FAVORITE" => $arParams['RSFLYAWAY_USE_FAVORITE'],
                        'PARAM_VIEW_MOB' => $viewMobileVer,
                        'TEMPLATE_AJAX_ID' => $arParams['TEMPLATE_AJAX_ID']
                    ),
                    $component
                );?>

                <div id="ajaxpages">
                    <?$APPLICATION->ShowViewContent('ajaxpages');?>
                </div>

                <div id="paginator">
                    <?$APPLICATION->ShowViewContent('paginator');?>
                </div>

                <?php
                ob_start();
                //$this->SetViewTarget('brands-catalog.sections.list');
                    global $arSectionFilter;
                    if (is_array($arSectionFilter) && count($arSectionFilter) > 0) {
                        
                        $APPLICATION->IncludeComponent(
                            "bitrix:catalog.section.list",
                            "lines",
                            Array(
                                "IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
                                "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
                                "SECTION_ID" => $request->get('section'), //$arResult["VARIABLES"]["SECTION_ID"],
                                "SECTION_CODE" => '', //$arResult["VARIABLES"]["SECTION_CODE"],
                                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                "CACHE_TIME" => $arParams["CACHE_TIME"],
                                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                "COUNT_ELEMENTS" => 'N', //$arParams["SECTION_COUNT_ELEMENTS"],
                                "TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
                                "SECTION_URL" => '?section=#SECTION_ID#', //$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                                "ADD_SECTIONS_CHAIN" => "N",
                                "FILTER_IDS" => $arSectionFilter,
                            ),
                            $component,
                            array('HIDE_ICONS'=>'Y')
                        );

                    }
                
                $sHtmlContent = ob_get_clean();
                $APPLICATION->AddViewContent('catalog_sidebar', $sHtmlContent, 300);
                //$this->EndViewTarget(); */
                ?>
            </div>

        </div>
    </div>

    <?php if ($useSorter) \Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID('catalog', '<div class="preloader"></div>'); ?>
</div>
<?php endif; ?>
