<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arErrorCorect = array("L" => GetMessage("ALTASIB_ERR_L"),
    "M" => GetMessage("ALTASIB_ERR_M"),
    "Q" => GetMessage("ALTASIB_ERR_Q"),
    "H" => GetMessage("ALTASIB_ERR_H"));

$arTypeQR = array(
    "TEXT" => GetMessage("ALTASIB_TYPE_TEXT"),
    "URL" => GetMessage("ALTASIB_TYPE_URL"),
    "TEL" => GetMessage("ALTASIB_TYPE_TEL"),
    "VCARD" => GetMessage("ALTASIB_TYPE_VCARD")
);
$arSize = array(
    "1" => "1",
    "2" => "2",
    "3" => "3",
    "4" => "4",
    "5" => "5",
    "6" => "6",
    "7" => "7"
);
/*
?>
<div style="background-color: #8E8E8E; height: 30px; padding: 7px; margin-bottom: 5px;">
        <a href="http://www.is-market.ru"><img src="/bitrix/components/altasib/qrcode/images/logo.gif" style="float: left; margin-right: 15px;" border="0" /></a>
        <div style="margin: 13px 0px 0px 0px">
        <a href="http://www.is-market.ru" style="color: #fff; font-size: 10px;"><?=GetMessage("ALTASIB_IS")?></a>
        </div>
</div>
<? */

$arComponentParameters = array(
    "GROUPS" => array(
        "QR_TYPE" => array(
            "SORT" => 100,
            "NAME" => GetMessage("ALTASIB_QR_TYPE_TITLE"),
        )
    ),
    "PARAMETERS" => array(
        "QR_TYPE_INF" => Array(
            "PARENT" => "QR_TYPE",
            "NAME" => GetMessage("ALTASIB_QR_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypeQR,
            "DEFAULT" => "TEXT",
            "REFRESH" => "Y",
        ),

        "QR_SIZE_VAL" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_SIZE_VAL"),
            "VALUES" => $arSize,
            "TYPE" => "LIST",
            "DEFAULT" => "4",
        ),

        "QR_ERROR_CORECT" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_ERROR_CORECT"),
            "TYPE" => "LIST",
            "VALUES" => $arErrorCorect,
            "DEFAULT" => "L",
        ),
        "QR_SQUARE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_SQUARE"),
            "TYPE" => "STRING",
            "DEFAULT" => "2",
            "COLS" => "6",
        ),
        "QR_COLOR" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_COLOR"),
            "TYPE" => "COLORPICKER",
            "DEFAULT" => "000000",
        ),
        "QR_COLORBG" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_COLORBG"),
            "TYPE" => "COLORPICKER",
            "DEFAULT" => "FFFFFF",
        ),
        "QR_MINI" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_MINI"),
            "TYPE" => "TEXT",
            "DEFAULT" => "",
        ),
        "QR_COPY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_COPY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "QR_TEXT" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_TEXT"),
            "TYPE" => "TEXT",
            "DEFAULT" => GetMessage("ALTASIB_QR_TEXT_DEFAULT"),
        ),
        "QR_DEL_CHACHE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ALTASIB_QR_DEL_CACHE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "CACHE_TIME" => array("DEFAULT" => 2592000),
    ),
);

if ($arCurrentValues["QR_TYPE_INF"] == "TEXT" || count($arCurrentValues) == 0) {
    $arComponentParameters["PARAMETERS"]["QR_TEXT"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_TEXT"),
        "TYPE" => "TEXTAREA",
        "COLS" => "30",
        "ROWS" => "2"
    );
}
if ($arCurrentValues["QR_TYPE_INF"] == "URL") {
    $arComponentParameters["PARAMETERS"]["QR_URL_CURRENT"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_URL_CURRENT"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y"
    );
}
if ($arCurrentValues["QR_URL_CURRENT"] == "Y"){
    $arComponentParameters["PARAMETERS"]["QR_VALID_PROPERTY"] = Array(
        "PARENT"    => "QR_TYPE",
        "NAME"      => GetMessage("ALTASIB_QR_VALID_PROPERTY"),
        "TYPE"      => "STRING",
        "DEFAULT"   => "ID,IBLOCK_ID,SECTION_ID,ELEMENT_ID,PARENT_ELEMENT_ID,FID,SID,EID,TID,MID,UID,VOTE_ID,print,goto"
    );
}
if ($arCurrentValues["QR_TYPE_INF"] == "URL" && $arCurrentValues["QR_URL_CURRENT"] != "Y") {
    $arComponentParameters["PARAMETERS"]["QR_URL"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_URL"),
        "TYPE" => "STRING"
    );
}
if ($arCurrentValues["QR_TYPE_INF"] == "TEL") {
    $arComponentParameters["PARAMETERS"]["QR_TEL_NUMB"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_TEL_NUMB"),
        "TYPE" => "STRING"
    );
    $arComponentParameters["PARAMETERS"]["QR_TEL_TEXT"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_TEL_TEXT"),
        "TYPE" => "STRING",
        "COLS" => "30",
        "ROWS" => "2"
    );
}
if ($arCurrentValues["QR_TYPE_INF"] == "VCARD") {
    $arComponentParameters["PARAMETERS"]["QR_VC_FNAME"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_VC_FNAME"),
        "TYPE" => "STRING");
    $arComponentParameters["PARAMETERS"]["QR_VC_LNAME"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_VC_LNAME"),
        "TYPE" => "STRING");
    $arComponentParameters["PARAMETERS"]["QR_VC_TEL"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_TEL_NUMB"),
        "TYPE" => "STRING");
    $arComponentParameters["PARAMETERS"]["QR_VC_EMAIL"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_EMAIL"),
        "TYPE" => "STRING");
    $arComponentParameters["PARAMETERS"]["QR_VC_COMPANY"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_COMPANY"),
        "TYPE" => "STRING");
    $arComponentParameters["PARAMETERS"]["QR_VC_TITLE"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_TITLE"),
        "TYPE" => "STRING");
    $arComponentParameters["PARAMETERS"]["QR_VC_ADR"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_ADR"),
        "TYPE" => "STRING");
    $arComponentParameters["PARAMETERS"]["QR_VC_URL"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_URL"),
        "TYPE" => "STRING");
    $arComponentParameters["PARAMETERS"]["QR_VC_NOTE"] = Array(
        "PARENT" => "QR_TYPE",
        "NAME" => GetMessage("ALTASIB_QR_NOTE"),
        "TYPE" => "STRING",
        "COLS" => "30",
        "ROWS" => "2");
}
?>
