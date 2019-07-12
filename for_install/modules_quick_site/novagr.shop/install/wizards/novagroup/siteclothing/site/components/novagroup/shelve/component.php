<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CModule::IncludeModule("sale");
$dbBasketItems = CSaleBasket::GetList(
    array(
        "NAME" => "ASC",
        "ID" => "ASC"
    ),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL",
        "DELAY" => "Y"
    ),
    false,
    false,
    array("ID", "PRODUCT_ID")
);

$arResult["SHELVED_ITEMS"] = array();
while ($arItems = $dbBasketItems->Fetch())
{
    $arResult["SHELVED_ITEMS"][] = $arItems["PRODUCT_ID"];
}
/**
 * @var CBitrixComponent $this
 */
$this->IncludeComponentTemplate();
?>