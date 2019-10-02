<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?=$arResult["NAVIGATION"];?>

<?$APPLICATION->IncludeComponent(
"bitrix:sale.viewed.product",
    "",
    Array(
        "VIEWED_COUNT" => "5",
        "VIEWED_NAME" => "Y",
        "VIEWED_IMAGE" => "Y",
        "VIEWED_PRICE" => "Y",
        "VIEWED_CURRENCY" => "default",
        "VIEWED_CANBUY" => "Y",
        "VIEWED_CANBASKET" => "Y",
        "VIEWED_IMG_HEIGHT" => "150",
        "VIEWED_IMG_WIDTH" => "150",
        "BASKET_URL" => "/personal/basket/",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "SET_TITLE" => "Y"
    )
);?>