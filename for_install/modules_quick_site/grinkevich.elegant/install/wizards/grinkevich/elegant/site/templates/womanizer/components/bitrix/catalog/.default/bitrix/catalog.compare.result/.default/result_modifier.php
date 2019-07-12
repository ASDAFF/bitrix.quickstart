<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$arResult["PROP_ROWS"] = array();
foreach($arParams["PROPERTY_CODE"] as $key => $propCode)
{
	if(empty($arResult["SHOW_PROPERTIES"][$propCode]["ID"]) && empty($arResult["DELETED_PROPERTIES"][$propCode]["ID"]))
	{
		unset($arParams["PROPERTY_CODE"][$key]);
		unset($arResult["SHOW_PROPERTIES"][$propCode]);
	}
}
while(count($arParams["PROPERTY_CODE"])>0)
{
	$arRow = array_splice($arParams["PROPERTY_CODE"], 0, 3);
	while(count($arRow) < 3)
		$arRow[]=false;
	$arResult["PROP_ROWS"][]=$arRow;
}

$arResult["OFFERS_PROP_ROWS"] = array();
foreach($arParams["OFFERS_PROPERTY_CODE"] as $key => $propCode)
{
	if(empty($arResult["SHOW_OFFER_PROPERTIES"][$propCode]["ID"]) && empty($arResult["DELETED_OFFER_PROPERTIES"][$propCode]["ID"]))
	{
		unset($arParams["OFFERS_PROPERTY_CODE"][$key]);
		unset($arResult["SHOW_OFFER_PROPERTIES"][$propCode]);
	}
}
while(count($arParams["OFFERS_PROPERTY_CODE"])>0)
{
	$arRow = array_splice($arParams["OFFERS_PROPERTY_CODE"], 0, 3);
	while(count($arRow) < 3)
		$arRow[]=false;
	$arResult["OFFERS_PROP_ROWS"][]=$arRow;
}

foreach ($arResult['ITEMS'] as $key => $arElement)
{
	if(is_array($arElement["FIELDS"]["DETAIL_PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
		}
		$arFileTmp = CFile::ResizeImageGet(
			$arElement["FIELDS"]['DETAIL_PICTURE'],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);

		$arResult['ITEMS'][$key]["FIELDS"]["DETAIL_PICTURE"]['PREVIEW_IMG'] = array(
			'SRC' => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
}

?>