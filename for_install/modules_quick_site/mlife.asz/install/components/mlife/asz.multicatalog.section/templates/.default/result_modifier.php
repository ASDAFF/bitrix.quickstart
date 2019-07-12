<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0) {
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
	}
}
?>