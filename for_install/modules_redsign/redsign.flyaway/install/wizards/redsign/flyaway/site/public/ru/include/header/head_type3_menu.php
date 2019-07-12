<header class="header head_type1 head_type3"> 
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
                <div class="col-sm-3 col-xs-8 col-md-3 col-lg-4">
                    <div class="row">
                        <div class="col-xs-12 col-md-12 col-lg-6 header-logo">
                            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "#SITE_DIR#include/company_logo.php"), false);?>
                        </div>
                        <div class="col-lg-6 header-slogan visible-lg">
                            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "#SITE_DIR#include/header/slogan.php"), false);?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9 col-xs-2 col-md-9 col-lg-8">
                    <div class="header-contacts">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "#SITE_DIR#include/header/phones.php"), false);?>
                    </div>
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
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
		
		<div class="top-menu">
        <div class="no-border collapse navbar-collapse navbar-responsive-collapse">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
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
</header>
