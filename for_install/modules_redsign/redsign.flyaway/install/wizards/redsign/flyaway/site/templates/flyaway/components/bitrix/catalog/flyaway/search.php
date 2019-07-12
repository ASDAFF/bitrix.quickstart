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

//global $HIDE_SIDEBAR;
//$HIDE_SIDEBAR = true;

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$this->SetViewTarget('catalog_sidebar');
    ?><div class="fixsidebar"><?
        ?><div class="hidden-sm hidden-xs"><?
        $APPLICATION->IncludeFile(SITE_DIR."include_areas/sidebar/widgets.php",array(),array("MODE"=>"html"));
        $APPLICATION->IncludeFile(SITE_DIR."include_areas/sidebar/text.php",array(),array("MODE"=>"html"));
        ?></div><?
    ?></div><?
$this->EndViewTarget();

if ($arParams['RSFLYAWAY_SHOW_SORTER'] == 'Y') {
  $useSorter = true;
}

$arElements = $APPLICATION->IncludeComponent(
  "bitrix:search.page",
  "catalog",
  Array(
    "RESTART" => $arParams["RESTART"],
    "NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"],
    "USE_LANGUAGE_GUESS" => $arParams["USE_LANGUAGE_GUESS"],
    "CHECK_DATES" => $arParams["CHECK_DATES"],
    "arrFILTER" => array("iblock_".$arParams["IBLOCK_TYPE"]),
    "arrFILTER_iblock_".$arParams["IBLOCK_TYPE"] => array($arParams["IBLOCK_ID"]),
    "USE_TITLE_RANK" => "N",
    "DEFAULT_SORT" => "rank",
    "FILTER_NAME" => "",
    "SHOW_WHERE" => "N",
    "arrWHERE" => array(),
    "SHOW_WHEN" => "N",
    "PAGE_RESULT_COUNT" => 50,
    "DISPLAY_TOP_PAGER" => "N",
    "DISPLAY_BOTTOM_PAGER" => "N",
    "PAGER_TITLE" => "",
    "PAGER_SHOW_ALWAYS" => "N",
    "PAGER_TEMPLATE" => "N",
  ),
  $component,
  array('HIDE_ICONS' => 'Y')
); ?>
<div class="catalog-content">
    <div class="row">

         <?php if (!empty($arElements) && is_array($arElements)): ?>

             <?php if($useSorter): ?>
               <div class="col col-md-12">
                 <?$APPLICATION->IncludeComponent(
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
                 );?>

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
                             "ALFA_SORT_BY_NAME" => array("name", "PROPERTY_PRICE"),
                             "ALFA_SORT_BY_DEFAULT" => "PROPERTY_PRICE_asc",
                             "ALFA_OUTPUT_OF" => array("5", "10", "15", "20", ""),
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
                             "TEMPLATE_AJAX_ID" => 'js-ajax-section'
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

                 global $searchFilter;
                 $searchFilter = array(
                   "=ID" => $arElements,
                 );

                 $APPLICATION->IncludeComponent(
                 "bitrix:catalog.section",
                 "flyaway",
                 array(
                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    "ELEMENT_SORT_FIELD" => ( $urlseSorter ? $alfaCSortType : $arParams["ELEMENT_SORT_FIELD"] ),
                    "ELEMENT_SORT_ORDER" => ( $useSorter ? $alfaCSortToo : $arParams["ELEMENT_SORT_ORDER"] ),
                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                    "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                    "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                    "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                    "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                    "BASKET_URL" => $arParams["BASKET_URL"],
                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                    "FILTER_NAME" => "searchFilter",
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "SET_TITLE" => $arParams["SET_TITLE"],
                    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                    "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                    "PAGE_ELEMENT_COUNT" => ( $useSorter ? $alfaCOutput : $arParams["PAGE_ELEMENT_COUNT"] ),
                    "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                    "USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
                    "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
                    "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
                    "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

                    "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                    "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
                    "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                    "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                    "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                    "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                    "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

                    "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                    "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "SHOW_ALL_WO_SECTION" => "Y",

                    "SECTION_ID" => "",
                    "SECTION_CODE" => "",
                    "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                    "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                    "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                    "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],

                    "LABEL_PROP" => $arParams["LABEL_PROP"],
                    "ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
                    "PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],
                                                            // offers
                    "OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
                    "OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
                    "OFFER_TREE_COLOR_PROPS" => $arParams["OFFER_TREE_COLOR_PROPS"],
                                                            'OFFER_TREE_BTN_PROPS' => $arParams['OFFER_TREE_BTN_PROPS'],
                    "PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
                    "SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
                    "SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
                    "MESS_BTN_BUY" => $arParams["MESS_BTN_BUY"],
                    "MESS_BTN_ADD_TO_BASKET" => $arParams["MESS_BTN_ADD_TO_BASKET"],
                    "MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
                    "MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
                    "MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
                    "TEMPLATE_THEME" => (isset($arParams["TEMPLATE_THEME"]) ? $arParams["TEMPLATE_THEME"] : ""),
                    "ADD_SECTIONS_CHAIN" => "N",
                    "ADD_TO_BASKET_ACTION" => $basketAction,
                    "SHOW_CLOSE_POPUP" => isset($arParams["COMMON_SHOW_CLOSE_POPUP"]) ? $arParams["COMMON_SHOW_CLOSE_POPUP"] : "",
                    "COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
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
                    "RSFLYAWAY_PROP_ARTICLE" => $arParams["RSFLYAWAY_PROP_ARTICLE"],
                    "RSFLYAWAY_PROP_BRAND" => $arParams["RSFLYAWAY_PROP_BRAND"],
                    "RSFLYAWAY_PROP_OFF_POPUP" => $arParams["RSFLYAWAY_PROP_OFF_POPUP"],

                    "SIDEBAR" => $arResult["SIDEBAR"],
                    "RSFLYAWAY_TEMPLATE" => $alfaCTemplate,
                    "RSFLYAWAY_USE_FAVORITE" => $arParams['RSFLYAWAY_USE_FAVORITE'],
                    'PARAM_VIEW_MOB' => $viewMobileVer,
                    'TEMPLATE_AJAX_ID' => $arParams['TEMPLATE_AJAX_ID']
                    ),
                    $arResult["THEME_COMPONENT"],
                    array('HIDE_ICONS' => 'Y')
               );?>
                <div id="ajaxpages">
                   <?$APPLICATION->ShowViewContent('ajaxpages');?>
                </div>

                <div id="paginator">
                   <?$APPLICATION->ShowViewContent('paginator');?>
                </div>
             </div>
         <?php elseif(is_array($arElements)): ?>
             <div class="col col-md-12"><div class="alert alert-danger"><?=Loc::getMessage("SEARCH_NOT_FOUND"); ?></div></div>
         <?php endif; ?>

    </div>
</div>
