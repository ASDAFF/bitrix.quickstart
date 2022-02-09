<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/*Подключение модуля информационных блоков*/
if (!CModule::IncludeModule("iblock"))
    return;

if (!$arParams["IBLOCK_ID"]) {$arParams["IBLOCK_ID"] = 7; }

$obCache = new CPHPCache;
$CACHE_ID = "CERTIFICATES" . $arParams["IBLOCK_ID"] . implode('-', $arParams["ID"]);

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
    $rsItems = \CIBlockElement::GetList(
        array("SORT" => "ASC"),
        $filter,
        false,
        false,
        array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_LOGO")
    );
    
    while ($item = $rsItems->Fetch()) {
        $arButtons = CIBlock::GetPanelButtons(
            $item["IBLOCK_ID"],
            $item["ID"],
            0,
            array("SECTION_BUTTONS"=>false, "SESSID"=>false)
        );
        $item["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
        $item["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

        if ($item["PREVIEW_PICTURE"]) {
            $item["PREVIEW_PICTURE"] = \CFile::GetPath($item["PREVIEW_PICTURE"]);
        }
        if ($item["DETAIL_PICTURE"]) {
            $item["DETAIL_PICTURE"] = \CFile::GetPath($item["DETAIL_PICTURE"]);
        }
        $item["LOGO"] = \CFile::GetPath($item["PROPERTY_LOGO_VALUE"]);
        $arResult["ITEMS"][$item["ID"]] = $item;
    }

    $obCache->EndDataCache(array("arResult" => $arResult));
}

$this->IncludeComponentTemplate();