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

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;


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
        "SET_LAST_MODIFIED" => 'N',//$arParams["SET_LAST_MODIFIED"],
        // on filter ajax request
        // Warning: Cannot modify header information - headers already sent by (output started at \bitrix\components\bitrix\catalog.smart.filter\component.php:902)
        // in D:\OpenServer\domains\activelife.fed\bitrix\modules\main\lib\httpresponse.php on line 99
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
<div class="row">
        <div class="l-side col-xs-12 col-md-3 col-lg-2d4">
            <?php $APPLICATION->ShowViewContent('brands-catalog.sections.list'); ?>
            <a class="l-side__collapsed collapsed" aria-expanded="false" data-target="#<?=$this->getEditAreaId("side");?>" data-toggle="collapse">
                <?=Loc::getMessage("RS_SLINE.BC_AL.FILTER_TITLE")?><i class="collapsed__icon"></i>
            </a>
            <div id="<?=$this->getEditAreaId("side");?>" class="l-side__collapse collapse">
            <?
            global $sSmartFilterPath;
            $arResult['VARIABLES']['SMART_FILTER_PATH'] = $sSmartFilterPath;
            ?>
            
            <?php if ($arParams["USE_FILTER"] == "Y"): ?>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:catalog.smart.filter",
                    "al",
                    array(
                        "COMPONENT_TEMPLATE" => "al",
                        "IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
                        "SECTION_ID" => $request->get('section'),
                        "FILTER_NAME" => $arParams["CATALOG_FILTER_NAME"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SAVE_IN_SESSION" => "N",
                        "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                        "XML_EXPORT" => "Y",
                        "SECTION_TITLE" => "NAME",
                        "SECTION_DESCRIPTION" => "DESCRIPTION",
                        "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                        "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                        "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                        "SEF_MODE" => "N", //$arParams["SEF_MODE"],
                        //"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
                        "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                        "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
                        "SHOW_ALL_WO_SECTION" => "Y",

                        "PRICES_GROUPED" => $arParams["FILTER_PRICES_GROUPED"],
                        "PRICES_GROUPED_FOR" => $arParams["FILTER_PRICES_GROUPED_FOR"],
                        "SCROLL_PROPS" => $arParams["FILTER_SCROLL_PROPS"],
                        "OFFER_SCROLL_PROPS" => $arParams["OFFER_FILTER_SCROLL_PROPS"],
                        "SEARCH_PROPS" => $arParams["FILTER_SEARCH_PROPS"],
                        "OFFER_SEARCH_PROPS" => $arParams["OFFER_FILTER_SEARCH_PROPS"],
                        "OFFER_TREE_COLOR_PROPS" => $arParams["OFFER_TREE_COLOR_PROPS"],
                        "OFFER_TREE_BTN_PROPS" => $arParams["OFFER_TREE_BTN_PROPS"],
                        "FILTER_FIXED" => $arParams["FILTER_FIXED"],
                        "TEMPLATE_AJAXID" => $arParams["CATALOG_TEMPLATE_AJAXID"],
                        //"MODEF_SHOW" => "N",
                        "BRAND_PROP" => $arParams["CATALOG_BRAND_PROP"],
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );?>
            <?php endif; ?>
            </div>
        </div>
        <div class="l-base col-xs-12 col-md-9 col-lg-9d6">
            <div class="catalog__head clearfix">
                <?php $APPLICATION->ShowViewContent("brand-mini"); ?>
                <h1 class="webpage__title"><?$APPLICATION->ShowTitle(false)?></h1>
            </div>
            <?php
            $APPLICATION->ShowViewContent("brand-full");
            $APPLICATION->ShowViewContent("catalog_filterin");
            ?>

            <div class="clearfix">
                <div class="catalog__sorter">
                    <?php
                    global $alfaCTemplate, $alfaCSortType, $alfaCSortToo, $alfaCOutput;
                    include($_SERVER["DOCUMENT_ROOT"].SITE_DIR."/include/template/components/bitrix/catalog/al/catalog.sorter.php");
                    ?>
                </div>
                <div class="catalog__pagenav js-catalog_refresh" id="<?=$arParams['CATALOG_TEMPLATE_AJAXID']?>_pager" data-ajax-id="<?=$arParams['CATALOG_TEMPLATE_AJAXID']?>" data-history-push="">
                    <?$APPLICATION->ShowViewContent("catalog_pager");?>
                </div>
            </div>
            <?php
            $basketAction = (isset($arParams['SECTION_ADD_TO_BASKET_ACTION']) ? $arParams['SECTION_ADD_TO_BASKET_ACTION'] : '');
            ?>
            <?php
            //include($_SERVER["DOCUMENT_ROOT"].SITE_DIR."/include/template/components/bitrix/news/brands/catalog.section.php");
            ?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "catalog",
                array(
                    "IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
                    "ELEMENT_SORT_FIELD" => $alfaCSortType,//$arParams["ELEMENT_SORT_FIELD"],
                    "ELEMENT_SORT_ORDER" => $alfaCSortToo,//$arParams["ELEMENT_SORT_ORDER"],
                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD"],
                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER"],

                    "PROPERTY_CODE" => $arParams["CATALOG_PROPERTY_CODE"],
                    //"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                    //"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                    //"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                    //"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                    "INCLUDE_SUBSECTIONS" => 'Y',
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
                    "PAGE_ELEMENT_COUNT" => $alfaCOutput,//$arParams["PAGE_ELEMENT_COUNT"],
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
                    "ADD_SECTIONS_CHAIN" => 'N', //(isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),
                    'ADD_TO_BASKET_ACTION' => $basketAction,

                    "TEMPLATE_AJAXID" => $arParams["CATALOG_TEMPLATE_AJAXID"],
                    "USE_AJAXPAGES" => $arParams["CATALOG_USE_AJAXPAGES"],
                    "ICON_MEN_PROP" => $arParams["ICON_MEN_PROP"],
                    "ICON_WOMEN_PROP" => $arParams["ICON_WOMEN_PROP"],
                    "ICON_NOVELTY_PROP" => $arParams["ICON_NOVELTY_PROP"],
                    "NOVELTY_TIME" => $arParams["NOVELTY_TIME"],
                    "ICON_DISCOUNT_PROP" => $arParams["ICON_DISCOUNT_PROP"],
                    "ICON_DEALS_PROP" => $arParams["ICON_DEALS_PROP"],
                    "USE_LIKES" => $arParams["USE_LIKES"],
                    'USE_SHARE' => $arParams['USE_SHARE'],
                    'SOCIAL_SERVICES' => $arParams['SOCIAL_SERVICES'],
                    'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
                    'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
                    'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
                    'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],
                    //"BRAND_LOGO_PROP" => $arParams["BRAND_LOGO_PROP"],
                    "BRAND_PROP" => $arParams["CATALOG_BRAND_PROP"],
                    //"ACCESSORIES_PROP" => $arParams["ACCESSORIES_PROP"],
                    "POPUP_DETAIL_VARIABLE" => $arParams["POPUP_DETAIL_VARIABLE"],
                    "ERROR_EMPTY_ITEMS" => $arParams["ERROR_EMPTY_ITEMS"],
                    "PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
                    'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
                    //'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                    'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
                    'COMPOSITE_FRAME' => 'Y',
                    "SHOW_ALL_WO_SECTION" => "Y", // set smart.filter + INCLUDE_SUBSECTIONS=Y = bug
                ),
                $component
            );?>

            <?php $this->SetViewTarget('brands-catalog.sections.list'); ?>
            <div class="l-side__collapse collapse">
                <? global $arSectionFilter; ?>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:catalog.section.list",
                    "al",
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
                        "SHOW_SECTION_PICTURE" => $arParams["SHOW_SECTION_PICTURE"],
                        "SECTION_PICTURE_WIDTH" => $arParams["SECTION_PICTURE_WIDTH"],
                        "SECTION_PICTURE_HEIGHT" => $arParams["SECTION_PICTURE_HEIGHT"],
                        "ADD_SECTIONS_CHAIN" => "N",
                        "FILTER_IDS" => $arSectionFilter,
                    ),
                    $component
                );?>
            </div>
            <?php $this->EndViewTarget(); ?>
        </div>
    </div>
<?php endif; ?>