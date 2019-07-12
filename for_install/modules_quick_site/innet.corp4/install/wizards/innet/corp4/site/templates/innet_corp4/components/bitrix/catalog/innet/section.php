<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?
$block_nav = GetIBlock($arParams['IBLOCK_ID']);
$APPLICATION->AddChainItem($block_nav['NAME'], $arResult['FOLDER']);

$arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $arResult['VARIABLES']['SECTION_ID'], 'GLOBAL_ACTIVE' => 'Y');
$db_list = CIBlockSection::GetList(array(), $arFilter, true, array('DESCRIPTION'));
if ($ar_result = $db_list->GetNext()) {
    $description = $ar_result['~DESCRIPTION'];
}
?>

<div class="cols2">
    <div class="col1">
        <?$APPLICATION->IncludeComponent("bitrix:menu", "left", array(
                "ROOT_MENU_TYPE" => "left",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(),
                "MAX_LEVEL" => "4",
                "CHILD_MENU_TYPE" => "",
                "USE_EXT" => "Y",
                "DELAY" => "N",
            ),
            false
        );?>

        <?if ($arParams['USE_FILTER'] == 'Y') {
            $arFilter = array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ACTIVE" => "Y",
                "GLOBAL_ACTIVE" => "Y",
            );
            if (0 < intval($arResult["VARIABLES"]["SECTION_ID"])) {
                $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
            } elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"]) {
                $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
            }

            $obCache = new CPHPCache();
            if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog")) {
                $arCurSection = $obCache->GetVars();
            } elseif ($obCache->StartDataCache()) {
                $arCurSection = array();
                if (\Bitrix\Main\Loader::includeModule("iblock")) {
                    $dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID"));

                    if (defined("BX_COMP_MANAGED_CACHE")) {
                        global $CACHE_MANAGER;
                        $CACHE_MANAGER->StartTagCache("/iblock/catalog");

                        if ($arCurSection = $dbRes->Fetch()) {
                            $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"]);
                        }
                        $CACHE_MANAGER->EndTagCache();
                    } else {
                        if (!$arCurSection = $dbRes->Fetch())
                            $arCurSection = array();
                    }
                }
                $obCache->EndDataCache($arCurSection);
            }
            ?>

            <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.smart.filter",
                "catalog",
                Array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SECTION_ID" => $arCurSection['ID'],
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"POPUP_POSITION" => "right",
					"DISPLAY_ELEMENT_COUNT" => "Y",
                    "SAVE_IN_SESSION" => "N",
                    "XML_EXPORT" => "Y",
                    "SECTION_TITLE" => "NAME",
                    "SECTION_DESCRIPTION" => "DESCRIPTION",
                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                    "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                    "INNET_FILTER_REFERENCE" => $arParams["INNET_FILTER_REFERENCE"],
                    "INNET_OFFERS_PROPERTIES_COLOR" => $arParams['INNET_OFFERS_PROPERTIES_COLOR'],
                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            );?>
        <?}?>
    </div>

    <div class="col2 catalog-v2">
        <p><?=$description?></p>

        <?
        $sort = $arParams['ELEMENT_SORT_ORDER'];
        $sortName = $arParams['ELEMENT_SORT_FIELD'];

        if (!empty($_REQUEST['sort'])) {
            if ($_REQUEST['sort'] == 'asc_name') {
                $sort = 'asc';
                $sortName = 'name';
            } elseif ($_REQUEST['sort'] == 'desc_name') {
                $sort = 'desc';
                $sortName = 'name';
            } elseif ($_REQUEST['sort'] == 'asc_price') {
                $sort = 'asc';
                $sortName = 'property_PRICE';
            } elseif ($_REQUEST['sort'] == 'desc_price') {
                $sort = 'desc';
                $sortName = 'property_PRICE';
            }
        }
        ?>
        <div class="clearfix">
            <div class="fll">
                <form action="" method="GET" name="form_bottom">
                    <select class="select" name="sort" onchange="form_bottom.submit();">
                        <option><?=GetMessage('INNET_CATALOG_SORT_0');?>:</option>
                        <option value="asc_price" <?if($_GET["sort"] == 'asc_price'){echo "selected";}?>><?=GetMessage('INNET_CATALOG_SORT_1');?></option>
                        <option value="desc_price" <?if($_GET["sort"] == 'desc_price'){echo "selected";}?>><?=GetMessage('INNET_CATALOG_SORT_2');?></option>
                        <option value="asc_name" <?if($_GET["sort"] == 'asc_name'){echo "selected";}?>><?=GetMessage('INNET_CATALOG_SORT_3');?></option>
                        <option value="desc_name" <?if($_GET["sort"] == 'desc_name'){echo "selected";}?>><?=GetMessage('INNET_CATALOG_SORT_4');?></option>
                    </select>
                </form>
            </div>

            <div class="flr view-style">
                <span><?=GetMessage('INNET_CATALOG_VIEW')?>:</span>
                <a class="view-style1 active" rel="bx_catalog_list_home"></a>
                <a class="view-style2" rel="items-view2"></a>
                <a class="view-style3" rel="items-view3"></a>
            </div>
        </div>
        <?$APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "innet",
            array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ELEMENT_SORT_FIELD" => $sortName,//$arParams["ELEMENT_SORT_FIELD"],
                "ELEMENT_SORT_ORDER" => $sort,//$arParams["ELEMENT_SORT_ORDER"],
                "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
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
                "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
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
                "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

                'LABEL_PROP' => $arParams['LABEL_PROP'],
                'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

                'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
                'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
                'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],

                'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                "ADD_SECTIONS_CHAIN" => "N",

                "INNET_IBLOCK_ID_ORDER" => $arParams['INNET_IBLOCK_ID_ORDER'],
                "INNET_IBLOCK_ID_QUESTIONS" => $arParams['INNET_IBLOCK_ID_QUESTIONS'],
                "INNET_IBLOCK_ID_REVIEWS" => $arParams['INNET_IBLOCK_ID_REVIEWS'],
                "INNET_ALLOW_REVIEWS" => $arParams['INNET_ALLOW_REVIEWS'],
                "INNET_PREVIEW_TEXT_DETAIL" => $arParams['INNET_PREVIEW_TEXT_DETAIL'],
                "INNET_DISPLAY_PROPERTIES_SECTION" => $arParams['INNET_DISPLAY_PROPERTIES_SECTION'],
                "INNET_PREVIEW_TEXT_SECTION" => $arParams['INNET_PREVIEW_TEXT_SECTION'],
                "INNET_PREVIEW_TEXT_SECTION_COUNT" => $arParams['INNET_PREVIEW_TEXT_SECTION_COUNT'],
                "INNET_MESS_BTN_DELAY" => $arParams['INNET_MESS_BTN_DELAY'],
                "INNET_USE_DELAY" => $arParams['INNET_USE_DELAY'],
                "INNET_DELAY_PATH" => $arParams['INNET_DELAY_PATH'],
            ),
            $component
        );?>
    </div>
</div>