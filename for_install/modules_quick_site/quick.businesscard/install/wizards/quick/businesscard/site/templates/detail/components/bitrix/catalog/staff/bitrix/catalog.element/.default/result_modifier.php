<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

// WORKS

$res = CIBlockElement::GetList(Array(),
		Array(
				"ID"=>$arResult["PROPERTIES"]["MORE_WORKS"]["VALUE"],
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
	$arFields["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arFields["DATE_ACTIVE_FROM"], CSite::GetDateFormat()));
	
	$arResult["MORE_WORKS"][$arFields["ID"]] = $arFields;
}

if(!empty($arResult["PROPERTIES"]["MORE_WORKS"]["VALUE"]) && is_array($arResult["PROPERTIES"]["MORE_WORKS"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["MORE_WORKS"]["VALUE"] as $ID)
		if(!empty($arResult["MORE_WORKS"][$ID]))
			$arResult["PROPERTIES"]["MORE_WORKS"]["ITEMS"][] = $arResult["MORE_WORKS"][$ID];

		
// LICENSE

$res = CIBlockElement::GetList(Array(), 
		Array(
				"ID"=>$arResult["PROPERTIES"]["MORE_LICENSE"]["VALUE"],
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
	$arFields["DETAIL_PICTURE"]  = (0 < $arFields["DETAIL_PICTURE"]  ? CFile::GetFileArray($arFields["DETAIL_PICTURE"])  : false);
	
	$arResult["MORE_LICENSE"][$arFields["ID"]] = $arFields;
}

if(!empty($arResult["PROPERTIES"]["MORE_LICENSE"]["VALUE"]) && is_array($arResult["PROPERTIES"]["MORE_LICENSE"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["MORE_LICENSE"]["VALUE"] as $ID)
		if(!empty($arResult["MORE_LICENSE"][$ID]))
			$arResult["PROPERTIES"]["MORE_LICENSE"]["ITEMS"][] = $arResult["MORE_LICENSE"][$ID];

		
// REVIEWS

$res = CIBlockElement::GetList(Array(),
		Array(
				"ID"=>$arResult["PROPERTIES"]["REVIEWS"]["VALUE"],
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
	$arResult["PROPERTIES"]["REVIEWS"]["~ITEMS"][$arFields["ID"]] = $arFields;
	$arResult["PROPERTIES"]["REVIEWS"]["~ITEMS"][$arFields["ID"]]["PREVIEW_PICTURE"] = (0 < $arFields["PREVIEW_PICTURE"] ? CFile::GetFileArray($arFields["PREVIEW_PICTURE"]) : false);
}

if( is_array($arResult["PROPERTIES"]["REVIEWS"]["VALUE"]) && !empty($arResult["PROPERTIES"]["REVIEWS"]["VALUE"]) )	
	foreach($arResult["PROPERTIES"]["REVIEWS"]["VALUE"] as $ID)
		$arResult["PROPERTIES"]["REVIEWS"]["ITEMS"][$ID] = $arResult["PROPERTIES"]["REVIEWS"]["~ITEMS"][$ID];


// SERVICES

$res = CIBlockElement::GetList(Array(),
		Array(
				"ID"=>$arResult["PROPERTIES"]["MORE_SERVICES"]["VALUE"],
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
	$arFields["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arFields["DATE_ACTIVE_FROM"], CSite::GetDateFormat()));
	
	$arResult["MORE_SERVICES"][$arFields["ID"]] = $arFields;
}

if(!empty($arResult["PROPERTIES"]["MORE_SERVICES"]["VALUE"]) && is_array($arResult["PROPERTIES"]["MORE_SERVICES"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["MORE_SERVICES"]["VALUE"] as $ID)
		if(!empty($arResult["MORE_SERVICES"][$ID]))
			$arResult["PROPERTIES"]["MORE_SERVICES"]["ITEMS"][] = $arResult["MORE_SERVICES"][$ID];

		
// ARTICLES

$res = CIBlockElement::GetList(Array(),
		Array(
				"ID"=>$arResult["PROPERTIES"]["MORE_ARTICLES"]["VALUE"],
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
	$arFields["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arFields["DATE_ACTIVE_FROM"], CSite::GetDateFormat()));
	
	$arResult["MORE_ARTICLES"][$arFields["ID"]] = $arFields;
}

if(!empty($arResult["PROPERTIES"]["MORE_ARTICLES"]["VALUE"]) && is_array($arResult["PROPERTIES"]["MORE_ARTICLES"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["MORE_ARTICLES"]["VALUE"] as $ID)
		if(!empty($arResult["MORE_ARTICLES"][$ID]))
			$arResult["PROPERTIES"]["MORE_ARTICLES"]["ITEMS"][] = $arResult["MORE_ARTICLES"][$ID];

		
if(!empty($arResult["PROPERTIES"]["PHOTO_BOTTOM"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["PHOTO_BOTTOM"]["VALUE"] as $key=>$arItem)
	{
		$arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"][$key]["PREVIEW"] = CFile::ResizeImageGet(
			$arItem,
			array("width" => 800, "height"=>800),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"][$key]["PREVIEW"]["SRC"] = $arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"][$key]["PREVIEW"]["src"];
		$arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"][$key]["DETAIL"] = CFile::GetFileArray($arItem);		
		$arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"][$key]["DESCRIPTION"] = $arResult["PROPERTIES"]["PHOTO_BOTTOM_DESCRIPTION"]["VALUE"][$key];
		$arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"][$key]["HREF"] = $arResult["PROPERTIES"]["PHOTO_BOTTOM_HREF"]["VALUE"][$key];
	}
?>