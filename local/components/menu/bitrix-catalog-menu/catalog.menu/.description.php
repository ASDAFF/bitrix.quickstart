<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

    $arComponentDescription = array(
        "NAME" => 'ghatuncev: меню разделов инфоблока',
        "DESCRIPTION" => 'Меню разделов каталога с возможностью выбора поля (NAME или SORT) и направления сортировки. Выводит список до третьего уровня вложенности.',
        "CACHE_PATH" => "Y",
        "PATH" => array(
            "ID" => "development",
            "NAME" => "DEVELOPMENT",
        )
    );
