<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if (!Loader::includeModule('iblock')) {
    return;
}

if (!Loader::includeModule('redsign.devfunc')) {
    return;
}

if (!Loader::includeModule('redsign.flyaway')) {
    return;
}
$bannerType = RSFlyaway::getSettings('bannerType', 'type1');
$sideBannerL = "";
$sideBannerR = "";
$mainBanner = "";
$arResult['SELECTED_SIDEBANNERS'] = array();

switch ($bannerType) {
    case 'type1':
        $mainBanner = "__center";
        break;
    case 'type2':
        $mainBanner  = "__wide";
        break;
    case 'type3':
        $sideBannerL = "__leftbanner";
        $sideBannerR = "__rightbanner";
        $mainBanner = "__center";
        $arResult['SELECTED_SIDEBANNERS'] = array("left", "right");
        break;
    case 'type4':
        $sideBannerL = "__leftbanner";
        $mainBanner = "__center";
        $arResult['SELECTED_SIDEBANNERS'][] = "left";
        break;
    case 'type5':
        $sideBannerR = "__rightbanner";
        $mainBanner = "__center";
        $arResult['SELECTED_SIDEBANNERS'][] = "right";
        break;
    
    default:
        $mainBanner  = "__wide";
        break;
}
$arResult['BANNER_CLASS'] = $sideBannerL." ".$sideBannerR." ".$mainBanner;

$titleProperty = (!empty($arParams['RS_TITLE_PROPERTY'])) ? $arParams['RS_TITLE_PROPERTY'] : 'TITLE';
$descProperty = (!empty($arParams['RS_DESC_PROPERTY'])) ? $arParams['RS_DESC_PROPERTY'] : 'DESCRIPTION';
$priceProperty = (!empty($arParams['RS_PRICE_PROPERTY'])) ? $arParams['RS_PRICE_PROPERTY'] : 'PRICE';
$backgroundProperty = (!empty($arParams['RS_BACKGROUND_PROPERTY'])) ? $arParams['RS_BACKGROUND_PROPERTY'] : 'BACKGROUND';
$imgProperty = (!empty($arParams['RS_IMG_PROPERTY'])) ? $arParams['RS_IMG_PROPERTY'] : 'PRODUCT_IMAGE';
$buttonTextProperty = (!empty($arParams['RS_BUTTON_TEXT_PROPERTY'])) ? $arParams['RS_BUTTON_TEXT_PROPERTY'] : 'BUTTON_TEXT';
$buttonBackgroundColorProperty = (!empty($arParams['RS_BUTTON_BACKGROUND_COLOR_PROPERTY'])) ? $arParams['RS_BUTTON_BACKGROUND_COLOR_PROPERTY'] : 'BUTTON_BACKGROUND_COLOR';
$buttonTextColorProperty = (!empty($arParams['RS_BUTTON_TEXT_COLOR_PROPERTY'])) ? $arParams['RS_BUTTON_TEXT_COLOR_PROPERTY'] : 'BUTTON_TEXT_COLOR';
$linkProperty = (!empty($arParams['RS_LINK_PROPERTY'])) ? $arParams['RS_LINK_PROPERTY'] : 'LINK';
$videoLinkProperty = (!empty($arParams['RS_VIDEO_LINK_PROPERTY'])) ? $arParams['RS_VIDEO_LINK_PROPERTY'] : 'VIDEO_LINK';
$videoFileProperty = (!empty($arParams['RS_VIDEO_LINK_PROPERTY'])) ? $arParams['RS_VIDEO_LINK_PROPERTY'] : 'VIDEO_LINK';

$arResult['SLIDER_ADAPTER'] = 'owl';
$arResult['BANNER_HEIGHT'] = (!empty($arParams['RS_BANNER_HEIGHT'])) ? $arParams['RS_BANNER_HEIGHT'] : '400px';
$arResult['BANNER_TYPE'] = (!empty($arParams['RS_BANNER_TYPE'])) ? $arParams['RS_BANNER_TYPE'] : 'wide';
$arResult['IS_JS_HEIGHT_ADJUST'] = 'N';
$arResult['MARGIN_TOP'] = ($headType == 'type3') ? '7px' : null;

$arResult['BANNER_OPTIONS'] = array(
    "autoplay" =>  ($arParams['RS_BANNER_IS_AUTOPLAY'] == 'N') ? false : true,
    "autoplay-speed" => (!empty($arParams['RS_BANNER_AUTOPLAY_SPEED'])) ? $arParams['RS_BANNER_AUTOPLAY_SPEED'] : 2000,
    "autoplay-timeout" => (!empty($arParams['RS_BANNER_AUTOPLAY_TIMEOUT'])) ? $arParams['RS_BANNER_AUTOPLAY_TIMEOUT'] : 7000,
    "height" => $arResult['BANNER_HEIGHT'],
    "is-auto-adjust-height" => $arResult['IS_JS_HEIGHT_ADJUST'] == 'Y' ? true : false
);

foreach ($arResult['ITEMS'] as &$arItem) {
    
    $arItem['BACKGROUND'] = !empty($arItem['PROPERTIES'][$backgroundProperty]['VALUE']) ?
                                CFile::GetPath($arItem['PROPERTIES'][$backgroundProperty]['VALUE']) : null;
                                
    $arItem['PRODUCT_IMG'] = !empty($arItem['PROPERTIES'][$imgProperty]['VALUE']) ?
                                CFile::GetPath($arItem['PROPERTIES'][$imgProperty]['VALUE']) : null;
                                
    $arItem['PRODUCT_TITLE'] = $arItem['DISPLAY_PROPERTIES'][$titleProperty]['DISPLAY_VALUE'];
    $arItem['PRODUCT_PRICE'] = $arItem['DISPLAY_PROPERTIES'][$priceProperty]['DISPLAY_VALUE'];
    $arItem['PRODUCT_DESC'] = $arItem['DISPLAY_PROPERTIES'][$descProperty]['DISPLAY_VALUE'];
    $arItem['PRODUCT_LINK'] = $arItem['PROPERTIES'][$linkProperty]['VALUE'];
                                
    $arItem['PRODUCT_BUTTON_TEXT'] = $arItem['DISPLAY_PROPERTIES'][$buttonTextProperty]['DISPLAY_VALUE'];
    $arItem['PRODUCT_BUTTON_BACKGROUND_COLOR'] = $arItem['DISPLAY_PROPERTIES'][$buttonBackgroundColorProperty]['DISPLAY_VALUE']; 
    $arItem['PRODUCT_BUTTON_TEXT_COLOR'] = $arItem['DISPLAY_PROPERTIES'][$buttonTextColorProperty]['DISPLAY_VALUE'];                             
    
    if(!empty($arItem['DISPLAY_PROPERTIES'][$videoLinkProperty]['DISPLAY_VALUE'])) {
        $arItem['VIDEO_URL'] = $arItem['PROPERTIES'][$videoLinkProperty]['VALUE'];
        $arItem['VIDEO_TYPE'] =  "frame";
    }
    elseif(!empty($arItem['DISPLAY_PROPERTIES'][$videoFileProperty]['DISPLAY_VALUE'])) {
        $arItem['VIDEO_URL'] = CFile::GetPath($arItem['PROPERTIES'][$videoFileProperty]['VALUE']);
        $arItem['VIDEO_TYPE'] =  "file";
    }
    else {
        $arItem['VIDEO_TYPE'] =  false;
    }
}
unset($arItem);

if (!empty($arParams['RS_SIDEBANNERS_IBLOCK_ID'])) {
    
    $dbSidebanners = CIBlockElement::GetList(
        array(),
        array(
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arParams['RS_SIDEBANNERS_IBLOCK_ID']
        ),
        false,
        false,
        array(
            "ID", "IBLOCK_ID", "NAME", "PROPERTY_IMAGE", "PROPERTY_HREF", "PROPERTY_PLACE"
        )
    );


    $arResult['SIDEBANNERS'] = array(
        "LEFT" => array(),
        "RIGHT" => array()
    );

    $arSidebannerPlaceProperty = array();
    while ($arSidebanner = $dbSidebanners->Fetch()) {
        
        if (empty($arSidebannerProperties[$arSidebanner["PROPERTY_PLACE_ENUM_ID"]])) {
            $arSidebannerProperties[$arSidebanner["PROPERTY_PLACE_ENUM_ID"]] = CIBlockPropertyEnum::GetByID($arSidebanner["PROPERTY_PLACE_ENUM_ID"]);
        }
        
        $place = $arSidebannerProperties[$arSidebanner["PROPERTY_PLACE_ENUM_ID"]]['XML_ID'];
        
        if (!is_array($arResult['SIDEBANNERS'][$place])) {
            continue;
        }
        
        $arResult['SIDEBANNERS'][$place][] = array(
            "src" => CFile::GetPath($arSidebanner['PROPERTY_IMAGE_VALUE']),
            "link" => $arSidebanner['PROPERTY_HREF_VALUE']
        );
    }
}
