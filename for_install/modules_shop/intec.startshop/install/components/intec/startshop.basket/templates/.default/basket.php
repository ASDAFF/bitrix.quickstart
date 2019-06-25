<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    global $APPLICATION;
?>
<?$APPLICATION->IncludeComponent('intec:startshop.basket.basket', '.default', array(
        "USE_ADAPTABILITY" => $arParams['USE_ADAPTABILITY'],
        "CURRENCY" => $arParams['CURRENCY'],
        "REQUEST_VARIABLE_ACTION" => $arParams['REQUEST_VARIABLE_ACTION'],
        "REQUEST_VARIABLE_QUANTITY" => $arParams['REQUEST_VARIABLE_QUANTITY'],
        "REQUEST_VARIABLE_ITEM" => $arParams['REQUEST_VARIABLE_ITEM'],
        "USE_ITEMS_PICTURES" => $arParams['USE_ITEMS_PICTURES'],
        "USE_BUTTON_CLEAR" => $arParams['USE_BUTTON_CLEAR'],
        "USE_SUM_FIELD" => $arParams['USE_SUM_FIELD'],
        "USE_BUTTON_ORDER" => "Y",
        "URL_ORDER" => $arResult['URL_ORDER'],
        "URL_BASKET_EMPTY" => $arParams['URL_BASKET_EMPTY'],
		"CFO_USE_FASTORDER" => $arParams['CFO_USE_FASTORDER'],
		"CFO_PROP_NAME" => $arParams['CFO_PROP_NAME'],
		"CFO_PROP_PHONE" => $arParams['CFO_PROP_PHONE'],
		"CFO_PROP_COMMENT" => $arParams['CFO_PROP_COMMENT']
    ),
    $component
);?>
