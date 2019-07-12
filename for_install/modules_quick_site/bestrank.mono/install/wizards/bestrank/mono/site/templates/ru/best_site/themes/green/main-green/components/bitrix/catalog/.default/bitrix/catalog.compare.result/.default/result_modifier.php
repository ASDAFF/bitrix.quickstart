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

$filter_name = ($arParams["USE_FILTER"]=="Y" && strlen($arParams["FILTER_NAME"])>0) ? $arParams["FILTER_NAME"] : "arrFilter";

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

	if($arElement["PROPERTIES"]["NEWPRODUCT"]["VALUE_XML_ID"]=="Y"){
		$arResult['ITEMS'][$key]["NEWPRODUCT"]=true;
	}
	if($arElement["PROPERTIES"]["SPECIALOFFER"]["VALUE_XML_ID"]=="Y"){
		$arResult['ITEMS'][$key]["SPECIALOFFER"]=true;
	}
	foreach($arElement["DISPLAY_PROPERTIES"] as $code=>$arProperty){
		//echo "<pre>"; print_r($arProperty); echo "</pre>";
		if(is_array($arProperty["DISPLAY_VALUE"])){
			$display_value=array();
			foreach($arProperty["DISPLAY_VALUE"] as  $k=>$v){
				$display_value[] =  str_replace(array("#SMART_FILTER_NAME#", "#PROPERTY_ID#", "#CRC32#"), array($filter_name, $arProperty["ID"], abs(crc32($arProperty["VALUE"][$k]))), $v);
			}
		}else{
			$display_value =  str_replace(array("#SMART_FILTER_NAME#", "#PROPERTY_ID#", "#CRC32#"), array($filter_name, $arProperty["ID"], abs(crc32($arProperty["VALUE"][$k]))), $arProperty["DISPLAY_VALUE"]);
		}
		$arResult['ITEMS'][$key]["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]=$display_value;
	} 

}
unset($arResult["SHOW_PROPERTIES"]["NEWPRODUCT"]);
unset($arResult["SHOW_PROPERTIES"]["SPECIALOFFER"]);

//echo "<pre>"; print_r($arResult['ITEMS']); echo "</pre>";

?>