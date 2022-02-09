<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/*Подключение модуля информационных блоков*/
if (!CModule::IncludeModule("iblock"))
    return;

if (!$arParams["IBLOCK_ID"]) {$arParams["IBLOCK_ID"] = 3; }

$obCache = new CPHPCache;
$CACHE_ID = "TECH_AVAIL" . $arParams["IBLOCK_ID"] . implode('-', $arParams["ID"]);

if($obCache->InitCache($arParams["CACHE_TIME"], $CACHE_ID, "/")) {
    $cache = $obCache->GetVars();
    $arResult = $cache["arResult"];
}
else {
    $obCache->StartDataCache();
    $filter = array(
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ACTIVE" => "Y"
        );
    if ($arParams["ID"]) {
        $filter["ID"] = $arParams["ID"];
    }
    $rsItems = \CIBlockElement::GetList(false, $filter, false, false, false );
    
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
        if ($item["PREVIEW_PICTURE"]) {
            $item["PREVIEW_PICTURE"] = \imageResize(array("WIDTH" => "170", "HEIGHT" => "130", "MODE" => "cut"), \CFile::GetPath($item["PREVIEW_PICTURE"]));
        }
        $props = $objItem->GetProperties();
        $arResult["ITEMS"][$item["ID"]] = array(
            "ID" => $item["ID"],
            "NAME" => $item["NAME"], 
            "URL" => $item["DETAIL_PAGE_URL"],
            "TYPE" => $props["TYPE"]["VALUE"],
            "PRODUCER" => $props["PRODUCER"]["VALUE"],
            "MODEL" => $props["MODEL"]["VALUE"],
            "YEAR" => $props["YEAR"]["VALUE"],
            "OPERATIONS" => $props["OPERATIONS"]["VALUE"],
            "PRICE_NEW" => TMPriceFormat($props["PRICE_NEW"]["VALUE"]),
            "PRICE_OLD" => TMPriceFormat($props["PRICE_OLD"]["VALUE"]),
            "DISCOUNT_END_DATE" => $props["DISCOUNT_END_DATE"]["VALUE"],
            "DISCOUNT" => $props["DISCOUNT"]["VALUE"],
            "IS_BU" => $props["IS_BU"]["VALUE"],
            "PLACE" => $props["PLACE"]["VALUE"],
            "PICTURE" => array(
                "THUMB" => \imageResize(array("WIDTH" => "170", "HEIGHT" => "130", "MODE" => "cut"), \CFile::GetPath($item["PREVIEW_PICTURE"])),
                "BIG" => CFile::GetPath($item["PREVIEW_PICTURE"])
                )
            );
    }

    $obCache->EndDataCache(array("arResult" => $arResult));
}

$this->IncludeComponentTemplate();