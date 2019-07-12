<?php

use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

$request = Application::getInstance()->getContext()->getRequest();

if (
    $request->get('rs_ajax') == 'Y' &&
    $request->get('action') == 'add2favorite' &&
    intval($request->get('element_id')) > 0
) {
    $result = RSFavoriteAddDel($request->get('element_id'));
    $arParams['LIKES_COUNT_PROP'] = Option::get('redsign.activelife', 'propcode_likes', 'LIKES_COUNT');
    
    $res = CIBlockElement::GetList(
        array(),
        array(
            'ID' => $request->get('element_id')
        ),
        false,
        false,
        array(
            'ID',
            'PROPERTY_'.$arParams['LIKES_COUNT_PROP']
        )
    );
    if ($arElement = $res->GetNext()) {
        $iElementCount = intval($arElement['PROPERTY_'.$arParams['LIKES_COUNT_PROP'].'_VALUE'] > 0 )
            ? $arElement['PROPERTY_'.$arParams['LIKES_COUNT_PROP'].'_VALUE']
            : 0 ;
    }
    
    switch ($result) {
        case 2:
            $JSON = array(
                'STATUS' => 'OK',
                'ACTION' => 'ADD',
                'TOTAL' => ++$arResult['COUNT'], //?????????????
                'LIKES_COUNT' => ++$iElementCount
            );
            if ($arElement) {
                
            }
            break;
        case 1:
            $JSON = array(
                'STATUS' => 'OK',
                'ACTION' => 'REMOVE',
                'TOTAL' => --$arResult['COUNT'], //?????????????
                'LIKES_COUNT' => --$iElementCount
            );
            break;
        default:
            $JSON = array(
                'STATUS' => 'ERROR'
            );
    }
    
    if ($arElement) {
        CIBlockElement::SetPropertyValueCode(
            $arElement['ID'],
            $arParams['LIKES_COUNT_PROP'],
            $iElementCount
        );
    }

    $APPLICATION->RestartBuffer();
    if ('utf-8' != SITE_CHARSET) {
        echo $APPLICATION->ConvertCharset(
            json_encode(
                $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8')
            )
        , 'utf-8', SITE_CHARSET);
    } else {
        echo json_encode($JSON);
    }
    die();
}

global $rsFavoriteElements;
$rsFavoriteElements = array();
if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {
    foreach ($arResult['ITEMS'] as $arItem) {
        $rsFavoriteElements[] = $arItem['ELEMENT_ID'];
    }
}