<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
	foreach($arResult["ITEMS"] as $k=>$arItem){
		if($arParams["DISPLAY_PICTURE"]=="Y"){

			if($arItem["PREVIEW_PICTURE"] && intval($arItem["PREVIEW_PICTURE"]["ID"])>0){
				$arFileTmp = CFile::ResizeImageGet(
					$arItem["PREVIEW_PICTURE"]["ID"],
					array("width" => 80, "height" => 100),
					BX_RESIZE_IMAGE_EXACT,
					true
				);
				$arResult["ITEMS"][$k]["PREVIEW_PICTURE"]["SRC"]=$arFileTmp['src'];
				$arResult["ITEMS"][$k]["PREVIEW_PICTURE"]["WIDTH"]=$arFileTmp['width'];
				$arResult["ITEMS"][$k]["PREVIEW_PICTURE"]["HEIGHT"]=$arFileTmp['height'];
			}
		}
	}

//echo "<pre>"; print_r($arResult["ITEMS"]); echo "</pre>";

?>