<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arDetParams = array(
    "PATH_TO_LIST" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['orders'],
    "PATH_TO_CANCEL" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['cancel'],
    "PATH_TO_PAYMENT" =>  SITE_DIR."cabinet/order/payment/",
    "SET_TITLE" => "N",
    "ID" => $arResult["VARIABLES"]["ID"],
);
foreach($arParams as $key => $val)
{
    if(strpos($key, "PROP_") !== false)
        $arDetParams[$key] = $val;
}
$APPLICATION->IncludeComponent(
    "bitrix:sale.personal.order.detail",
    "demoshop",
    $arDetParams,
    $component
);
?>
