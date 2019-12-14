<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

use \Bitrix\Main\Application;
use \Bitrix\Main\Page\Asset;

$Asset = Asset::getInstance();

// $Asset->addJs(SITE_TEMPLATE_PATH.'/assets/js/owl.carousel/owl.carousel.min.js');
// $Asset->addCss(SITE_TEMPLATE_PATH.'/assets/js/owl.carousel/owl.carousel.css');

/*
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/template_ext/catalog.section/catalog/script.js');
*/
if ($arParams['POPUP_DETAIL_VARIABLE'] == 'ON_IMAGE' || $arParams['POPUP_DETAIL_VARIABLE'] == 'ON_LUPA') {
    $Asset->addJs(SITE_TEMPLATE_PATH.'/components/bitrix/catalog.element/catalog/script.js');
}

$Asset->addCss(SITE_TEMPLATE_PATH.'/components/bitrix/catalog.element/catalog/style.css');

$Asset->addJs(SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/script.js');
$Asset->addCss(SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/style.css');


$request = Application::getInstance()->getContext()->getRequest();

if (
    $request->get('rs_ajax') == 'Y' &&
    $request->get('ajax_id') == $arParams['TEMPLATE_AJAXID']
) {
    $APPLICATION->restartBuffer();

    if ($request->get('ajax_type') == 'pages') {
        echo $templateData['TEMPLATE_ITEMS'];
    } elseif ($request->get('ajax_filter')) {
        $arJson = array(
            $arParams['TEMPLATE_AJAXID'] => $templateData['TEMPLATE_HTML'],
        );
        echo CUtil::PhpToJSObject($arJson, false, false, true);
    } else {
        $arJson = array(
            $arParams['TEMPLATE_AJAXID'].'_items' => $templateData['TEMPLATE_ITEMS'],
            $arParams['TEMPLATE_AJAXID'].'_sorter' => $APPLICATION->GetViewContent($arParams['TEMPLATE_AJAXID'] . '_sorter'),
            $arParams['TEMPLATE_AJAXID'].'_pager' => $APPLICATION->GetViewContent('catalog_pager'),
            $arParams['TEMPLATE_AJAXID'].'_filterin' => $APPLICATION->GetViewContent('catalog_filterin')
        );
        echo CUtil::PhpToJSObject($arJson, false, false, true);
    }
    die();
}