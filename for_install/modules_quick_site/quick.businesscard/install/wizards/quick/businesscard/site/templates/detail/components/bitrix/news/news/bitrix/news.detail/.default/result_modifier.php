<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if(!empty($arResult["PROPERTIES"]["PHOTO_TOP"]["VALUE"]))
	foreach($arResult["PROPERTIES"]["PHOTO_TOP"]["VALUE"] as $key=>$arItem)
	{
	  /*$arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"][$arItem] = CFile::ResizeImageGet(
			$arItem,
			array("width" => 800, "height" => 800),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);*/		
		
		$arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"][$arItem] = CFile::GetFileArray($arItem);
		$arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"][$arItem]["DESCRIPTION"] = $arResult["PROPERTIES"]["PHOTO_TOP_DESCRIPTION"]["VALUE"][$key];
	}

if( is_array($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"]) && !empty($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"]) )
{
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
	foreach($arResult["PROPERTIES"]["DOCUMENTS"]["VALUE"] as $ID)
		$arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"][$ID] = $arResult["PROPERTIES"]["DOCUMENTS"]["~ITEMS"][$ID];
}
		
		
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