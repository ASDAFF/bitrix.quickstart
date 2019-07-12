<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- <?=GetMessage("DVS_COP")?> -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery-1.7.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/camera.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery.selectBox.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/products.js"></script>
	
	<script src="<?=SITE_TEMPLATE_PATH?>/j/encode_form_field_utf8_detect.js"></script>
	<script>
	if (encodeFormFieldIsPageOnUTF8()){
		document.write('<'+'script src="<?=SITE_TEMPLATE_PATH?>/j/encode_form_field_utf8_stub.js"></'+'script>');
	}else{
		document.write('<'+'script src="<?=SITE_TEMPLATE_PATH?>/j/encode_form_field.js"></'+'script>');
		window.encodeURIComponent = function(text) { return(encodeFormField(text)); }
	}
	</script>
	
    <!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script><![endif]-->
    <?$APPLICATION->ShowHead();?>
    <link href="<?=SITE_TEMPLATE_PATH?>/color.css" type="text/css" rel="stylesheet" />
    <link href="<?=SITE_TEMPLATE_PATH?>/camera.css" type="text/css" rel="stylesheet" />
</head>

<?if (CSite::inDir(SITE_DIR."catalog/")) {
    $isDetailPage = (count(explode("/", rtrim(str_replace(SITE_DIR . "catalog/", "", $APPLICATION->GetCurDir()), "/"))) > 1 ? 1 : 0);
}?>
<body<?=(CSite::inDir(SITE_DIR."catalog/") ? ($isDetailPage ? ' class="item"' : ' class="catalog"') : '')?>>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div id="overlay"></div>
<? $APPLICATION->IncludeComponent(
	"fashion:callback",
	"dvs_callback",
	array(
		"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME_FROM", "TIME_TILL"),
		"TIME_FROM" => "18:00",
		"TIME_TILL" => "20:00"
	)
);
?>
<?if($isDetailPage){?>
<div id="cart-confirm" class="dialog">
    <h3><?=GetMessage("ADDED2BASKET")?> <strong></strong></h3>
    <div class="info">
        <p class="image"><img id="cart-image" /></p>
        <ul class="opts">
            <li><?=GetMessage("COLOR")?>: <span class="color" id="cart-color"></span></li>
            <li><?=GetMessage("SIZE")?>: <strong id="cart-size"></strong></li>
            <li><?=GetMessage("COST")?>: <strong id="cart-price"></strong> <span class="rub"><?=GetMessage("RUB")?></span></li>
            <li><?=GetMessage("COUNT")?>: <strong id="cart-quantity"></strong></li>
            <li><?=GetMessage("TOTAL")?>: <strong id="cart-overall"></strong> <span class="rub"><?=GetMessage("RUB")?></span></li>
        </ul>
    </div>
    <p>
        <a href="#" class="button close"><?=GetMessage("GO2ITEMS")?></a>
    </p>
    <p>
        <input type="submit" value="<?=GetMessage("GO2BASKET")?>" id="go-to-basket" />
        <script>$('#go-to-basket').click(function (){document.location.href = "<?=SITE_DIR?>personal/cart/";});</script>
    </p>
</div>
<?}?>

<div class="back">
    <div class="back-2">

<div id="wrapper">
    <div id="header" itemscope itemtype = "http://schema.org/LocalBusiness">
        <div class="title vcard">
            <div class="wrapper">
            <a href="<?=SITE_DIR?>"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></a>
            <abbr itemprop = "name" class="category"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?></abbr>
            </div>
        </div><!-- .logo -->

        <div class="user-links">
            <div class="wrapper">
                <span id="top-cart"><?$APPLICATION->IncludeComponent(
                    "bitrix:sale.basket.basket.small",
                    "",
                    Array(
                        "PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",
                        "PATH_TO_ORDER" => "#SITE_DIR#personal/order/"
                    ),
                false
                );?></span>
                <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "", array(
                    "REGISTER_URL" => "#SITE_DIR#auth/",
                    "PROFILE_URL" => "#SITE_DIR#personal/",
                    "SHOW_ERRORS" => "N"
                    ),
                    false,
                    Array()
                );?>
            </div>
        </div>

        <div class="adr">
            <div class="wrapper">
            <address itemprop = "address"><strong><?=GetMessage("ADDRESS")?></strong> <span class="locality"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/locality.php"), false);?></span>, <span class="street-address"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/street_address.php"), false);?></span></address>
            <p><a href="<?=SITE_DIR?>contacts/" class="sheme"><?=GetMessage("SCHEME")?></a></p>
            </div>
        </div>

        <div class="contacts vcard">
            <div class="wrapper">
            <abbr itemprop = "telephone" class="tel"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></abbr>
            <span itemprop = "openingHours" class="workhours"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?></span>
            <br /><a id="open-dialog"><?=GetMessage("ORDER_CALLBACK");?></a>
			</div>
        </div>

        <?$APPLICATION->IncludeComponent("bitrix:menu", "top", array(
            "ROOT_MENU_TYPE" => "catalog",
            "MENU_CACHE_TYPE" => "A",
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS" => array(
            ),
            "MAX_LEVEL" => "2",
            "CHILD_MENU_TYPE" => "catalog",
            "USE_EXT" => "Y",
            "DELAY" => "N",
            "ALLOW_MULTI_SELECT" => "N"
            ),
            false
        );?>
    </div><!-- #header -->

    <?$APPLICATION->IncludeComponent("bitrix:menu", "all", array(
        "ROOT_MENU_TYPE" => "catalog",
        "MENU_CACHE_TYPE" => "A",
        "MENU_CACHE_TIME" => "3600",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "MENU_CACHE_GET_VARS" => array(
        ),
        "MAX_LEVEL" => "2",
        "CHILD_MENU_TYPE" => "catalog",
        "USE_EXT" => "Y",
        "DELAY" => "N",
        "ALLOW_MULTI_SELECT" => "N"
        ),
        false
    );?>

    <div id="middle">
    <div id="container">
    <div id="content" class="no-sidebar">
		<div id="index-main">
			<table class="monindex">
				<tr>
					<td rowspan="2">
						<?$APPLICATION->IncludeComponent("bitrix:news.list","banners",Array(
								"DISPLAY_DATE" => "Y",
								"DISPLAY_NAME" => "Y",
								"DISPLAY_PICTURE" => "Y",
								"DISPLAY_PREVIEW_TEXT" => "Y",
								"AJAX_MODE" => "Y",
								"IBLOCK_TYPE" => "content",
								"IBLOCK_ID" => "#BANNER_IBLOCK_ID#",
								"NEWS_COUNT" => "10",
								"SORT_BY1" => "ACTIVE_FROM",
								"SORT_ORDER1" => "DESC",
								"SORT_BY2" => "SORT",
								"SORT_ORDER2" => "ASC",
								"FILTER_NAME" => "",
								"FIELD_CODE" => Array("ID", "NAME", "DETAIL_PICTURE", "PREVIEW_PICTURE", "DETAIL_TEXT", "PREVIEW_TEXT"),
								"PROPERTY_CODE" => Array("LINK"),
								"CHECK_DATES" => "Y",
								"DETAIL_URL" => "",
								"PREVIEW_TRUNCATE_LEN" => "",
								"ACTIVE_DATE_FORMAT" => "d.m.Y",
								"SET_TITLE" => "N",
								"SET_STATUS_404" => "Y",
								"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
								"ADD_SECTIONS_CHAIN" => "N",
								"HIDE_LINK_WHEN_NO_DETAIL" => "Y",
								"PARENT_SECTION" => "",
								"PARENT_SECTION_CODE" => "",
								"INCLUDE_SUBSECTIONS" => "Y",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "3600",
								"CACHE_FILTER" => "Y",
								"CACHE_GROUPS" => "Y",
								"DISPLAY_TOP_PAGER" => "Y",
								"DISPLAY_BOTTOM_PAGER" => "Y",
								"PAGER_TITLE" => "",
								"PAGER_SHOW_ALWAYS" => "Y",
								"PAGER_TEMPLATE" => "",
								"PAGER_DESC_NUMBERING" => "Y",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
								"PAGER_SHOW_ALL" => "Y",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"AJAX_OPTION_HISTORY" => "N",
								"AJAX_OPTION_ADDITIONAL" => ""
							)
						);?>
					</td>
					<td rowspan="2" style="width:5px">&nbsp;</td>
					<td style="padding-bottom:7px;">
						<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/banner1.php",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</td>			
				</tr>
				<tr>
					<td>
						<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/banner2.php",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</td>
				</tr>
			</table>
			
			<div style="clear:both"></div>
			<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list",
				"",
				Array(
					"IBLOCK_TYPE" => "catalog",
					"IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#",
					"SECTION_ID" => "",
					"SECTION_CODE" => "",
					"SECTION_URL" => "",
					"COUNT_ELEMENTS" => "Y",
					"TOP_DEPTH" => "1",
					"SECTION_FIELDS" => "",
					"SECTION_USER_FIELDS" => "",
					"ADD_SECTIONS_CHAIN" => "Y",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_NOTES" => "",
					"CACHE_GROUPS" => "Y"
				)
			);
			
			
			$newFilter = array("!PROPERTY_models_new" => false);
			$APPLICATION->IncludeComponent(
				"fashion:catalog.section",
				"index",
				Array(
					"TITLE" => GetMessage("DVS_NEW_GOODS"),
					"BY_LINK" => "Y",
					"IBLOCK_TYPE" => "catalog",
					"IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#",
					"ELEMENT_SORT_FIELD" => "RAND",
					"ELEMENT_SORT_ORDER" => "ASC",
					"PROPERTY_CODE" => array(
						0 => "models_hit",
						1 => "models_new",
						2 => "models_rating",
						3 => "",
					),
					"META_KEYWORDS" => "-",
					"META_DESCRIPTION" => "-",
					"BROWSER_TITLE" => "-",
					"INCLUDE_SUBSECTIONS" => "Y",
					"BASKET_URL" => SITE_DIR."personal/basket.php",
					"ACTION_VARIABLE" => "action",
					"PRODUCT_ID_VARIABLE" => "id",
					"SECTION_ID_VARIABLE" => "SECTION_ID",
					"FILTER_NAME" => "newFilter",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"DISPLAY_COMPARE" => "N",
					"PAGE_ELEMENT_COUNT" => "4",
					"LINE_ELEMENT_COUNT" => "4",
					"PRICE_CODE" => array(
						0 => "BASE",
					),
					"USE_PRICE_COUNT" => "N",
					"SHOW_PRICE_COUNT" => "1",
					"ADD_SECTIONS_CHAIN" => "N",
					"PRICE_VAT_INCLUDE" => "Y",

					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"PAGER_TITLE" => "title",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => "",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"OFFERS_CART_PROPERTIES" => array(
						0 => "item_color",
						1 => "item_size",
					),
					"OFFERS_FIELD_CODE" => array(),
					"OFFERS_PROPERTY_CODE" => array(
						0 => "item_color",
						1 => "item_size",
						2 => "",
					),
					"OFFERS_SORT_FIELD" => "SORT",
					"OFFERS_SORT_ORDER" => "ASC",
					"OFFERS_LIMIT" => "0",

					"SHOW_INSTOCK" => "Y"
				)
			);

			$specFilter = array("!PROPERTY_models_hit" => false);
			$APPLICATION->IncludeComponent(
				"fashion:catalog.section",
				"index",
				Array(
					"TITLE" => GetMessage("DVS_SPECIAL"),
					"BY_LINK" => "Y",
					"IBLOCK_TYPE" => "catalog",
					"IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#",
					"ELEMENT_SORT_FIELD" => "RAND",
					"ELEMENT_SORT_ORDER" => "DESC",
					"PROPERTY_CODE" => array(
						0 => "models_hit",
						1 => "models_new",
						2 => "models_rating",
						3 => "",
					),
					"META_KEYWORDS" => "-",
					"META_DESCRIPTION" => "-",
					"BROWSER_TITLE" => "-",
					"INCLUDE_SUBSECTIONS" => "Y",
					"BASKET_URL" => SITE_DIR."personal/basket.php",
					"ACTION_VARIABLE" => "action",
					"PRODUCT_ID_VARIABLE" => "id",
					"SECTION_ID_VARIABLE" => "SECTION_ID",
					"FILTER_NAME" => "specFilter",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"DISPLAY_COMPARE" => "N",
					"PAGE_ELEMENT_COUNT" => "4",
					"LINE_ELEMENT_COUNT" => "4",
					"PRICE_CODE" => array(
						0 => "BASE",
					),
					"USE_PRICE_COUNT" => "N",
					"SHOW_PRICE_COUNT" => "1",
					"ADD_SECTIONS_CHAIN" => "N",
					"PRICE_VAT_INCLUDE" => "Y",

					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"PAGER_TITLE" => "title",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => "",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",

					"OFFERS_CART_PROPERTIES" => array(
						0 => "item_color",
						1 => "item_size",
					),
					"OFFERS_FIELD_CODE" => array(),
					"OFFERS_PROPERTY_CODE" => array(
						0 => "item_color",
						1 => "item_size",
						2 => "",
					),
					"OFFERS_SORT_FIELD" => "SORT",
					"OFFERS_SORT_ORDER" => "ASC",
					"OFFERS_LIMIT" => "0",

					"SHOW_INSTOCK" => "Y"
				)
			);

			?>
			<div class="banner-home">
			<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/shipping.php",
					"EDIT_TEMPLATE" => ""
				),
				false
			);?>
			</div>
			<div class="title">
				<h1><?$APPLICATION->ShowTitle(false);?><?$APPLICATION->ShowProperty("ADDITIONAL_TITLE", "");?></h1>
			</div>
			
			<p><?=GetMessage("TEXT1");?></p>
			<p><?=GetMessage("TEXT2");?></p>
			<?

			$APPLICATION->IncludeComponent("fashion:brands.list", ".default", array(
				"MODELS_IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#",
				"BRANDS_IBLOCK_ID" => "#CATALOG_BRANDS_IBLOCK_ID#",
				"PROPERTY_BRAND" => "fil_models_brand",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "N",
				"CACHE_GROUPS" => "Y",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "N",
				"PAGER_TITLE" => "title",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => "catalog",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"PAGE_ELEMENT_COUNT" => "4",
				"ONLY_WITH_IMAGE" => "Y"
			));
		?>

		</div>