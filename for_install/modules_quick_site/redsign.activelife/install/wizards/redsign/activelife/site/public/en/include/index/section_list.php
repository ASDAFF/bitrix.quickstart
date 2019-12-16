<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
    "redsign:uf.section.list",
    "al",
    array(
        "COMPONENT_TEMPLATE" => "al",
        "IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
        "COUNT" => "25",
        "UF_CODE" => "UF_FOR_MAIN",
        "UF_VALUE" => "",
        "UF_VALUE_NOT" => "Y",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "MAX_WIDTH" => "",
        "MAX_HEIGHT" => "",
        "BLOCK_TITLE" => "Sports",
        "SHOW_TAB" => $bRSHomeTabShow,
    ),
    false
);?>