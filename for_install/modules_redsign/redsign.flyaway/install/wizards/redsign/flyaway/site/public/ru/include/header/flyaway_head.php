<?php
$headType = rsFlyaway::getSettings('openMenuType', 'type1');
$RSStickyHeader = rsFlyaway::getSettings('StickyHeader', 'Y');
?>

<div class="fly-header-wrap __sidemenu<?php if($RSStickyHeader == 'Y') echo ' js-sticky-header';?>" id="fly-header">
    <div class="fly-header-sidenav-button js-toggle-mainmenu">
        <div class="fly-header-sidenav-button__valign">
            <div class="fly-header-sidenav-button__lines">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
            <span class="fly-header-sidenav-button__text">РњРµРЅСЋ</span>
        </div>
    </div>
    <header class="fly-header">

        <div class="fly-header__menu-button js-toggle-mainmenu">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>

        <div class="fly-header__logo">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "/include/company_logo.php"), false);?>
        </div>

        <div class="fly-header__tagline">
            <div class="header-slogan">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "/include/header/slogan.php"), false);?>
            </div>
        </div>
        <div class="fly-header__contacts">
           <div class="header-contacts">
               <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "/include/header/phones.php"), false);?>
           </div>
       </div>

        <div class="fly-header__search">
            <div class="loss-menu-right views fly-header__search-icon js-fly-header__search-icon">
                <a class="selected" href="#" onclick="return false;">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="header-search__form js-header-search__form">
                <div class="fly-header__search-close js-fly-header__search-close"><i class="fa fa-times"></i></div>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:search.title",
                    "flyaway",
                    Array(
                      "CATEGORY_0" => array("iblock_catalog"),
                      "CATEGORY_0_TITLE" => "",
                      "CATEGORY_0_iblock_catalog" => array("#IBLOCK_ID_catalog_catalog#"),
                      "CATEGORY_0_main" => array(""),
                      "CATEGORY_0_socialnetwork" => array("all"),
                      "CATEGORY_OTHERS_TITLE" => "",
                      "CHECK_DATES" => "N",
                      "CONTAINER_ID" => "title-search",
                      "CONVERT_CURRENCY" => "Y",
                      "CURRENCY_ID" => "RUB",
                      "IBLOCK_ID" => array("#IBLOCK_ID_catalog_catalog#"),
                      "INPUT_ID" => "title-search-input",
                      "NUM_CATEGORIES" => "1",
                      "OFFERS_FIELD_CODE" => array("",""),
                      "OFFERS_PROPERTY_CODE" => array("",""),
                      "ORDER" => "date",
                      "PAGE" => "/catalog/",
                      "PREVIEW_HEIGHT" => "100",
                      "PREVIEW_TRUNCATE_LEN" => "",
                      "PREVIEW_WIDTH" => "100",
                      "PRICE_CODE" => array("BASE"),
                      "PRICE_VAT_INCLUDE" => "Y",
                      "PRODUCT_QUANTITY_VARIABLE" => "",
                      "SHOW_INPUT" => "Y",
                      "SHOW_OTHERS" => "N",
                      "SHOW_PREVIEW" => "Y",
                      "TOP_COUNT" => "5",
                      "USE_LANGUAGE_GUESS" => "N",
                      "USE_PRODUCT_QUANTITY" => "N"
                    )
                  );?>
                </div>
            </div>

            <div class="fly-header__icons">
                <div class="clearfix" style="position: relative;">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:sale.basket.basket.line",
                        "flyaway",
                        array(
                            "PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",
                            'HIDE_ON_BASKET_PAGES' => 'N',
                            "PATH_TO_PERSONAL" => "#SITE_DIR#personal/",
                            "SHOW_PERSONAL_LINK" => "N",
                            "SHOW_NUM_PRODUCTS" => "N",
                            "SHOW_TOTAL_PRICE" => "Y",
                            "SHOW_EMPTY_VALUES" => "Y",
                            "SHOW_PRODUCTS" => "Y",
                            "PATH_TO_ORDER" => "#SITE_DIR#personal/order/",
                            "SHOW_DELAY" => "N",
                            "SHOW_NOTAVAIL" => "N",
                            "SHOW_SUBSCRIBE" => "N",
                            "SHOW_IMAGE" => "Y",
                            "SHOW_PRICE" => "Y",
                            "SHOW_SUMMARY" => "Y"
                        )
                    );?>
                    <?$APPLICATION->IncludeComponent(
                        "redsign:favorite.list",
                        "inheader",
                        array(
                            "CACHE_TYPE" => "N",
                            "CACHE_TIME" => "3600",
                            "ACTION_VARIABLE" => "topaction",
                            "PRODUCT_ID_VARIABLE" => "id",
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "#IBLOCK_ID_catalog_catalog#",
                            "SHOW_POPUP" =>  "Y",
					                  "PATH_TO_FAVORITE" => "#SITE_DIR#personal/favorite/",
                            "PRICE_CODE" => array(
                                0 => "BASE",
                                1 => "WHOLE",
                                2 => "second",
                                3 => "RETAIL",
                                4 => "EXTPRICE",
                                5 => "EXTPRICE2"
                            )
                        ),
                        false
                    );?>
            </div>
        </div>

    </header>
    <?php
    if($headType != 'type3'):
    ?>
    <div class="top-menu">
        <div class="no-border collapse navbar-collapse navbar-responsive-collapse">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <?
                        if($headType  == "type2") {
                            $menuTempl = "main_menu2";
                        } else {
                            $menuTempl = "main_menu";
                        }
                        ?>
                        <?$APPLICATION->IncludeComponent(
                        	"bitrix:menu",
                        	$menuTempl,
                        	array(
                        		"ROOT_MENU_TYPE" => "catalog",
                        		"CHILD_MENU_TYPE" => "topsub",
                        		"MENU_CACHE_TYPE" => "A",
                        		"MENU_CACHE_TIME" => "3600",
                        		"MENU_CACHE_USE_GROUPS" => "Y",
                        		"MENU_CACHE_GET_VARS" => array(
                        		),
                        		"MAX_LEVEL" => "3",
                        		"USE_EXT" => "Y",
                        		"DELAY" => "N",
                        		"ALLOW_MULTI_SELECT" => "N",
                        		"CATALOG_PATH" => "#SITE_DIR#catalog/",
                        		"MAX_ITEM" => "9",
                        		"IBLOCK_ID" => "#IBLOCK_ID_catalog_catalog#",
                        		"PRICE_CODE" => array(
                        			0 => "BASE",
                        		),
                        		"PRICE_VAT_INCLUDE" => "N",
                        		"OFFERS_PROPERTY_CODE" => array(
                        			0 => "",
                        			1 => "",
                        		),
                        		"CONVERT_CURRENCY" => "N",
                        		"USE_PRODUCT_QUANTITY" => "N",
                        		"PRODUCT_QUANTITY_VARIABLE" => "quan",
                        		"OFFERS_FIELD_CODE" => array(
                        			0 => "",
                        			1 => "",
                        		),
                        		"RSFLYAWAY_IS_SHOW_PRODUCTS" => "Y",
                        		"RSFLYAWAY_IS_SHOW_IMAGE" => "Y",
                        		"COMPONENT_TEMPLATE" => "main_menu2",
                        		"IBLOCK_TYPE" => "catalog",
                        		"RSFLYAWAY_PROP_MORE_PHOTO" => "MORE_PHOTO",
                        		"RSFLYAWAY_PROP_SKU_MORE_PHOTO" => "MORE_PHOTO",
                        		"PROPERTY_CODE_ELEMENT_IN_MENU" => "SHOW_IN_MENU"
                        	),
                        	false
                        );?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
