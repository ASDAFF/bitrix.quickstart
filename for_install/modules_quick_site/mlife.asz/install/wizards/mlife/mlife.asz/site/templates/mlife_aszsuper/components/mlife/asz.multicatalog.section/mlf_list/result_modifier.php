<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0) {
	$arResult["DISCOUNT_INFO"] = array();
	$arDiscIds = array();
	foreach($arResult['ITEMS'] as &$item){
		if(intval($item['DETAIL_PICTURE'])>0){
		$arFileTmp = CFile::ResizeImageGet(
				$item['DETAIL_PICTURE'],
				array("width" => 100, "height" => 100),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true, Array("name" => "sharpen", "precision" => 15), false, 80
			);

			$item["IMAGE"] = array(
				"SRC" => $arFileTmp["src"],
				'WIDTH' => $arFileTmp["width"],
				'HEIGHT' => $arFileTmp["height"],
				
			);
		}
		if(isset($arResult["DISCOUNT"][$item["ID"]]["DISCOUNTID"][0]) && is_array($arParams["TOVAR_DAY"])){
			if(in_array($arResult["DISCOUNT"][$item["ID"]]["DISCOUNTID"][0],$arParams["TOVAR_DAY"])){
			$item["DAY"] = $arResult["DISCOUNT"][$item["ID"]]["DISCOUNTID"][0];
			$arDiscIds[$arResult["DISCOUNT"][$item["ID"]]["DISCOUNTID"][0]] = $arResult["DISCOUNT"][$item["ID"]]["DISCOUNTID"][0];
			}
		}
	}
	if(!empty($arDiscIds)){
		
		//получаем скидки
		$discount = \Mlife\Asz\DiscountTable::getList(
			array(
				'select' => array("*"),
				'filter' => array("ACTIVE"=>"Y", "ID"=>$arDiscIds),
			)
		);
		while($arDiscountdb = $discount->Fetch()){
			$arResult["DISCOUNT_INFO"][$arDiscountdb["ID"]] = $arDiscountdb;
		}
		
	}

}



?>