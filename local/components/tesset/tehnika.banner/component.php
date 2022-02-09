<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/*Подключение модуля информационных блоков*/
if (!CModule::IncludeModule("iblock"))
    return;

if (!$arParams["IBLOCK_ID"]) {$arParams["IBLOCK_ID"] = 4; }

$obCache = new CPHPCache;
$CACHE_ID = "TEHNIKA_IN_STORE" . $arParams["IBLOCK_ID"];

if($obCache->InitCache($arParams["CACHE_TIME"], $CACHE_ID, "/")) {
    $cache = $obCache->GetVars();
    $arResult = $cache["arResult"];
}
else {
    $obCache->StartDataCache();
    $filter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "!PROPERTY_ON_MAIN" => false);
    $rsItems = \CIBlockElement::GetList(false, $filter, false, false, false);
    
    while ($objItem = $rsItems->GetNextElement()) {
        $item = $objItem->GetFields();
        $arButtons = CIBlock::GetPanelButtons(
            $item["IBLOCK_ID"],
            $item["ID"],
            0,
            array("SECTION_BUTTONS"=>false, "SESSID"=>false)
        );
        $item["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
        $item["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
        $props = $objItem->GetProperties();
        $arResult["ITEMS"][$item["ID"]] = array(
            "ID" => $item["ID"],
            "DETAIL_PAGE_URL" => $item["DETAIL_PAGE_URL"],
            "NAME" => $item["NAME"],
            "PRICE_OLD" => TMPriceFormat($props["PRICE_OLD"]["VALUE"]),
            "PRICE_NEW" => TMPriceFormat($props["PRICE_NEW"]["VALUE"]),
            "DISCOUNT_END_DATE" => $props["DISCOUNT_END_DATE"]["VALUE"],
            "DISCOUNT" => $props["DISCOUNT"]["VALUE"],
            "CODE" => $item["CODE"],
            "PICTURE" => \imageResize(array("WIDTH" => "175", "HEIGHT" => "115", "MODE" => "cut"), \CFile::GetPath($props["PICTURES"]["VALUE"][0]))
            );
    }

    $obCache->EndDataCache(array("arResult" => $arResult));
}

$this->IncludeComponentTemplate();