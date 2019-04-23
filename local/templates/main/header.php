<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @var CUser $USER
 * @var CMain $APPLICATION
 * @var $full_width - переменная в которой будет true если выполнится условие из  $arFullWidthPages
 */
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
    <meta charset="<?= SITE_CHARSET ?>">
    <title><? $APPLICATION->ShowTitle() ?></title>
    <?
    //Тут мета-теги
    $APPLICATION->ShowMeta("robots", false, false);
    $APPLICATION->ShowMeta("keywords", false, false);
    $APPLICATION->ShowMeta("description", false, false);
    ?>
    <meta name="yandex-verification" content="xxxxxxxxxxxxxxxx">
    <meta name="google-site-verification" content="xxxxxxxxxxxxxxxx">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <?
    //$APPLICATION->ShowHead();

    //Тут канонический url
    $APPLICATION->ShowLink("canonical", null, false);

    //Тут стили шаблона сайта
    // Bootstrap core CSS
    //Asset::getInstance()->addCss(PATH_TEMPLATE_CSS . 'bootstrap.min.css');
    $APPLICATION->SetAdditionalCSS(PATH_TEMPLATE_CSS . 'bootstrap.min.css');

    // Animate.css
    //Asset::getInstance()->addCss(PATH_TEMPLATE_CSS . 'animate.min.css');
    $APPLICATION->SetAdditionalCSS(PATH_TEMPLATE_CSS . 'animate.min.css');

    // FancyBox CSS
    $APPLICATION->SetAdditionalCSS(PATH_TEMPLATE_CSS . 'jquery.fancybox.min.css');

    // Owl Carousel
    $APPLICATION->SetAdditionalCSS(PATH_TEMPLATE_CSS . 'owl.carousel.min.css');

    // Custom styles for this template
    $APPLICATION->SetAdditionalCSS(PATH_TEMPLATE_CSS . 'global.css');
    $APPLICATION->SetAdditionalCSS(PATH_TEMPLATE_CSS . 'responsive.css');

    //Тут выводим стили
    $APPLICATION->ShowCSS(true, false);
    ?>
    <!--[if lt IE 9]>
    <script type="text/javascript"
            src="<?=CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH.'/js/ie8-polyfill.js');?>"></script>


    <![endif]-->
    <script type="text/javascript"
            src="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/js/jquery-1.11.2_min.js'); ?>"></script>
    <?
    //Это встроенная в ядро Битрикс jQuery, если подключать ее, то строку подключения jQuery 1.11.2 выше надо удалить.
    //CJSCore::Init(array('jquery'));

    //Тут скрипты
    //Asset::getInstance()->addJs(PATH_TEMPLATE_JS . 'modernizr.js');
    //Asset::getInstance()->addJs(PATH_TEMPLATE_JS . 'bootstrap.min.js');
    //Asset::getInstance()->addJs(PATH_TEMPLATE_JS . 'owl.carousel.min.js');

    $APPLICATION->AddHeadScript(PATH_TEMPLATE_JS . 'modernizr.js');
    $APPLICATION->AddHeadScript(PATH_TEMPLATE_JS . 'bootstrap.min.js');
    $APPLICATION->AddHeadScript(PATH_TEMPLATE_JS . 'owl.carousel.min.js');
    $APPLICATION->AddHeadScript(PATH_TEMPLATE_JS . 'jquery.appear.js');
    $APPLICATION->AddHeadScript(PATH_TEMPLATE_JS . 'jquery.fancybox.min.js');
    $APPLICATION->AddHeadScript(PATH_TEMPLATE_JS . 'global.js');

    //Asset::getInstance()->addString("<meta name='viewport' content='width=device-width, initial-scale=1'>");

    //Тут выводим скрипты
    $APPLICATION->ShowHeadStrings();
    $APPLICATION->ShowHeadScripts();
    ?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>