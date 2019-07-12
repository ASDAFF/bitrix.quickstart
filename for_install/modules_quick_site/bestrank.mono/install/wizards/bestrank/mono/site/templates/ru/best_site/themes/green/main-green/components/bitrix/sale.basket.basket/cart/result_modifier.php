<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
	CModule::IncludeModule("iblock");	
	$arIDs = array();
	foreach($arResult["ITEMS"] as $k=>$v){
		foreach($v as $k1=> $arBasketItems){
			$arIDs[$arBasketItems["PRODUCT_ID"]] = "";
		}
	}
	$res=CIBlockElement::GetList(array(), array("ID"=>array_keys($arIDs)), false, false, array());
	while($ar=$res->GetNext()){
		$id=0;
		if($ar["PREVIEW_PICTURE"])
			$id= $ar["PREVIEW_PICTURE"];
		elseif($ar["DETAIL_PICTURE"])
			$id= $ar["DETAIL_PICTURE"];
		if($id>0){
			$arFileTmp = CFile::ResizeImageGet(
				$id,
				array("width" => 160, "height" => 120),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);			
			$arIDs[$ar["ID"]]=array(
				'SRC' => $arFileTmp["src"],
				'WIDTH' => $arFileTmp["width"],
				'HEIGHT' => $arFileTmp["height"],
			);
		}
	}


	foreach($arResult["ITEMS"] as $k=>$v){
		foreach($v as $k1=> $arBasketItems){
			foreach($arIDs as $id=>$pic){
				if($id==$arBasketItems["PRODUCT_ID"]){
					$arResult["ITEMS"][$k][$k1]["DETAIL_PICTURE"]=$pic;
					break;
				}
			}
		}
	}

?>