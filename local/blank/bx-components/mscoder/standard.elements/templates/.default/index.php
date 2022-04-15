<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?$APPLICATION->IncludeComponent(
    "project:associations.list",
    "",
    Array(
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "COUNT" => $arParams['COUNT'],
        "IBLOCK_CODE" => $arParams['IBLOCK_CODE'],
        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
        "SHOW_NAV" => $arParams['SHOW_NAV'],
        "SORT_DIRECTION1" => $arParams['SORT_DIRECTION1'],
        "SORT_DIRECTION2" => $arParams['SORT_DIRECTION2'],
        "SORT_FIELD1" => $arParams['SORT_FIELD1'],
        "SORT_FIELD2" => $arParams['SORT_FIELD2']
    ),
    $component
);?>
