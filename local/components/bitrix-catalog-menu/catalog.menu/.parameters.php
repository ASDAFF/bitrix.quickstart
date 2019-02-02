<?php

    /**
     * @author Gennadiy Hatuntsev
     * @package catalog.menu
     */

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

    $arComponentParameters = array(
        "PARAMETERS" => array(
            "IBLOCK_ID" => array(
                "NAME" => "ID инфоблока",
                "TYPE" => "STRING",
                "MULTIPLE" => "N",
                "PARENT" => "BASE"
            ),
            "SORT_FIELD" => array(
                "NAME" => "Поле сортировки",
                "PARENT" => "BASE",
                "TYPE" => "LIST",
                "MULTIPLE" => "N",
                "VALUES" => array("NAME" => "Наименование", "SORT" => "Сортировка"),
                "DEFAULT" => "NAME"
            ),
            "SORT_ORDER" => array(
                "NAME" => "Направление сортировки",
                "PARENT" => "BASE",
                "TYPE" => "LIST",
                "MULTIPLE" => "N",
                "VALUES" => array("ASC" => "По возрастанию", "DESC" => "По убыванию"),
                "DEFAULT" => "ASC"
            )
        )
    );
