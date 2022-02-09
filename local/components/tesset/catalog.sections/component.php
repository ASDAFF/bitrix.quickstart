<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/*Подключение модуля информационных блоков*/
if (!CModule::IncludeModule("iblock"))
    return;

if (!$arParams["IBLOCK_ID"]) {$arParams["IBLOCK_ID"] = 4; }

$obCache = new CPHPCache;
$CACHE_ID = "CATALOG" . $arParams["IBLOCK_ID"] . implode('-', $arParams["ID"]) . $_GET["SECTION_CODE"];

if($obCache->InitCache($arParams["CACHE_TIME"], $CACHE_ID, "/")) {
    $cache = $obCache->GetVars();
    $arResult = $cache["arResult"];
}
else {
    $obCache->StartDataCache();
    $filter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y");
    if ($_GET["SECTION_CODE"]) {
        $mainSection = CIBlockSection::GetList(false, array("CODE" => $_GET["SECTION_CODE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID"))->Fetch();
        $filter["SECTION_ID"] = $mainSection["ID"];
    } else {
        $filter["DEPTH_LEVEL"] = 1;
    }
    /**
     * Get catalog I Level
     */
    $rsSections = CIBlockSection::GetList(
        array("SORT" => "ASC"),
        $filter,
        false,
        array("NAME", "IBLOCK_ID", "SECTION_PAGE_URL", "PICTURE", "CODE", "DEPTH_LEVEL", "IBLOCK_SECTION_ID")
        );
    while ($section = $rsSections->GetNext()) {
        $arButtons = CIBlock::GetPanelButtons(
            $section["IBLOCK_ID"],
            $section["ID"],
            0,
            array("SECTION_BUTTONS"=>false, "SESSID"=>false)
        );
        $section["EDIT_LINK"] = $arButtons["edit"]["edit_section"]["ACTION_URL"];
        $section["DELETE_LINK"] = $arButtons["edit"]["delete_section"]["ACTION_URL"];
        $section["PICTURE"] = CFile::GetPath($section["PICTURE"]);
        $arResult["SECTIONS"]["I_LEVEL"][$section["ID"]] = $section;
        $SectionsFirstLevel[] = $section["ID"];
        if (!$nextDepthLevel) {
            $nextDepthLevel = 1 + $section["DEPTH_LEVEL"];
        }
    }
    $filter["DEPTH_LEVEL"] = $nextDepthLevel;
    $filter["SECTION_ID"] = $SectionsFirstLevel;

    /**
     * Get catalog II Level
     */
    if ($arResult["SECTIONS"]["I_LEVEL"]) {
        $rsSections = CIBlockSection::GetList(
            array("SORT" => "ASC"),
            $filter,
            false,
            array("NAME", "SECTION_PAGE_URL", "PICTURE", "CODE", "DEPTH_LEVEL", "IBLOCK_SECTION_ID")
            );
        $nextDepthLevel = false;
        while ($section = $rsSections->GetNext()) {
            $section["PICTURE"] = CFile::GetPath($section["PICTURE"]);
            $arResult["SECTIONS"]["II_LEVEL"][$section["IBLOCK_SECTION_ID"]][] = $section;
            if (!$nextDepthLevel) {
                $nextDepthLevel = 1 + $section["DEPTH_LEVEL"];
            }
        }
        $filter["DEPTH_LEVEL"] = $nextDepthLevel;
        if ($arResult["SECTIONS"]["II_LEVEL"]) {
            /**
             * Get catalog III Level
             */
            $rsSections = CIBlockSection::GetList(
                array("SORT" => "ASC"),
                $filter,
                false,
                array("NAME", "SECTION_PAGE_URL", "PICTURE", "CODE", "DEPTH_LEVEL", "IBLOCK_SECTION_ID")
                );

            while ($section = $rsSections->GetNext()) {
                $section["PICTURE"] = CFile::GetPath($section["PICTURE"]);
                $arResult["SECTIONS"]["III_LEVEL"][$section["IBLOCK_SECTION_ID"]][] = $section;
            }
        } else {
            $arResult["OPTIONS"]["SHOW_ITEMS_II_LEVEL"] = true;
            $arResult["OPTIONS"]["SHOW_ITEMS"] = true;
            $rsItems = \CIBlockElement::GetList(false, array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "SECTION_ID" => $SectionsFirstLevel), false, false, false);
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
            $arResult["ITEMS"][$item["IBLOCK_SECTION_ID"]][$item["ID"]] = array(
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
        }
    } else {
        $arResult["OPTIONS"]["SHOW_ITEMS"] = true;
        $rsItems = \CIBlockElement::GetList(false, array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "SECTION_ID" => $mainSection["ID"]), false, false, false);
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
    }
    $obCache->EndDataCache(array("arResult" => $arResult));
}



$this->IncludeComponentTemplate();