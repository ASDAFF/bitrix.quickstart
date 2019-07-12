<!-- Header v1 -->    
<!-- header.no-border - СЃС‚РёР»СЊ Р±РµР· Р±РѕСЂРґРµСЂРѕРІ -->
<!-- header.color - СЃС‚РёР»СЊ СЃ Р·Р°Р»РёРІРєРѕР№ -->
<header class="style4 <?=$arParams['HEAD_ADD_CSS_NAME']?>">
    <!-- Navbar -->
    <div class="navbar navbar-default mega-menu" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <div class="row">
                    <div class="col col-md-3 col-sm-6">
                        <div class="box logo"><div class="in">
							<a href="#SITE_DIR#">
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "#SITE_DIR#include/company_logo.php"), false);?>
							</a>
						</div></div>
                    </div>
                    <div class="col col-md-3 hidden-xs hidden-sm vertical_blue_line">
                        <div class="box slogan roboto">
							<div class="in">
								<h4 class="roboto">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "#SITE_DIR#include/header/company_name.php"), false);?>
								</h4>
							</div>
						</div>
                    </div>
                    <div class="col col-md-3 col-sm-6 hidden-xs">
                        <div class="box contacts"><div class="in"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "#SITE_DIR#include/phones.php"), false);?></div></div>
                    </div>
                    <div class="col col-md-3 hidden-xs hidden-sm">
                        <div class="box buttons"><div class="in"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => "#SITE_DIR#include/header/buttons.php"), false);?></div></div>
                    </div>
                </div>    
                <div class="row">
                    <div class="col col-md-12">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                            <span class="full-width-menu"><?=GetMessage('RS.MONOPOLY.MENU.TITLE')?></span>
                            <span class="icon-toggle">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </span>    
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <div class="clearfix"></div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="hidden-md hidden-lg">
        <div class="collapse navbar-collapse navbar-responsive-collapse">
            <div class="container">

<?$APPLICATION->IncludeComponent(
    "bitrix:menu", 
    "main_menu", 
    array(
        "ROOT_MENU_TYPE" => "top",
        "CHILD_MENU_TYPE" => "topsub",
        "MENU_CACHE_TYPE" => "A",
        "MENU_CACHE_TIME" => "3600",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "MENU_CACHE_GET_VARS" => array(
        ),
        "MAX_LEVEL" => "4",
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
        )
    ),
    false
);?>

<!-- Search Block -->
<?$APPLICATION->IncludeComponent(
    "bitrix:search.form", 
    "monopoly", 
    array(
        "PAGE" => "#SITE_DIR#search/"
    ),
    false
);?>
<!-- End Search Block -->

                </div><!--/end container-->
            </div><!--/navbar-collapse-->
        </div>

    </div>            
    <!-- End Navbar -->
</header>
<!-- End Header v1-->    