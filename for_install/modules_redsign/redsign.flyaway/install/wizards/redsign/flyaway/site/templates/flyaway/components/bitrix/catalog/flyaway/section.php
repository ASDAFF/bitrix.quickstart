<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\ModuleManager,
    \Bitrix\Main\Loader;

$this->setFrameMode(true);

CJSCore::Init(array('currency'));

if (!\Bitrix\Main\Loader::includeModule('redsign.flyaway')) {
  return;
}

$useSorter = false;

if ($arParams['RSFLYAWAY_SHOW_SORTER'] == 'Y' && \Bitrix\Main\Loader::includeModule("redsign.devcom")) {
  $useSorter = true;
}

global $IS_CATALOG, $IS_CATALOG_SECTION, $JSON;
$IS_CATALOG = true;

if (\Bitrix\Main\Loader::includeModule("iblock")) {
	// take data about curent section
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	);

	if (IntVal($arResult["VARIABLES"]["SECTION_ID"]) > 0) {
		$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
	} elseif ($arResult["VARIABLES"]["SECTION_CODE"] != "") {
		$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
	}

	$obCache = new CPHPCache();

	if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog")) {
		$arCurSection = $obCache->GetVars();
	} elseif ($obCache->StartDataCache()) {
		$arCurSection = array();
		$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID", "LEFT_MARGIN", "RIGHT_MARGIN", "DESCRIPTION", "PICTURE"));

		if (defined("BX_COMP_MANAGED_CACHE")) {
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache("/iblock/catalog");

			if ($arCurSection = $dbRes->GetNext()) {
				$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
			}

			$CACHE_MANAGER->EndTagCache();
		} elseif (!$arCurSection = $dbRes->GetNext()) {
				$arCurSection = array();
		}

		$obCache->EndDataCache($arCurSection);
	}
	// /take data about curent section
}

$arParams['USE_FILTER'] = $arParams['SECTIONS_VIEW_MODE'] == 'VIEW_SECTIONS' && (($arCurSection["RIGHT_MARGIN"] - $arCurSection["LEFT_MARGIN"]) > 1) ? false : $arParams['USE_FILTER'];
?>

<div class="catalog">

    <?php
    if ($arParams['SECTIONS_VIEW_MODE'] == 'VIEW_SECTIONS' && (($arCurSection["RIGHT_MARGIN"] - $arCurSection["LEFT_MARGIN"]) > 1)):
        global $HIDE_SIDEBAR;
        $HIDE_SIDEBAR = true;
    ?>
        <div class="row">
            <div class="col col-md-12">
                <?$APPLICATION->IncludeComponent(
                        "bitrix:catalog.section.list",
                        "flyaway",
                        array(
                            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                            'SECTION_ID' => $arCurSection['ID'],
                            'FILTER_NAME' => $arParams['FILTER_NAME'],
                            'PRICE_CODE' => $arParams['FILTER_PRICE_CODE'],
                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                            'CACHE_TIME' => $arParams['CACHE_TIME'],
                            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                            'SAVE_IN_SESSION' => 'N',
                            'TOP_DEPTH' => 1,
                            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                            // simple
                            'PROPS_FILTER_COLORS' => $arParams['PROPS_FILTER_COLORS'],
                            'FILTER_PRICE_GROUPED' => $arParams['FILTER_PRICE_GROUPED'],
                            'FILTER_PRICE_GROUPED_FOR' => $arParams['FILTER_PRICE_GROUPED_FOR'],
                            'FILTER_PROP_SCROLL' => $arParams['FILTER_PROP_SCROLL'],
                            'FILTER_PROP_SEARCH' => $arParams['FILTER_PROP_SEARCH'],
                            'FILTER_FIXED' => $arParams['FILTER_FIXED'],
                            'FILTER_USE_AJAX' => $arParams['FILTER_USE_AJAX'],
                            'FILTER_DISABLED_PIC_EFFECT' => $arParams['FILTER_DISABLED_PIC_EFFECT'],
                            // offers
                            'PROPS_SKU_FILTER_COLORS' => $arParams['PROPS_SKU_FILTER_COLORS'],
                            'FILTER_SKU_PROP_SCROLL' => $arParams['FILTER_SKU_PROP_SCROLL'],
                            'FILTER_SKU_PROP_SEARCH' => $arParams['FILTER_SKU_PROP_SEARCH'],
                            // compare
                            'USE_COMPARE' => $arParams['USE_COMPARE'],
                            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                            //chpu url
                            "SEF_MODE" => $arParams["SEF_MODE"],
                            "SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
                            "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                        ),
                        $component,
                        array('HIDE_ICONS'=>'Y')
                );?>
            </div>
            <div class="col col-md-12 sectiondescription"><?=$arCurSection['DESCRIPTION']?></div>
        </div>
    <?php else: $IS_CATALOG_SECTION = true;?>

        <?php if ($useSorter) \Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID('catalog'); ?>

        <?php $this->SetViewTarget('catalog_sidebar'); ?>

        <div class="fixsidebar">
            <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "lines",
                array(
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
                        "TOP_DEPTH" => "1",
                        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                        "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),),
                $component,
                array('HIDE_ICONS'=>'Y')
            );?>
            <?php if ($arParams['USE_FILTER'] == 'Y'): ?>
                <?$APPLICATION->IncludeComponent(
                        'bitrix:catalog.smart.filter',
                        'flyaway',
                        array(
                                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                'SECTION_ID' => $arCurSection['ID'],
                                'FILTER_NAME' => $arParams['FILTER_NAME'],
                                'PRICE_CODE' => $arParams['FILTER_PRICE_CODE'],
                                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                'CACHE_TIME' => $arParams['CACHE_TIME'],
                                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                                'SAVE_IN_SESSION' => 'N',
                                'XML_EXPORT' => 'Y',
                                'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                                'SEF_MODE' => $arParams["SEF_MODE"],
                                'SEF_RULE' => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
                                'SMART_FILTER_PATH' => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                                'FILTER_PROP_SEARCH' => $arParams['FILTER_PROP_SEARCH'],
                                'PAGER_PARAMS_NAME' => $arParams["PAGER_PARAMS_NAME"],
                                'USE_AJAX' => $arParams['FILTER_USE_AJAX'],
                                'TEMPLATE_AJAX_ID' => "js-ajax-section",
                        ),
                        $component
                );?>
            <?php endif; ?>

            <?php
            if (ModuleManager::isModuleInstalled("sale"))
			{
				$arRecomData = array();
				$recomCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
				$obCache = new CPHPCache();
				if ($obCache->InitCache(36000, serialize($recomCacheID), "/sale/bestsellers"))
				{
					$arRecomData = $obCache->GetVars();
				}
				elseif ($obCache->StartDataCache())
				{
					if (Loader::includeModule("catalog"))
					{
						$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
						$arRecomData['OFFER_IBLOCK_ID'] = (!empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);
					}
					$obCache->EndDataCache($arRecomData);
				}

				if(!empty($arRecomData) && $arParams['USE_GIFTS_SECTION'] === 'Y')
				{
					$APPLICATION->IncludeComponent(
						"bitrix:sale.gift.section",
						".default",
						array(
							"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
							"PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
							"SHOW_NAME" => "Y",
							"SHOW_IMAGE" => "Y",
							"MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
							"MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
							"MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
							"MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
							"TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
							"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"FILTER_NAME" => $arParams["FILTER_NAME"],
							"ORDER_FILTER_NAME" => "arOrderFilter",
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action").'_sgs',
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
							"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
							"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
							"USE_PRODUCT_QUANTITY" => 'N',
							"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
							"OFFER_TREE_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
							"ADDITIONAL_PICT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ADD_PICT_PROP'],
							"ADDITIONAL_PICT_PROP_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADD_PICT_PROP'],

							"SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
							"SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
							"TEXT_LABEL_GIFT" => $arParams['GIFTS_SECTION_LIST_TEXT_LABEL_GIFT'],
							"HIDE_BLOCK_TITLE" => $arParams['GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE'],
							"BLOCK_TITLE" => $arParams['GIFTS_SECTION_LIST_BLOCK_TITLE'],
							"PAGE_ELEMENT_COUNT" => $arParams['GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT'],
							"LINE_ELEMENT_COUNT" => $arParams['GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT'],

							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						)
						+ array(
							"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
							"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);
				}
			}
			?>
        </div>
        <?php $this->EndViewTarget(); ?>


        <div class="catalog-content">
            <div class="row">

                <div class="col col-md-12 hidden-xs">
                    <?$APPLICATION->ShowViewContent('catalog_section_descr');?>
                </div>

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

                        $sorterTemplates = array(
                            'list_little',
                            'list',
                            'showcase',
                            'showcase_mob'
                        );

                        if (isset($arParams['RS_SORTER_TEMPLATES']) && count($arParams['RS_SORTER_TEMPLATES']) > 0) {
                        	 $sorterTemplates = $arParams['RS_SORTER_TEMPLATES'];
                        }

                        $sorterTemplatesCount = count($sorterTemplates);

                        $sorterTemplatesParameters = array();
                        foreach ($sorterTemplates as $index => $templateName) {
                          	$sorterTemplatesParameters["ALFA_CNT_TEMPLATES_".$index] = "";
                          	$sorterTemplatesParameters["ALFA_CNT_TEMPLATES_NAME_".$index] = $templateName;
                        }

                        $sorterOutputOf = array("5", "10", "15", "20", "");
                        if(isset($arParams['RS_SORTER_OUTPUT_OF'])) {
                      	  $sorterOutputOf = $arParams['RS_SORTER_OUTPUT_OF'];
                        }

                        $sorterOutputDefault = isset($arParams['RS_SORTER_OUTPUT_DEFAULT']) ? $arParams['RS_SORTER_OUTPUT_DEFAULT'] : 15;

                        $defaultSort = isset($arParams['RS_DEFAULT_SORT']) ? $arParams['RS_DEFAULT_SORT'] : 'name';
                        $defaultSortType = isset($arParams['RS_DEFAULT_SORT_TYPE']) ? $arParams['RS_DEFAULT_SORT_TYPE'] : 'asc';

                        $arSorts = array();
                        if (isset($arParams['RS_SORTER_AVAILABLE_SORTS']) && is_array($arParams['RS_SORTER_AVAILABLE_SORTS']) && count($arParams['RS_SORTER_AVAILABLE_SORTS'])) {
                          foreach ($arParams['RS_SORTER_AVAILABLE_SORTS'] as $sort) {
                            if ($sort == 'price') {
                              if ($arParams['RS_SORTER_PRICE_USE_PROPERTY'] == 'Y') {
                                $arSorts[] = 'PROPERTY_'.$arParams['RS_SORTER_PRICE_CODE'];
                              } else {
                                $arSorts[] = 'CATALOG_PRICE_'.$arParams['RS_SORTER_PRICE_ID'];
                              }
                            } elseif (strlen(trim($sort)) > 0) {
                              $arSorts[] = $sort;
                            }
                          }
                        } else {
                            $arSorts = array(
                                'CATALOG_PRICE_BASE',
                                'name',
                                'sort'
                            );
                        }
                        
                        $APPLICATION->IncludeComponent(
                            "redsign:catalog.sorter",
                            "flyaway",
                            array_merge(
                                array(
                                    "COMPONENT_TEMPLATE" => "flyaway",
                                    "ALFA_ACTION_PARAM_NAME" => "alfaction",
                                    "ALFA_ACTION_PARAM_VALUE" => "alfavalue",
                                    "ALFA_CHOSE_TEMPLATES_SHOW" => $arParams['RSFLYAWAY_SORTER_SHOW_TEMPLATE'],
                                    "ALFA_SORT_BY_SHOW" => $arParams['RSFLYAWAY_SORTER_SHOW_SORTING'],
                                    "ALFA_SHORT_SORTER" => "N",
                                    "ALFA_OUTPUT_OF_SHOW" => $arParams['RSFLYAWAY_SORTER_SHOW_PAGE_COUNT'],
                                    "ALFA_CNT_TEMPLATES" => $sorterTemplatesCount,
                                    "ALFA_DEFAULT_TEMPLATE" => $arParams['RSFLYAWAY_SORTER_TEMPLATE_DEFAULT'],
                                    "ALFA_SORT_BY_NAME" => $arSorts,
                                    "ALFA_SORT_BY_DEFAULT" => $defaultSort.'_'.$defaultSortType,
                                    "ALFA_OUTPUT_OF" => $sorterOutputOf,
		                                "ALFA_OUTPUT_OF_DEFAULT" => $sorterOutputDefault,
                                    "ALFA_OUTPUT_OF_SHOW_ALL" => isset($arParams['RS_SORTER_OUTPUT_ALL']) ? $arParams['RS_SORTER_OUTPUT_ALL']: 'N',
                                    "USE_FILTER" => $arParams['USE_FILTER'],
                                    "USE_AJAX" => $arParams['SORTER_USE_AJAX'],
                                    "TEMPLATE_AJAX_ID" => "js-ajax-section",
                                ),
                                $sorterTemplatesParameters
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
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "ELEMENT_SORT_FIELD" => ( $useSorter ? $alfaCSortType : $arParams["ELEMENT_SORT_FIELD"] ),
                            "ELEMENT_SORT_ORDER" => ( $useSorter ? $alfaCSortToo : $arParams["ELEMENT_SORT_ORDER"] ),
                            "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                            "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
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
                            "SET_TITLE" => $arParams["SET_TITLE"],
                            "MESSAGE_404" => $arParams["MESSAGE_404"],
                            "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                            "SHOW_404" => $arParams["SHOW_404"],
                            "FILE_404" => $arParams["FILE_404"],
                            "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                            "PAGE_ELEMENT_COUNT" => ( $useSorter ? $alfaCOutput : $arParams["PAGE_ELEMENT_COUNT"] ),
                            "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
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
                            "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                            "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                            "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

                            "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                            "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                            "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                            "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

                            'LABEL_PROP' => $arParams['LABEL_PROP'],
                            'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                            'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

                            // offers
                            'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                            'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                            "OFFER_TREE_COLOR_PROPS" => $arParams["OFFER_TREE_COLOR_PROPS"],
                            'OFFER_TREE_BTN_PROPS' => $arParams['OFFER_TREE_BTN_PROPS'],
                            'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                            'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                            'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                            'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                            'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
                            'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

                            'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                            "ADD_SECTIONS_CHAIN" => "N",
                            'ADD_TO_BASKET_ACTION' => $basketAction,
                            'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                            'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
                            'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
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
                            'TEMPLATE_AJAX_ID' => $arParams['TEMPLATE_AJAX_ID'],

                            'RSFLYAWAY_PROP_ADDITIONAL_MEASURE' => $arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE'],
                            'RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO' => $arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO']
                        ),
                        $component
                    );?>

                    <div id="ajaxpages">
                        <?$APPLICATION->ShowViewContent('ajaxpages');?>
                    </div>

                    <div id="paginator">
                        <?$APPLICATION->ShowViewContent('paginator');?>
                    </div>

                </div>

            </div>
        </div>

        <?php if ($useSorter) \Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID('catalog', '<div class="preloader"></div>'); ?>

    <?php endif; ?>
</div>
