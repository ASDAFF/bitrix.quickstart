<header class="header head_type2">
    <div class="head_info">
        <div class="container">
            <div class="row">
                <div class="col-xs-2 navbar-toggle-hide">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <span class="full-width-menu"><?=GetMessage('RS.MONOPOLY.MENU.TITLE')?></span>
                        <span class="icon-toggle">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </span>
                    </button>
                </div>
                <div class="col col-sm-3 col-xs-8 col-md-2 col-lg-2">
                    <div class="header-logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "#SITE_DIR#include/company_logo.php"), false);?></div>
                </div>
                <div class="col col-sm-6 col-xs-2 col-md-8 col-lg-8">
                    <div class="header-search">
                        <div class="loss-menu-right views">
                            <a class="selected" href="javascript:void(0);">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                        <div class="header-search__form">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:search.form",
                                "flyaway",
                                array(
                                    "PAGE" => "#SITE_DIR#search/"
                                ),
                                false
                            );?>
                        </div>
                    </div>
                    <div class="wrap-top-menu hidden-xs">
                        <div class="top-menu menu_left">
                            <div class="no-border collapse navbar-collapse navbar-responsive-collapse">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-12 padding-no">
                                            <?$APPLICATION->IncludeComponent(
												"bitrix:menu",
												"main_menu",
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
													"IBLOCK_ID" => "",
													"PRICE_CODE" => "",
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
													"COMPONENT_TEMPLATE" => "main_menu"
												),
												false
											);?>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="col-sm-3 col-xs-2 col-md-2 col-lg-2 hidden-xs">

                <?$APPLICATION->IncludeComponent(
                    "bitrix:sale.basket.basket.line",
                    "flyaway",
                    array(
                        "PATH_TO_BASKET" => "#SITE_DIR#personal/cart",
                        "PATH_TO_PERSONAL" => "#SITE_DIR#personal/",
                        "SHOW_PERSONAL_LINK" => "N",
                        "SHOW_NUM_PRODUCTS" => "N",
                        "SHOW_TOTAL_PRICE" => "Y",
                        "SHOW_EMPTY_VALUES" => "Y",
                        "SHOW_PRODUCTS" => "Y",
                        "POSITION_FIXED" => "N",
                        "POSITION_HORIZONTAL" => "right",
                        "POSITION_VERTICAL" => "top",
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
                <!--<div class="col col-md-1 col-lg-1 col-sm-1 col-xs-2 search">
                    <div class="search-icon">
                        <div class="loss-menu-right views">
                            <a class="selected" href="javascript:void(0);">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                        <div class="search-form">
                            <?/*$APPLICATION->IncludeComponent(
                                "bitrix:search.form",
                                "flyaway",
                                array(
                                    "PAGE" => "#SITE_DIR#search/"
                                ),
                                false
                            );*/?>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
    </div>

    <div class="wrap-menu">
    </div>
</header>
