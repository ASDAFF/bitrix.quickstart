<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?$this->setFrameMode(true);?>

<?
$block_nav = GetIBlock($arParams['IBLOCK_ID']);
$APPLICATION->AddChainItem($block_nav['NAME'], $arResult['FOLDER']);
?>

<?
$res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arParams['IBLOCK_ID'], "CODE" => $arResult['VARIABLES']['ELEMENT_CODE']), false, array("nPageSize" => 1), array('PROPERTY_ORDER_PROJECTS'));
if ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    $order = $arFields['PROPERTY_ORDER_PROJECTS_VALUE'];
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
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "",
                "USE_EXT" => "Y",
                "DELAY" => "N",
            ),
            false
        );?>

        <?if ($order == "Y"){?>
            <div class="order-block">
                <div class="title3">
                    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR . "include/projects/desc_3.php",
                        ),
                        false
                    );?>
                </div>
                <p>
                    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR . "include/projects/desc_4.php",
                        ),
                        false
                    );?>
                </p>
                <a class="btn popbutton" data-window="order_projects">
                    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR . "include/projects/desc_5.php",
                        ),
                        false
                    );?>
                </a>
            </div>
        <?}?>
    </div>

    <div class="col2 catalog-v2 projects_box">
        <?$APPLICATION->IncludeComponent(
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
                "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
                "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                "META_KEYWORDS" => $arParams["META_KEYWORDS"],
                "META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
                "BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
                "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
                "SET_TITLE" => $arParams["SET_TITLE"],
                "SET_STATUS_404" => $arParams["SET_STATUS_404"],
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
                "IBLOCK_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],
                "USE_SHARE" => $arParams["USE_SHARE"],
                "SHARE_HIDE" => $arParams["SHARE_HIDE"],
                "SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
                "SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
                "SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
                "SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],

                "INNET_ALLOW_REVIEWS" => $arParams["INNET_ALLOW_REVIEWS"],
                "INNET_IBLOCK_ID_ORDER" => $arParams["INNET_IBLOCK_ID_ORDER"],
                "INNET_IBLOCK_ID_REVIEWS" => $arParams["INNET_IBLOCK_ID_REVIEWS"],
                "INNET_IBLOCK_ID_SERVICES" => $arParams["INNET_IBLOCK_ID_SERVICES"],
            ),
            $component
        );?>
    </div>
</div>