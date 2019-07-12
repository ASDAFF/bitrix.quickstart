<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    $APPLICATION->IncludeComponent(
        "novagroup:search.title",
        "demoshop",
        Array(
            "SHOW_INPUT" => "Y",
            "INPUT_ID" => "title-search-input",
            "CONTAINER_ID" => "search",
            "PAGE" => SITE_DIR."catalog/",
            "NUM_CATEGORIES" => "1",
            "TOP_COUNT" => "4",
            "ORDER" => "date",
            "USE_LANGUAGE_GUESS" => "Y",
            "CHECK_DATES" => "N",
            "SHOW_OTHERS" => "N",
            "CATEGORY_OTHERS_TITLE" => GetMessage("T_CATEGORY_OTHERS_TITLE"),
            "CATEGORY_0_TITLE" => GetMessage("T_CATEGORY_0_TITLE"),
            "CATEGORY_0" => array("iblock_catalog"),
            "CATEGORY_0_iblock_catalog" => array("#CATALOG_IBLOCK_ID#"),
            //"FASHION_IBLOCK_ID" => 16,
            "CATALOG_IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
            "OFFERS_IBLOCK_ID" => "#OFFERS_IBLOCK_ID#",
            "CATALOG_IBLOCK_NAME" => "Товары",
            "CATALOG_IBLOCK_PATH" => SITE_DIR."catalog/",
            //"FASHION_IBLOCK_NAME" => "Образы",
            //"FASHION_IBLOCK_PATH" => "/imageries/",
            "QUERY" => $_POST['q']
        ), false,
        Array(
            'ACTIVE_COMPONENT' => 'Y',
            "HIDE_ICONS" => "Y"
        )
    );
?>
    