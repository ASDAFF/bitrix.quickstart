<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
    <? $APPLICATION->ShowHead(); ?>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= SITE_TEMPLATE_PATH ?>/favicon.ico"/>
    <? CUtil::InitJSCore(array('jquery', 'window', 'popup', 'ajax', 'date')); ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/bootstrap.min.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/jquery.fancybox.css');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/vendor/jquery-2.2.4.min.js');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/vendor/js/bootstrap.min.js');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/vendor/jquery.fancybox.min.js');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/scripts.min.js');
    ?>
</head>
<body>
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<div class="page-wrapper">
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-3">
                    <? $APPLICATION->IncludeFile(SITE_DIR . "include/logo.php", array(), array(
                            "MODE" => "php",
                            "NAME" => "logo",
                        )
                    ); ?>
                </div>
                <div class="col-md-6 hidden-xs hidden-sm">
                    <?
                    $APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
                            "ROOT_MENU_TYPE" => "top",
                            "MAX_LEVEL" => "1",
                            "CHILD_MENU_TYPE" => "",
                            "USE_EXT" => "Y",
                            "DELAY" => "N",
                            "ALLOW_MULTI_SELECT" => "N",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "3600000",
                            "MENU_CACHE_USE_GROUPS" => "N",
                            "MENU_CACHE_GET_VARS" => ""
                        )
                    );
                    ?>
                </div>
                <div class="col-xs-8 col-sm-8 col-md-3">
                    <?
                    $APPLICATION->IncludeComponent(
                        "msnet:variable.set",
                        "soc_header",
                        array(
                            "COMPONENT_TEMPLATE" => "soc_header",
                            "LINK_VK" => "https://vk.com/nervyofficial",
                            "LINK_INSTAGRAM" => "https://www.instagram.com/nervy_official/",
                            "LINK_YOUTUBE" => "https://www.youtube.com/channel/UC1bb2kw4IZwbUNHC1LeG4Zg"
                        ),
                        false
                    );
                    ?>
                </div>
            </div>
        </div>
    </header>