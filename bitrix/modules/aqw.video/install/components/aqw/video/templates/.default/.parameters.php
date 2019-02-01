<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
    "IBLOCKS"  =>  Array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
        "TYPE" => "STRING",
        //"VALUES" => $arIBlocks,
        "DEFAULT" => '',
        "MULTIPLE" => "Y",
        "ADDITIONAL_VALUES" => "Y"
    ),
);

