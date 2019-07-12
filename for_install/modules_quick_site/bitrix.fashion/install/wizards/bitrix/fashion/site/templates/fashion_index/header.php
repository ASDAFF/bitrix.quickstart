<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- <?=GetMessage("DVS_COP")?> -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
    <?$APPLICATION->ShowHead();?>
    <link href="<?=SITE_TEMPLATE_PATH?>/color.css" type="text/css" rel="stylesheet" />
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery-1.7.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/fashion.js"></script>

    <!--[if lt IE 9]>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->
</head>

<body class="promo">
<div id="panel"><?$APPLICATION->ShowPanel();?></div>

<div style="position:relative;height:100%;width:100%;">

<div id="wrapper">

    <?$APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "promo",
        Array(
            "DISPLAY_DATE" => "N",
            "DISPLAY_NAME" => "N",
            "DISPLAY_PICTURE" => "N",
            "DISPLAY_PREVIEW_TEXT" => "N",
            "AJAX_MODE" => "N",
            "IBLOCK_TYPE" => "content",
            "IBLOCK_ID" => "#PROMO_IBLOCK_ID#",
            "NEWS_COUNT" => "6",
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "ACTIVE_FROM",
            "SORT_ORDER2" => "ASC",
            "FILTER_NAME" => "",
            "FIELD_CODE" => array("DETAIL_PICTURE"),
            "PROPERTY_CODE" => array("products"),
            "CHECK_DATES" => "N",
            "DETAIL_URL" => "",
            "PREVIEW_TRUNCATE_LEN" => "",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "SET_TITLE" => "N",
            "SET_STATUS_404" => "N",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "N",
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
            "PARENT_SECTION" => "",
            "PARENT_SECTION_CODE" => "",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "PAGER_TITLE" => "",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => "",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N"
        ),
    false
    );?>

    <div id="header" itemscope itemtype = "http://schema.org/LocalBusiness">
        <div class="title vcard">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?>
            <abbr itemprop = "name" class="category"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?></abbr>
        </div><!-- .logo -->

        <div class="user-links">
            <?$APPLICATION->IncludeComponent(
                "bitrix:sale.basket.basket.small",
                "",
                Array(
                    "PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",
                    "PATH_TO_ORDER" => "#SITE_DIR#personal/order/"
                ),
            false
            );?>
            <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "", array(
                "REGISTER_URL" => "#SITE_DIR#auth/",
                "PROFILE_URL" => "#SITE_DIR#personal/",
                "SHOW_ERRORS" => "N"
                ),
                false,
                Array()
            );?>
        </div>

        <div class="contacts vcard">
            <abbr itemprop = "telephone" class="tel"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></abbr>
            <span itemprop = "openingHours" class="workhours"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?></span>
        </div>
    </div><!-- #header -->

    <div id="content">
		<div class="categories-link"><a href="<?=SITE_DIR?>catalog/"><?=GetMessage("DVS_ALL_SECTIONS")?></a></div>

		<?$APPLICATION->IncludeComponent("bitrix:menu", "index", array(
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
		);
		
		$APPLICATION->IncludeComponent("bitrix:menu", "all", array(
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