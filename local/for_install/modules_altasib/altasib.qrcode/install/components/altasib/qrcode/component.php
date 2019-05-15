<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

function Dec($Hex){
    if (is_numeric($Hex))
        return $Hex;
    switch ($Hex) {
        case "A": return 10;
        case "B": return 11;
        case "C": return 12;
        case "D": return 13;
        case "E": return 14;
        case "F": return 15;
        default:  return false;
    }
}

function fRGB($COLOR){
    for ($a = 0; $a < 6; $a++) {
        $arr_tmp[$a] = Dec(substr($COLOR, $a, 1));
        if ($arr_tmp[$a] === false)
            return false;
    }
    $arr_RGB["R"] = $arr_tmp[0] * 16 + $arr_tmp[1];
    $arr_RGB["G"] = $arr_tmp[2] * 16 + $arr_tmp[3];
    $arr_RGB["B"] = $arr_tmp[4] * 16 + $arr_tmp[5];
    return $arr_RGB;
}

$arParams["QR_URL_CURRENT"] = $arParams["QR_URL_CURRENT"] == "Y";
if ($arParams["QR_URL_CURRENT"]){
    $arParams["QR_VALID_PROPERTY"] = preg_replace("/\s/", '', $arParams["QR_VALID_PROPERTY"]);
    $arParams["QR_URL"] =$_SERVER["HTTP_HOST"].$APPLICATION->GetCurPageParam('', array_diff(array_keys($_GET), explode(',', $arParams["QR_VALID_PROPERTY"])));
}
$arParams["QR_COLOR"] = strtoupper(trim(str_replace(array("#", "'", "\""), "", $arParams["QR_COLOR"])));
if (!$arParams["QR_COLOR"] = fRGB($arParams["QR_COLOR"])) $arParams["QR_COLOR"] = "0,0,0";

$arParams["QR_MINI"] = intval($arParams["QR_MINI"]);

$arParams["QR_COLORBG"] = strtoupper(trim(str_replace(array("#", "'", "\""), "", $arParams["QR_COLORBG"])));
if (!$arParams["QR_COLORBG"] = fRGB($arParams["QR_COLORBG"])) $arParams["QR_COLORBG"] = "255,255,255";
$arParams["QR_TEXT"] = trim($arParams["QR_TEXT"]);
$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
if ($arParams["CACHE_TIME"] < 0)
    $arParams["CACHE_TIME"] = 3000;

$arParams["QR_SIZE_VAL"] = intval($arParams["QR_SIZE_VAL"]);
if ($arParams["QR_SIZE_VAL"] < 0)
    $arParams["QR_SIZE_VAL"] = 7;

if (strtoupper($arParams["QR_ERROR_CORECT"]) != "L" &&
    strtoupper($arParams["QR_ERROR_CORECT"]) != "M" &&
    strtoupper($arParams["QR_ERROR_CORECT"]) != "Q" &&
    strtoupper($arParams["QR_ERROR_CORECT"]) != "H"
)
    $arParams["QR_ERROR_CORECT"] = "L";


if (strtoupper($arParams["QR_TYPE_INF"]) != "TEXT" &&
    strtoupper($arParams["QR_TYPE_INF"]) != "URL" &&
    strtoupper($arParams["QR_TYPE_INF"]) != "TEL" &&
    strtoupper($arParams["QR_TYPE_INF"]) != "VCARD"
)
    $arParams["QR_TYPE_INF"] = false;

$arParams["QR_SQUARE"] = intval($arParams["QR_SQUARE"]);
if ($arParams["QR_SQUARE"] < 0)
    $arParams["QR_SQUARE"] = 2;

$arParams["QR_DEL_CHACHE"] = $arParams["QR_DEL_CHACHE"] == "Y";

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/components/altasib/qrcode/phpqrcode/qrlib.php");

if ($this->StartResultCache()) {

    global $APPLICATION;

    $qrVal = null;
    if ($arParams["QR_TYPE_INF"] == "TEXT") {
        $qrVal = $arParams["QR_TEXT"];
    }
    if ($arParams["QR_TYPE_INF"] == "URL") {
        if (ereg("^http://", trim($arParams["QR_URL"]))) {
            $qrVal = $arParams["QR_URL"];
        } else {
            $qrVal = "http://".$arParams["QR_URL"];
        }
    }
    if ($arParams["QR_TYPE_INF"] == "TEL") {
        $qrVal = "SMSTO:".$arParams["QR_TEL_NUMB"].":".$arParams["QR_TEL_TEXT"];
    }
    if ($arParams["QR_TYPE_INF"] == "VCARD") {
        $qrVal = "BEGIN:VCARD\n";
        if (strlen($arParams["QR_VC_FNAME"]) > 0)
            $qrVal = $qrVal."N:".$arParams["QR_VC_FNAME"].";";
        if (strlen($arParams["QR_VC_LNAME"]) > 0)
            $qrVal = $qrVal." ".$arParams["QR_VC_LNAME"]."\n";
        else
            $qrVal = $qrVal."\n";
        if (strlen($arParams["QR_VC_TEL"]) > 0)
            $qrVal = $qrVal."TEL;TYPE=voice:".$arParams["QR_VC_TEL"]."\n";
        if (strlen($arParams["QR_VC_EMAIL"]) > 0)
            $qrVal = $qrVal."EMAIL;TYPE=INTERNET:".$arParams["QR_VC_EMAIL"]."\n";
        if (strlen($arParams["QR_VC_COMPANY"]) > 0)
            $qrVal = $qrVal."ORG:".$arParams["QR_VC_COMPANY"]."\n";
        if (strlen($arParams["QR_VC_TITLE"]) > 0)
            $qrVal = $qrVal."TITLE:".$arParams["QR_VC_TITLE"]."\n";
        if (strlen($arParams["QR_VC_ADR"]) > 0)
            $qrVal = $qrVal."ADR;TYPE=work:;;".$arParams["QR_VC_ADR"]."\n";
        if (strlen($arParams["QR_VC_URL"]) > 0)
            $qrVal = $qrVal."URL:".$arParams["QR_VC_URL"]."\n";
        if (strlen($arParams["QR_VC_NOTE"]) > 0)
            $qrVal = $qrVal."NOTE:".$arParams["QR_VC_NOTE"]."\n";
        $qrVal = $qrVal."END:VCARD";
    }

    if (BX_UTF !== true) $qrVal = $APPLICATION->ConvertCharset($qrVal, "windows-1251", "UTF-8");

    if ($this->__templateName == "input") {
        if (isset($_POST["QR_VALUE"]) && strlen($_POST["QR_VALUE"]) > 0 && strlen($_POST["GEN_QR"]) > 0) {
            $md = md5($_POST["QR_VALUE"].$arParams["QR_SIZE_VAL"].$arParams["QR_ERROR_CORECT"].$arParams["QR_SQUARE"]);
            if (BX_UTF !== true)
                $qrVal = $APPLICATION->ConvertCharset($_POST["QR_VALUE"], "windows-1251", "UTF-8");
        }
    } else {
        $md = md5($qrVal.$arParams["QR_SIZE_VAL"].$arParams["QR_ERROR_CORECT"].$arParams["QR_SQUARE"]);
    }
    if ($_GET["clear_cache"] == "Y" && $arParams["QR_DEL_CHACHE"]) {
        unlink($_SERVER["DOCUMENT_ROOT"].'/upload/altasib/qrcode/'.$md.'.png');
        unlink($_SERVER["DOCUMENT_ROOT"].'/upload/altasib/qrcode/'.$md.'_copy.png');
        $this->ClearResultCache();
    }
    CheckDirPath($_SERVER["DOCUMENT_ROOT"]."/upload/altasib/qrcode/", true);

    if (!file_exists($_SERVER["DOCUMENT_ROOT"].'/upload/altasib/qrcode/'.$md.'.png')) {
        $qrVal = htmlspecialcharsBack(trim($qrVal));
        if (strlen($qrVal) > 0) {
            QRcode::png($qrVal, $_SERVER["DOCUMENT_ROOT"].'/upload/altasib/qrcode/'.$md.'.png', $arParams["QR_ERROR_CORECT"], $arParams["QR_SIZE_VAL"], $arParams["QR_SQUARE"], false, $arParams["QR_COLOR"], $arParams["QR_COLORBG"]);
            if ($arParams["QR_COPY"] == "Y")
                QRcode::png($qrVal, $_SERVER["DOCUMENT_ROOT"].'/upload/altasib/qrcode/'.$md.'_copy.png', $arParams["QR_ERROR_CORECT"], $arParams["QR_SIZE_VAL"], $arParams["QR_SQUARE"], false, array("R" => 0, "G" => 0, "B" => 0), array("R" => 255, "G" => 255, "B" => 255));
            $arResult["RESULT"] = "Y";
        } else {
            $arResult["RESULT"] = "N";
        }
    }

    if (file_exists($_SERVER["DOCUMENT_ROOT"].'/upload/altasib/qrcode/'.$md.'.png'))
        $arResult["RESULT"] = "Y";
    else
        $arResult["RESULT"] = "N";

    $arResult["QRCODE"] = '/upload/altasib/qrcode/'.$md.'.png';
    if ($arParams["QR_COPY"] == "Y")
        $arResult["QRCODE_COPY"] = '/upload/altasib/qrcode/'.$md.'_copy.png';
    else
        $arResult["QRCODE_COPY"] = '/upload/altasib/qrcode/'.$md.'.png';
    $this->IncludeComponentTemplate();
}
?>
