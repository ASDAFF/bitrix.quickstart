<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("iblock");
//PR($arParams);

$arFilter = Array(
	"IBLOCK_TYPE"=>$arParams["IBLOCK_TYPE"],
	"IBLOCK_ID"=>$arParams["IBLOCK_ID"], 
   	"ACTIVE"=>"Y", 
);
$arOrder = array(
	$arParams["SORT_BY1"] => $arParams["SORT_ORDER1"]
);
$res = CIBlockElement::GetList($arOrder, $arFilter, false, array("nPageSize"=>$arParams["NEWS_COUNT"]), Array("ID","NAME","PREVIEW_PICTURE"));

$arImgaeSizes = array(
	"PREVIEW_IMG" => array("width" => $arParams["SMALL_WIDTH"], "height" => $arParams["SMALL_HEIGHT"]),
	//"SMALL_IMG" => array("width" => $arParams["SMALL_WIDTH_SMALL"], "height" => $arParams["SMALL_HEIGHT_SMALL"]),
	"BIG_IMG" =>  array("width" => $arParams["SMALL_WIDTH_BIG"], "height" => $arParams["SMALL_HEIGHT_BIG"]),
);

while($result = $res->Fetch())
{
	if($result["PREVIEW_PICTURE"])
	{
		foreach ($arImgaeSizes as $code => $size) {
			$arFileTmp = CFile::ResizeImageGet(
     			$result["PREVIEW_PICTURE"],
     			$size,
     			BX_RESIZE_IMAGE_EXACT,
     			false
     		);
     		$arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$arFileTmp["src"]);

    		$result[$code] = array(
         	 	"SRC" => $arFileTmp["src"],
         	 	"WIDTH" => IntVal($arSize[0]),
          	 	"HEIGHT" => IntVal($arSize[1]),
   			);
		}
		$result["DEFAULT_IMG"] = CFile::GetPath($result["PREVIEW_PICTURE"]);
	}
	//PR($result["PREVIEW_IMG"]);
	$arItems[] = $result;
}

$arResult["ITEMS"] = $arItems;
//PR($arResult["ITEMS"]);
$this->IncludeComponentTemplate();
?>