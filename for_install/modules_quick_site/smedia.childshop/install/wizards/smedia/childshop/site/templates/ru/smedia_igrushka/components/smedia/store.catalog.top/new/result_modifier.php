<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->__component->arResult["PROPS"] = array();
$this->__component->arResult["IDS"] = array();
$this->__component->arResult["OFFERS_IDS"] = array();
foreach ($arResult['ITEMS'] as $key => $arElement)
{
	if(is_array($arElement["DETAIL_PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array(array("name" => "sharpen", "precision" => $arParams["SHARPEN"]));
		}
		
		$arFileTmp = CFile::ResizeImageGet(
			$arElement['DETAIL_PICTURE'],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);
		
		$arResult["ITEMS"][$key]["PREVIEW_IMG"] = array(
			"SRC" => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
	$this->__component->arResult["IDS"][] = $arElement["ID"];	
	$this->__component->arResult["PROPS"][$arElement["ID"]] = $arElement["PRODUCT_PROPERTIES"];
		
	if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])){
		foreach($arElement["OFFERS"] as $arOffer){
			$this->__component->arResult["OFFERS_IDS"][] = $arOffer["ID"];
		}
	}
}
$this->__component->SetResultCacheKeys(array("IDS"));
$this->__component->SetResultCacheKeys(array("OFFERS_IDS"));
$this->__component->SetResultCacheKeys(array("PROPS"));
/*$arResult["ROWS"] = array();

foreach ($arResult['ITEMS'] as $key => $arElement)
{
	$arRow = array_splice($arResult["ITEMS"], 0, $arParams["LINE_ELEMENT_COUNT"]);
	while(count($arRow) < $arParams["LINE_ELEMENT_COUNT"])
		$arRow[]=false;
	if(!empty($arRow[0]))
		$arResult["ROWS"][]=$arRow;
}*/
?>
