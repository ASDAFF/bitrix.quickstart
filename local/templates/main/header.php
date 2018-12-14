<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * @var CUser $USER
 * @var CMain $APPLICATION
 * @var $full_width - переменная в которой будет true если выполнится условие из  $arFullWidthPages
 */
?><!DOCTYPE html>
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
    //Тут канонический url
    $APPLICATION->ShowLink("canonical", null, false);

    //Тут стили шаблона сайта
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/style.css');

    //Custom styles for this template
    $APPLICATION->SetAdditionalCSS(PATH_TEMPLATE_CSS . 'bootstrap.min.css');
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
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/uikit/core.min.js');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/uikit/grid.min.js');

    //Тут выводим скрипты
    $APPLICATION->ShowHeadStrings();
    $APPLICATION->ShowHeadScripts();
    ?>
</head>
<body>
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>