<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . SITE_TEMPLATE_ID . "/header.php");
$wizTemplateId = COption::GetOptionString("main", "wizard_template_id", "eshop_adapt_horizontal", SITE_ID);
CUtil::InitJSCore();
CJSCore::Init(array("fx"));
$curPage = $APPLICATION->GetCurPage(true);
if (CSite::InDir(SITE_DIR . '/index.php')) {
    $isFrontPage = true;
}
if (CSite::InDir(SITE_DIR . 'about/')) {
    $isTextPage = true;
}
?>
<!DOCTYPE html>
<html><head>
        <title><? $APPLICATION->ShowTitle() ?></title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <link rel="shortcut icon" type="image/x-icon" href="<?= SITE_DIR ?>/favicon.ico" />
        <?
        //$APPLICATION->ShowHead();
        echo '<meta http-equiv="Content-Type" content="text/html; charset=' . LANG_CHARSET . '"' . (true ? ' /' : '') . '>' . "\n";
        $APPLICATION->ShowMeta("robots", false, true);
        $APPLICATION->ShowMeta("keywords", false, true);
        $APPLICATION->ShowMeta("description", false, true);
        $APPLICATION->ShowCSS(true, true);
        ?>
        <link rel="stylesheet" type="text/css" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . "/style.css") ?>" />
        <?
        $APPLICATION->ShowHeadStrings();
        $APPLICATION->ShowHeadScripts();
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/script.js");
        ?>
        <title><? $APPLICATION->ShowTitle() ?></title>
        <link rel="stylesheet" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . "/css/cssreset-min.css") ?>" media="all" type="text/css">
        <script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/script.js"></script>
        <script type="text/javascript">
            $(function() {
                $('#b-item__main-image__img').elevateZoom({gallery: 'item__photo__gallery', cursor: 'pointer', galleryActiveClass: "active", imageCrossfade: true, responsive: true, zoomWindowFadeIn: 200, zoomWindowFadeOut: 200, zoomWindowWidth: 520, zoomWindowHeight: 470, zoomType: "lens", containLensZoom: true, });
            })
        </script>
    </head>
    <body class="b-body<? if ($isFrontPage): ?> b-body_main<? endif; ?>">
        <? $APPLICATION->ShowPanel(); ?>
        <div class="b-page">
            <div class="b-page__wrapper">
                <header class="b-header">
                    <div class="b-header__menu-wrapper">
                        <div class="b-header__logo"><a href="<?= SITE_DIR ?>">
                                <?
                                $APPLICATION->IncludeFile(
                                        SITE_DIR . "include/company_logo.php", Array(), Array("MODE" => "html")
                                );
                                ?></a></div>
                        <div class="b-header__description">
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company_name.php"), false); ?><br>
                            <nobr><? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company_support.php"), false); ?></nobr>
                        </div>
                        <ul class="b-main-menu">
                            <li class="b-main-menu__item b-main-menu__item_type_search">
                                <?
                                $APPLICATION->IncludeComponent("bitrix:search.title", "visual", array(
                                    "NUM_CATEGORIES" => "1",
                                    "TOP_COUNT" => "5",
                                    "CHECK_DATES" => "N",
                                    "SHOW_OTHERS" => "N",
                                    "PAGE" => SITE_DIR . "catalog/",
                                    "CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
                                    "CATEGORY_0" => array(
                                        0 => "iblock_catalog",
                                    ),
                                    "CATEGORY_0_iblock_catalog" => array(
                                        0 => "all",
                                    ),
                                    "CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
                                    "SHOW_INPUT" => "Y",
                                    "INPUT_ID" => "title-search-input",
                                    "CONTAINER_ID" => "search",
                                    "PRICE_CODE" => array(
                                        0 => "BASE",
                                    ),
                                    "SHOW_PREVIEW" => "Y",
                                    "PREVIEW_WIDTH" => "75",
                                    "PREVIEW_HEIGHT" => "75",
                                    "CONVERT_CURRENCY" => "Y"
                                        ), false
                                );
                                ?>
                            </li>


                            <?
                            $APPLICATION->IncludeComponent("bitrix:system.auth.form", "", array(
                                "REGISTER_URL" => SITE_DIR . "login/",
                                "PROFILE_URL" => SITE_DIR . "personal/",
                                "SHOW_ERRORS" => "N"
                                    ), false, Array()
                            );
                            ?>


                            <li class="b-main-menu__item b-main-menu__item_type_cart" id="cartNav">
                                <?
                                $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "eshop_adapt", array(
                                    "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
                                    "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                                    "SHOW_PERSONAL_LINK" => "N"
                                        ), false, array()
                                );
                                ?>
                            </li>
                        </ul>
                    </div>


                    <?
                    $APPLICATION->IncludeComponent("bitrix:menu", "catalog_native", array(
                        "ROOT_MENU_TYPE" => "left",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "36000000",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_THEME" => "site",
                        "CACHE_SELECTED_ITEMS" => "N",
                        "MENU_CACHE_GET_VARS" => array(
                        ),
                        "MAX_LEVEL" => "3",
                        "CHILD_MENU_TYPE" => "left",
                        "USE_EXT" => "Y",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N",
                            ), false
                    );
                    ?>

                    <script type="text/javascript">

                        if (!$) {
                            $ = jQuery;
                        }
                        $('#categories-menu > li').mouseover(function() {
                            $(this).find('ul.categories-level1').show();
                        }).mouseout(function() {
                            $(this).find('ul.categories-level1').hide();
                        })
                    </script>


                </header>
                <section class="b-content ">
                    <? if (!$isFrontPage): ?>
                        <?
                        $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array(
                            "START_FROM" => "1",
                            "PATH" => "",
                            "SITE_ID" => "-"
                                ), false, Array('HIDE_ICONS' => 'Y')
                        );
                        ?>
                    <? endif; ?>
                    <? if ($isTextPage): ?>
                        <div class="b-textpage__content">
                        <? endif; ?>