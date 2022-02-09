<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/*Подключение модуля информационных блоков*/
if (!CModule::IncludeModule("iblock"))
    return;

if (!$arParams["IBLOCK_ID"]) {$arParams["IBLOCK_ID"] = array(4, 6); }

$obCache = new CPHPCache;
$CACHE_ID = "TECH_AVAIL_DETAIL" . $arParams["IBLOCK_ID"] . implode('-', $arParams["ID"]) . $arParams["CODE"];
if (false) {
// if($obCache->InitCache($arParams["CACHE_TIME"], $CACHE_ID, "/")) {
    $cache = $obCache->GetVars();
    $arResult = $cache["arResult"];
}
else {
    $obCache->StartDataCache();
    $filter = array(
            "ACTIVE" => "Y",
            "CODE" => $arParams["CODE"]
        );
    $rsItems = \CIBlockElement::GetList(false, $filter, false, false, false );
    
    if ($objItem = $rsItems->GetNextElement()) {
        $item = $objItem->GetFields();
        $arButtons = CIBlock::GetPanelButtons(
            $item["IBLOCK_ID"],
            $item["ID"],
            0,
            array("SECTION_BUTTONS"=>false, "SESSID"=>false)
        );
        $props = $objItem->GetProperties();
        foreach ($props["PICTURES"]["VALUE"] as $id) {
            $pictures[] = array(
                "BIG" => \CFile::GetPath($id),
                "THUMB" => \imageResize(array("WIDTH" => "170", "HEIGHT" => "130", "MODE" => "cut"), \CFile::GetPath($id))
                );
        }
        foreach ($props as $code => $prop) {
            if ($prop["VALUE"] && !is_array($prop["VALUE"])) {
                $arResult["PROPS"][$code] = $prop["NAME"];
            }
        }
        $arResult["ITEM"] = array(
            "ID" => $item["ID"],
            "IBLOCK_ID" => $item["IBLOCK_ID"],
            "EDIT_LINK" => $arButtons["edit"]["edit_element"]["ACTION_URL"],
            "DELETE_LINK" => $arButtons["edit"]["delete_element"]["ACTION_URL"],
            "NAME" => $item["NAME"], 
            "DETAIL_TEXT" => $item["DETAIL_TEXT"], 
            "PREVIEW_TEXT" => $item["PREVIEW_TEXT"], 
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
                ),
            "PICTURES" => $pictures
            );
    }

    $obCache->EndDataCache(array("arResult" => $arResult));
}

$this->IncludeComponentTemplate();