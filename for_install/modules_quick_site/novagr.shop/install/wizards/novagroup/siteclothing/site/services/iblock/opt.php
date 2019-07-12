<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 30.01.14
 * Time: 15:19
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

if(!CModule::IncludeModule('catalog'))
    return;

WizardServices::IncludeServiceLang("opt.php", "ru");

$dbResultList = CCatalogGroup::GetList(Array(), Array("BASE" => "Y"));
if(!($dbResultList->Fetch()))
{
    $arFields = Array();
    $rsLanguage = CLanguage::GetList($by, $order, array());
    while($arLanguage = $rsLanguage->Fetch())
    {
        $arFields["USER_LANG"][$arLanguage["ID"]] = "BASE";
    }
    $arFields["BASE"] = "Y";
    $arFields["SORT"] = 100;
    $arFields["NAME"] = "BASE";
    $arFields["USER_GROUP"] = Array(1, 2);
    $arFields["USER_GROUP_BUY"] = Array(1, 2);
    CCatalogGroup::Add($arFields);
}

//???????? ?????? ?????????????
$rsGroups = CGroup::GetList ($by = "c_sort", $order = "asc", Array ("STRING_ID" => "opt"));
if($arFields = $rsGroups->Fetch()){
    $NEW_GROUP_ID = $arFields['ID'];
} else {
    $group = new CGroup;
    $arFields = Array (
        "ACTIVE" => "Y" ,
        "C_SORT" => 100 ,
        "ANONYMOUS" => "N" ,
        "NAME" => GetMessage("OPT_GROUP") ,
        "DESCRIPTION" => GetMessage("OPT_GROUP") ,
        "STRING_ID" => "opt" ,
    );
    $NEW_GROUP_ID = $group->Add($arFields);
}

//???????? ?????? ???
if($NEW_GROUP_ID>0)
{
    $arFields = array(
        "NAME" => "opt",
        "BASE" => "N" ,
        "SORT" => 100,
        "USER_GROUP" => array($NEW_GROUP_ID),   // ????? ???? ????? ????? 2 ? 4
        "USER_GROUP_BUY" => array($NEW_GROUP_ID),  // ???????? ?? ???? ????
        // ?????? ????? ?????? 2
        "USER_LANG" => array(
            "ru" => GetMessage("OPT_PRICE"),
            "en" => "Opt price"
        )
    );
    $ID = CCatalogGroup::Add($arFields);
}
