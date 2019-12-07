<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if(!empty($arResult["PROPERTIES"]["ACTIVE_FROM"]["VALUE"]))
	$arResult["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arResult["PROPERTIES"]["ACTIVE_FROM"]["VALUE"], CSite::GetDateFormat()));

if(!empty($arResult["PROPERTIES"]["ACTIVE_TO"]["VALUE"]))
	$arResult["DISPLAY_ACTIVE_TO"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arResult["PROPERTIES"]["ACTIVE_TO"]["VALUE"], CSite::GetDateFormat()));

if(!empty($arResult["PROPERTIES"]["PHOTO_TOP"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["PHOTO_TOP"]["VALUE"] as $key => $arItem)
	{
	  /*$arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"][$arItem] = CFile::ResizeImageGet(
			$arItem,
			array("width" => 800, "height"=> 800),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);*/
		
		$arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"][$arItem] = CFile::GetFileArray($arItem);
		$arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"][$arItem]["DESCRIPTION"] = $arResult["PROPERTIES"]["PHOTO_TOP_DESCRIPTION"]["VALUE"][$key];
	}


// WORKS

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


// STAFF

$res = CIBlockElement::GetList(Array(),
		Array(
				"ID"=>$arResult["PROPERTIES"]["MORE_STAFF"]["VALUE"],
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

	$arResult["MORE_STAFF"][$arFields["ID"]] = $arFields;
}

if(!empty($arResult["PROPERTIES"]["MORE_STAFF"]["VALUE"]) && is_array($arResult["PROPERTIES"]["MORE_STAFF"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["MORE_STAFF"]["VALUE"] as $ID)
		if(!empty($arResult["MORE_STAFF"][$ID]))
			$arResult["PROPERTIES"]["MORE_STAFF"]["ITEMS"][] = $arResult["MORE_STAFF"][$ID];


// PRODUCTS

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


// DOCUMENTS

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

if( is_array($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"]) && !empty($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"]) )
	foreach($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"] as $ID)
		$arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"][$ID] = $arResult["PROPERTIES"]["DOCUMENTS"]["~ITEMS"][$ID];

	
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