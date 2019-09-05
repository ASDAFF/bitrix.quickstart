<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
// пользователь
$APPLICATION->IncludeComponent(
    "site:users.detail",
    "",
    Array(
        "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
        "SEF_FOLDER" => $arParams["SEF_FOLDER"],
        "SET_TITLE" => $arParams["SET_ELEMENT_TITLE"],
        "ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
        "FIELDS" => $arParams["DETAIL_FIELDS"],
        "CACHE_TIME" => $arParams["CACHE_TIME"]
    ),
    $component
);
?>