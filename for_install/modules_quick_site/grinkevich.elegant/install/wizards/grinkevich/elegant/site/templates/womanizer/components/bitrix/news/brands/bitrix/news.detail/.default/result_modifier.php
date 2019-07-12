<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if( is_array($arResult["DETAIL_PICTURE"]) ){
	$arFileTmp = CFile::ResizeImageGet(
		$arResult["DETAIL_PICTURE"],
		array("width" => "150", "height" => "150"),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true, $arFilter
	);
	$arResult["PIC"] = $arFileTmp;
}
?>