<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arProps = array(); 
$rs=CIBlockProperty::GetList(array(),array("IBLOCK_ID"=>$arCurrentValues['IBLOCK_ID'],"ACTIVE"=>"Y"));
while($f = $rs->Fetch())
   $arProps[$f['CODE']] = $f['NAME'];

$arTemplateParameters = array(
	"DISPLAY_DATE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_NAME" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_NAME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
       "PROPERTY_VIDEO" => Array(
                "NAME" => GetMessage("T_IBLOCK_DESC_NEWS_VIDEO"),
                "TYPE" => "LIST",
                "VALUES" => $arProps,
        ),
       "PROPERTY_FOTO" => Array(
                "NAME" => GetMessage("T_IBLOCK_DESC_NEWS_FOTO"),
                "TYPE" => "LIST",
                "VALUES" => $arProps,
        ),
);
?>
