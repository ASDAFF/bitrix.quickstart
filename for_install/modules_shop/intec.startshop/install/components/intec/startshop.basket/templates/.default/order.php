<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    global $APPLICATION;
?>
<?$APPLICATION->IncludeComponent('intec:startshop.order', '.default', array(
        "USE_ADAPTABILITY" => $arParams['USE_ADAPTABILITY'],
        "CURRENCY" => $arParams['CURRENCY'],
        "REQUEST_VARIABLE_ACTION" => $arParams['REQUEST_VARIABLE_ACTION'],
        "USE_ITEMS_PICTURES" => $arParams['USE_ITEMS_PICTURES'],
        "USE_BUTTON_BASKET" => $arParams['USE_BUTTON_BASKET'],
        "URL_BASKET" => $arResult['URL_BASKET'],
        "URL_ORDER_CREATED" => $arParams['URL_ORDER_CREATED'],
        "URL_ORDER_CREATED_TO_USER" => $arParams['URL_ORDER_CREATED_TO_USER'],
        "URL_BASKET_EMPTY" => $arResult['URL_BASKET_EMPTY']
    ),
    $component
);?>