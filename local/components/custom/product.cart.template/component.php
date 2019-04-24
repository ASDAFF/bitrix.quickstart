<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */


$arResult = $arParams['RESULT'];
$arResult["SKU_PROPS"] = $arParams['SKU_PROPS'];
$arParams = $arParams['PARAMS'];

$res = CUser::GetByID($USER->GetID());
$arResult['USER'] = $res->Fetch();

foreach($arResult["OFFERS"] as $offer) {
    if ($offer["MIN_PRICE"]["DISCOUNT_VALUE"] < $arResult["MIN_PRICE"]["DISCOUNT_VALUE"]) {
        $arResult["MIN_PRICE"] = $offer["MIN_PRICE"];
    }
}

$this->includeComponentTemplate();
