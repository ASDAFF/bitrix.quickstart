<?php
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 18.04.2019
 * Time: 18:13
 */

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section.list",
        "products",
        Array(
            "ADD_SECTIONS_CHAIN" => "N",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "COUNT_ELEMENTS" => "Y",
            "IBLOCK_ID" => IBLOCK_ID__VEHICLES,
            "IBLOCK_TITLE_TEXT" => "Разделы",
            "IBLOCK_TYPE" => "effortless",
            "SECTION_CODE" => "",
            "SECTION_FIELDS" => array("", ""),
            "SECTION_ID" => $_REQUEST["SECTION_ID"],
            "SECTION_URL" => "",
            "SECTION_USER_FIELDS" => array("", ""),
            "SHOW_PARENT_NAME" => "Y",
            "TOP_DEPTH" => "2",
            "VIEW_MODE" => "LIST"
        )
    );

}

