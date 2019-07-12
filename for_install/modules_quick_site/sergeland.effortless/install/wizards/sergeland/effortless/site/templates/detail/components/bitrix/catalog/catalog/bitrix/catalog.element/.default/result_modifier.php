<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

/*
foreach($arResult["MORE_PHOTO"] as &$arItem)
	$arItem["PREVIEW"] = CFile::ResizeImageGet(
		$arItem["ID"],
		array("width" => 800, "height"=>800),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	);
*/

$res = CIBlockElement::GetList(Array(), 
		Array(
				"ID"=>$arResult["PROPERTIES"]["MORE_PRODUCTS"]["VALUE"], 
				"IBLOCK_LID" => SITE_ID,
				"IBLOCK_ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
				"MIN_PERMISSION" => "R",
			 ),
			 false,
			 false,
		Array(
				"ID",
				"IBLOCK_ID",
				"CODE",
				"XML_ID",
				"NAME",
				"ACTIVE",
				"DATE_ACTIVE_FROM",
				"DATE_ACTIVE_TO",
				"SORT",
				"PREVIEW_TEXT",
				"PREVIEW_TEXT_TYPE",
				"DETAIL_TEXT",
				"DETAIL_TEXT_TYPE",
				"DATE_CREATE",
				"CREATED_BY",
				"TIMESTAMP_X",
				"MODIFIED_BY",
				"TAGS",
				"IBLOCK_SECTION_ID",
				"DETAIL_PAGE_URL",
				"DETAIL_PICTURE",
				"PREVIEW_PICTURE",
				"PROPERTY_*",
			)			 
);
while($obElement = $res->GetNextElement())
{
	$arFields = $obElement->GetFields();
	$arFields["PROPERTIES"] = $obElement->GetProperties();
	$arFields["PREVIEW_PICTURE"] = (0 < $arFields["PREVIEW_PICTURE"] ? CFile::GetFileArray($arFields["PREVIEW_PICTURE"]) : false);
	
	$arResult["MORE_PRODUCTS"][$arFields["ID"]] = $arFields;
}

if(!empty($arResult["PROPERTIES"]["MORE_PRODUCTS"]["VALUE"]) && is_array($arResult["PROPERTIES"]["MORE_PRODUCTS"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["MORE_PRODUCTS"]["VALUE"] as $ID)
		if(!empty($arResult["MORE_PRODUCTS"][$ID]))
			$arResult["PROPERTIES"]["MORE_PRODUCTS"]["ITEMS"][] = $arResult["MORE_PRODUCTS"][$ID];		


$res = CIBlockElement::GetList(Array(), 
		Array(
				"ID"=>$arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"], 
				"IBLOCK_LID" => SITE_ID,
				"IBLOCK_ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
				"MIN_PERMISSION" => "R",
			 ),
			 false,
			 false,
		Array(
				"ID",
				"IBLOCK_ID",
				"CODE",
				"XML_ID",
				"NAME",
				"ACTIVE",
				"DATE_ACTIVE_FROM",
				"DATE_ACTIVE_TO",
				"SORT",
				"PREVIEW_TEXT",
				"PREVIEW_TEXT_TYPE",
				"DETAIL_TEXT",
				"DETAIL_TEXT_TYPE",
				"DATE_CREATE",
				"CREATED_BY",
				"TIMESTAMP_X",
				"MODIFIED_BY",
				"TAGS",
				"IBLOCK_SECTION_ID",
				"DETAIL_PAGE_URL",
				"DETAIL_PICTURE",
				"PREVIEW_PICTURE",
				"PROPERTY_*",
			)
);
while($obElement = $res->GetNextElement())
{
	$arFields = $obElement->GetFields();
	$arFields["PROPERTIES"] = $obElement->GetProperties();
	$arFields["PROPERTIES"]["FILE"] = (0 < $arFields["PROPERTIES"]["FILE"]["VALUE"] ? CFile::GetFileArray($arFields["PROPERTIES"]["FILE"]["VALUE"]) : false);		
	$arResult["PROPERTIES"]["DOCUMENTS"]["~ITEMS"][$arFields["ID"]] = $arFields;
}

if(!empty($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"]) && is_array($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"]))	
	foreach($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"] as $ID)
		$arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"][$ID] = $arResult["PROPERTIES"]["DOCUMENTS"]["~ITEMS"][$ID];
		
$cp = $this->__component;
if(is_object($cp))
   $cp->SetResultCacheKeys(array("PROPERTIES", "PREVIEW_TEXT", "DETAIL_TEXT", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));
?>