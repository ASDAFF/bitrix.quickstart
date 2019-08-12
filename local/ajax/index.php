<?php
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/*----------- CALLBACK Form -----------------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["CALLBACK"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");


    $SITE_ID = $_POST["CALLBACK"]["SITE_ID"];
    $_POST["CALLBACK"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

    if (!array_key_exists("COMMENT", $_POST["CALLBACK"]))
        $_POST["CALLBACK"]["COMMENT"] = "-";

    CEvent::SendImmediate("CALLBACK_FORM", $SITE_ID, $_POST["CALLBACK"]);

    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- CALLBACK_MODAL Form -----------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["CALLBACK_MODAL"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["CALLBACK_MODAL"]["SITE_ID"];
    $_POST["CALLBACK_MODAL"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

    if (!array_key_exists("COMMENT", $_POST["CALLBACK_MODAL"]))
        $_POST["CALLBACK_MODAL"]["COMMENT"] = "-";

    // save to infoblock contacts information
    //\Bitrix\Main\Loader::includeModule("iblock");
    //$arLoadParams = array(
    //		"IBLOCK_ID" => $_POST["CALLBACK_MODAL"]["IBLOCK_ID"],
    //		"IBLOCK_SECTION_ID" => false,
    //		"ACTIVE" => "Y",
    //		"DATE_ACTIVE_FROM" => $_POST["CALLBACK_MODAL"]["DATE_ACTIVE_FROM"],
    //		"NAME"	=> $_POST["CALLBACK_MODAL"]["PHONE"],
    //		"PROPERTY_VALUES" => array("NAME" => $_POST["CALLBACK_MODAL"]["NAME"], "COMMENT" => $_POST["CALLBACK_MODAL"]["COMMENT"]),
    //	);

    //$el = new CIBlockElement;
    //if($el->add($arLoadParams))
    CEvent::SendImmediate("CALLBACK_FORM", $SITE_ID, $_POST["CALLBACK_MODAL"]);
    //else $arr["MESSAGE"]["ERROR"] = $el->LAST_ERROR;

    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- FEEDBACK Form -----------------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["FEEDBACK"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["FEEDBACK"]["SITE_ID"];
    $_POST["FEEDBACK"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии
    CEvent::SendImmediate("FEEDBACK_FORM", $SITE_ID, $_POST["FEEDBACK"]);
    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- FEEDBACK_MODAL Form -----------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["FEEDBACK_MODAL"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["FEEDBACK_MODAL"]["SITE_ID"];
    $_POST["FEEDBACK_MODAL"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии
    CEvent::SendImmediate("FEEDBACK_FORM", $SITE_ID, $_POST["FEEDBACK_MODAL"]);
    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}


if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["VACANCIES"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $_POST["VACANCIES"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $_POST["VACANCIES"]["FILE"] = "-";

    $SITE_ID = $_POST["VACANCIES"]["SITE_ID"];
    $arr["MESSAGE"]["ERROR"] = 0;

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch()) {
        $LANG_CHARSET = $arSite["CHARSET"];
        $SITE_DIR = $arSite["DIR"];

    }

    if (!empty($_FILES['FILE']['tmp_name'])) {
        //SendForms::convert_charset_array($_FILES, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

        //создаем папку загрузки файла
        $uploaddir = $SITE_DIR . 'images/' . md5(time()) . '/';
        mkdir($_SERVER["DOCUMENT_ROOT"] . $uploaddir);

        //адрес расположения нового файла
        $uploadfile = $uploaddir . $_FILES['FILE']['name'];

        // Копируем файл из каталога для временного хранения файлов
        if (!copy($_FILES['FILE']['tmp_name'], $_SERVER["DOCUMENT_ROOT"] . $uploadfile))
            $arr["MESSAGE"]["ERROR"] = 1;
        else $_POST["VACANCIES"]["FILE"] = "https://" . $_SERVER["SERVER_NAME"] . $uploadfile;
    }

    if ($arr["MESSAGE"]["ERROR"] < 1)
        CEvent::SendImmediate("VACANCIES_FORM", $SITE_ID, $_POST["VACANCIES"]);

    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- COMMENTS Form -----------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["COMMENTS"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["COMMENTS"]["SITE_ID"];
    $_POST["COMMENTS"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

    if (!array_key_exists("STARS", $_POST["COMMENTS"]))
        $_POST["COMMENTS"]["STARS"] = "";

    // save to infoblock information
    \Bitrix\Main\Loader::includeModule("iblock");
    $arLoadParams = array(
        "IBLOCK_ID" => $_POST["COMMENTS"]["IBLOCK_ID"],
        "IBLOCK_SECTION_ID" => false,
        "ACTIVE" => "N",
        "DATE_ACTIVE_FROM" => $_POST["COMMENTS"]["DATE_ACTIVE_FROM"],
        "NAME" => $_POST["COMMENTS"]["NAME"],
        "PREVIEW_TEXT" => $_POST["COMMENTS"]["COMMENT"],
        "PREVIEW_TEXT_TYPE" => "text",
        "PROPERTY_VALUES" => array("ID" => $_POST["COMMENTS"]["ID"], "EMAIL" => $_POST["COMMENTS"]["EMAIL"], "STARS" => $_POST["COMMENTS"]["STARS"]),
    );

    $el = new CIBlockElement;
    if ($el->add($arLoadParams))
        CEvent::SendImmediate("COMMENTS_FORM", $SITE_ID, $_POST["COMMENTS"]);
    else $arr["MESSAGE"]["ERROR"] = $el->LAST_ERROR;

    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- ORDER Form -----------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["ORDER"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["ORDER"]["SITE_ID"];
    $_POST["ORDER"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

    // save to infoblock information
    \Bitrix\Main\Loader::includeModule("iblock");
    $arLoadParams = array(
        "IBLOCK_ID" => $_POST["ORDER"]["IBLOCK_ID"],
        "IBLOCK_SECTION_ID" => false,
        "ACTIVE" => "Y",
        "DATE_ACTIVE_FROM" => $_POST["ORDER"]["DATE_ACTIVE_FROM"],
        "NAME" => $_POST["ORDER"]["PRODUCT_NAME"],
        "PROPERTY_VALUES" => array(
            "ID" => $_POST["ORDER"]["ID"],
            "NAME" => $_POST["ORDER"]["NAME"],
            "PHONE" => $_POST["ORDER"]["PHONE"],
            "EMAIL" => $_POST["ORDER"]["EMAIL"],
            "COMMENT" => $_POST["ORDER"]["COMMENT"]
        ),
    );

    $el = new CIBlockElement;
    if ($el->add($arLoadParams))
        CEvent::SendImmediate("ORDER_FORM", $SITE_ID, $_POST["ORDER"]);
    else $arr["MESSAGE"]["ERROR"] = $el->LAST_ERROR;

    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- CONTACTS Form -----------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["CONTACTS"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["CONTACTS"]["SITE_ID"];
    $_POST["CONTACTS"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

    CEvent::SendImmediate("FEEDBACK_FORM", $SITE_ID, $_POST["CONTACTS"]);
    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- CONTACTS_MODAL Form -----------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["CONTACTS_MODAL"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["CONTACTS_MODAL"]["SITE_ID"];
    $_POST["CONTACTS_MODAL"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии
    CEvent::SendImmediate("FEEDBACK_FORM", $SITE_ID, $_POST["CONTACTS_MODAL"]);
    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- ORDER_SCHEME Form -----------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["ORDER_SCHEME"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["ORDER_SCHEME"]["SITE_ID"];
    $_POST["ORDER_SCHEME"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;

    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

    // save to infoblock information
    \Bitrix\Main\Loader::includeModule("iblock");
    $arLoadParams = array(
        "IBLOCK_ID" => $_POST["ORDER_SCHEME"]["IBLOCK_ID"],
        "IBLOCK_SECTION_ID" => false,
        "ACTIVE" => "Y",
        "DATE_ACTIVE_FROM" => $_POST["ORDER_SCHEME"]["DATE_ACTIVE_FROM"],
        "NAME" => $_POST["ORDER_SCHEME"]["NAME_SCHEME"],
        "PROPERTY_VALUES" => array(
            "ID" => $_POST["ORDER_SCHEME"]["ID"],
            "SECT" => $_POST["ORDER_SCHEME"]["SECT"],
            "SPACE" => $_POST["ORDER_SCHEME"]["SPACE"],
            "METHOD" => $_POST["ORDER_SCHEME"]["METHOD"],
            "COLOR" => $_POST["ORDER_SCHEME"]["COLOR"],
            "NAME" => $_POST["ORDER_SCHEME"]["NAME"],
            "PHONE" => $_POST["ORDER_SCHEME"]["PHONE"]
        ),
    );

    $el = new CIBlockElement;
    if ($el->add($arLoadParams))
        CEvent::SendImmediate("ORDER_SCHEME_FORM", $SITE_ID, $_POST["ORDER_SCHEME"]);
    else $arr["MESSAGE"]["ERROR"] = $el->LAST_ERROR;

    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}

/*----------- NETWORK Form -----------*/
if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["REQUEST_METHOD"] == "POST" && is_array($_POST["NETWORK"])) {
    header("Cache-Control: no-store, no-cache, must-revalidate");

    $SITE_ID = $_POST["NETWORK"]["SITE_ID"];
    $_POST["NETWORK"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
    $arr["MESSAGE"]["ERROR"] = 0;


    $dbSite = CSite::GetByID($SITE_ID);
    if ($arSite = $dbSite->Fetch())
        $LANG_CHARSET = $arSite["CHARSET"];

    //SendForms::convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);// если сайт работает в кодировке windows-1251 убрать комментарии

        CEvent::SendImmediate("NETWORK", $SITE_ID, $_POST["NETWORK"]);

    //SendForms::convert_charset_array($arr, $LANG_CHARSET, "UTF-8");// если сайт работает в кодировке windows-1251 убрать комментарии
    echo json_encode($arr);

    return;
}