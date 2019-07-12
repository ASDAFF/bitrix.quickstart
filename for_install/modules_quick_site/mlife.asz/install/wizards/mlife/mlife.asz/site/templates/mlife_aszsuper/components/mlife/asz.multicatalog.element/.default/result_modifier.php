<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$arResult["DISCOUNT_INFO"] = array();
$arDiscIds = array();
		if(intval($arResult['DETAIL_PICTURE'])>0){
			$arFileTmp = CFile::ResizeImageGet(
				$arResult['DETAIL_PICTURE'],
				array("width" => 150, "height" => 540),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true, Array("name" => "sharpen", "precision" => 15), false, 80
			);

			$arResult["IMAGE"] = array(
				"SRC" => $arFileTmp["src"],
				'WIDTH' => $arFileTmp["width"],
				'HEIGHT' => $arFileTmp["height"],
				'BIG_SRC' => $arResult['DETAIL_PICTURE']['SRC'],
			);
		}
		if(isset($arResult["DISCOUNT"][$arResult["ID"]]["DISCOUNTID"][0]) && is_array($arParams["TOVAR_DAY"])){
			if(in_array($arResult["DISCOUNT"][$arResult["ID"]]["DISCOUNTID"][0],$arParams["TOVAR_DAY"])){
			$arResult["DAY"] = $arResult["DISCOUNT"][$arResult["ID"]]["DISCOUNTID"][0];
			$arDiscIds[$arResult["DISCOUNT"][$arResult["ID"]]["DISCOUNTID"][0]] = $arResult["DISCOUNT"][$arResult["ID"]]["DISCOUNTID"][0];
			}
		}
$arResult['MORE_PHOTO'] = array();		
if (is_array($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']) && count($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']) > 0)
{

	foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $key => $arFile)
	{
		$arFilter = array("name" => "sharpen", "precision" => 15);
		$arFileTmp = CFile::ResizeImageGet(
			$arFile,
			array("width" => 55, "height" => 55),
			BX_RESIZE_IMAGE_EXACT,
			true, $arFilter
		);
		$arFiletmp = array();
		$arFiletmp['PREVIEW_WIDTH'] = $arFileTmp["width"];
		$arFiletmp['PREVIEW_HEIGHT'] = $arFileTmp["height"];

		$arFiletmp['SRC_PREW'] = $arFileTmp['src'];
		$arFiletmp['SRC'] = CFile::GetPath($arFile);
		$arResult['MORE_PHOTO'][$key] = $arFiletmp;
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
?>