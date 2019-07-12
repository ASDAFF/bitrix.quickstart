<?
$APPLICATION->IncludeComponent(
        "bitrix:catalog.section.list", "grocer", Array(
    "IBLOCK_TYPE" => "catalog",
    "IBLOCK_ID" => "7",
    "CACHE_TYPE" => "N",
    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"]
        ), $component
);
?>
