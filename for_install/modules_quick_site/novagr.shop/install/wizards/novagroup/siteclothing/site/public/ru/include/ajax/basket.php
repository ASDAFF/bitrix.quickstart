<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}

/*
$APPLICATION->IncludeComponent("novagroup:sale.basket.basket.line", ".default", array(
        "PATH_TO_BASKET" => SITE_DIR."cabinet/cart/",
        "PATH_TO_PERSONAL" => SITE_DIR."cabinet/",
        "SHOW_PERSONAL_LINK" => "N"
    ),
    false,
    Array('')
);*/

$APPLICATION->IncludeComponent("novagroup:top.basket", ".default", array(
        "PATH_TO_ORDER" => SITE_DIR . "cabinet/order/make/",
        "PATH_TO_BASKET" => SITE_DIR."cabinet/cart/",
        "PATH_TO_PERSONAL" => SITE_DIR."cabinet/",
        'CATALOG_IBLOCK_ID' => "#CATALOG_IBLOCK_ID#",
        "OFFERS_IBLOCK_ID" => "#OFFERS_IBLOCK_ID#"
    ),
    false,
    Array('')
);
?>