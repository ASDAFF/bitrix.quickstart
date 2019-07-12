<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?

	if($arParams["DISPLAY_PICTURE"]=="Y"){
		$pictures_id=0;
		if($arResult["DETAIL_PICTURE"] && intval($arResult["DETAIL_PICTURE"]["ID"])>0){
			$pictures_id = $arResult["DETAIL_PICTURE"]["ID"];
		} else {
			if(CModule::IncludeModule("iblock")){
				$res=CIBlockElement::GetList(array(), array("ID"=>$arResult["ID"]), false, false, array("PREVIEW_PICTURE"));
				if($ar=$res->GetNext()) $pictures_id=$ar["PREVIEW_PICTURE"];
			}
		}
		if($pictures_id>0){
			$arFileTmp = CFile::ResizeImageGet(
				$pictures_id,
				array("width" => 150, "height" => 200),
				BX_RESIZE_IMAGE_EXACT,
				true
			);

			$arResult["PREVIEW_PICTURE"]=array();
			$arResult["PREVIEW_PICTURE"]["SRC"]=$arFileTmp['src'];
			$arResult["PREVIEW_PICTURE"]["WIDTH"]=$arFileTmp['width'];
			$arResult["PREVIEW_PICTURE"]["HEIGHT"]=$arFileTmp['height'];

			$arFileTmp = CFile::ResizeImageGet(
				$pictures_id,
				array("width" => 800, "height" => 1000),
				BX_RESIZE_IMAGE_EXACT,
				true
			);

			if(!is_array($arResult["DETAIL_PICTURE"]))
				$arResult["DETAIL_PICTURE"]=array();
			$arResult["DETAIL_PICTURE"]["SRC"]=$arFileTmp['src'];
			$arResult["DETAIL_PICTURE"]["WIDTH"]=$arFileTmp['width'];
			$arResult["DETAIL_PICTURE"]["HEIGHT"]=$arFileTmp['height'];

		}
	}

//echo "<pre>"; print_r($arResult); echo "</pre>";
?>