<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php"); 
CModule::IncludeModule("iblock");

//21.07.2014 - Update 2.1.3
if($arParams["ID_IBLOCK"] == "" || $arParams["ID_IBLOCK"] == "#COMMENTS_IBLOCK_ID#")
{
    $res = CIBlock::GetList(array(), array('TYPE' => 'personal', 'SITE_ID' => SITE_ID, 'ACTIVE' => 'Y', 'CODE' => 'comments_'.SITE_ID), false);
    if($ar_res = $res->Fetch())
        $arParams["ID_IBLOCK"] = $ar_res["ID"];
}

if(is_numeric($arParams["ID_RECORD"]) && $arParams["ID_RECORD"] > 0)
{
    if($arParams["NO_USE_CAPTCHA"] != "Y")
        $arResult["CAPTCHA_CODE"] = $APPLICATION->CaptchaGetCode();
    
    if($USER->GetID() > 0)
        if($USER->GetFullName() != "")
            $arResult["NAME"] = $USER->GetFullName();
            
    if($_POST["send"] =="y")
    {
        if($arParams["NO_USE_CAPTCHA"] != "Y")
        {
            $cptcha = new CCaptcha();
            if(!strlen($_REQUEST["captcha_word"]) > 0)
                $arResult["ERROR"][] = GetMessage("V1RT_COMMENT_CAPTCHA_CODE");
            elseif(!$cptcha->CheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"])) 
                $arResult["ERROR"][] = GetMessage("V1RT_COMMENT_CAPTCHA_CODE_ERROR");
        }
        
        if(strip_tags($_POST["NAME"]) != "")
            $arResult["NAME"] = strip_tags($_POST["NAME"]);
        else
            $arResult["ERROR"][] = GetMessage("V1RT_COMMENT_ERROR_NAME");
        
        if(strip_tags($_POST["MESSAGE"]) != "")
            $arResult["MESSAGE"] = strip_tags($_POST["MESSAGE"]);
        else
            $arResult["ERROR"][] = GetMessage("V1RT_COMMENT_ERROR_MESSAGE");
        
        
        if(count($arResult["ERROR"]) == 0)
        {
            $el = new CIBlockElement;
            $PROP = array();
            $PROP[$arParams["PROPERTY"]] = $arParams["ID_RECORD"];
            
            $arLoadProductArray = Array(
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID"      => IntVal($arParams["ID_IBLOCK"]),
                "PROPERTY_VALUES"=> $PROP,
                "NAME"           => $arResult["NAME"],
                "ACTIVE"         => "Y",
                "PREVIEW_TEXT"   => $arResult["MESSAGE"],
                "DATE_ACTIVE_FROM" => date("d.m.Y H:i:s"),
            );
            
            if($PRODUCT_ID = $el->Add($arLoadProductArray))
            {
                $arResult["RESULT"] = GetMessage("V1RT_COMMENT_ADD");
                unset($_POST["NAME"]);
                unset($_POST["send"]);
            }
            else
                $arResult["ERROR"][] = $el->LAST_ERROR;
        }
    }
    
    $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DATE_CREATE");
    $arFilter = Array("IBLOCK_ID" => IntVal($arParams["ID_IBLOCK"]), "ACTIVE"=>"Y", "PROPERTY_".$arParams["PROPERTY"] => $arParams["ID_RECORD"]);
    $res = CIBlockElement::GetList(Array("DATE_CREATE" => "ASC"), $arFilter, false, Array("nPageSize"=>10000), $arSelect);
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $arResult["COMMENTS"][] = $arFields;
    }
    
    $this->IncludeComponentTemplate();
}
?>