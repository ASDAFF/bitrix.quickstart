<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/*Подключение модуля информационных блоков*/
if (!CModule::IncludeModule("iblock"))
    return;

if (!$arParams["IBLOCK_ID"]) {$arParams["IBLOCK_ID"] = 8; }

$obCache = new CPHPCache;
$CACHE_ID = "VACANCY" . $arParams["IBLOCK_ID"] . implode('-', $arParams["ID"]);

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
        array(
            "IBLOCK_ID", 
            "ID", 
            "NAME",
            "PROPERTY_A_REQURIES",
            "PROPERTY_P_REQURIES",
            "PROPERTY_A_RESPONSIBILITY",
            "PROPERTY_P_RESPONSIBILITY",
            "PROPERTY_A_CONDITIONS",
            "PROPERTY_P_CONDITIONS",
            "PROPERTY_ZP"
            )
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
        $arResult["ITEMS"][$item["ID"]] = array(
            "ID" => $item["ID"],
            "IBLOCK_ID" => $item["IBLOCK_ID"],
            "NAME" => $item["NAME"],
            "ZP" => TMPriceFormat($item["PROPERTY_ZP_VALUE"]),
            "A_REQURIES" => $item["PROPERTY_A_REQURIES_VALUE"]["TEXT"],
            "P_REQURIES" => $item["PROPERTY_P_REQURIES_VALUE"]["TEXT"],
            "A_RESPONSIBILITY" => $item["PROPERTY_A_RESPONSIBILITY_VALUE"]["TEXT"],
            "P_RESPONSIBILITY" => $item["PROPERTY_P_RESPONSIBILITY_VALUE"]["TEXT"],
            "A_CONDITIONS" => $item["PROPERTY_A_CONDITIONS_VALUE"]["TEXT"],
            "P_CONDITIONS" => $item["PROPERTY_P_CONDITIONS_VALUE"]["TEXT"]
            );
    }

    $obCache->EndDataCache(array("arResult" => $arResult));
}

$this->IncludeComponentTemplate();