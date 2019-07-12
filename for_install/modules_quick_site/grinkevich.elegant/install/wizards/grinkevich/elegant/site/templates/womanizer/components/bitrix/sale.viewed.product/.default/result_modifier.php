<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach($arResult as $key => $val)
{
	$img = "";
	if ($val["DETAIL_PICTURE"] > 0)
		$img = $val["DETAIL_PICTURE"];
	elseif ($val["PREVIEW_PICTURE"] > 0)
		$img = $val["PREVIEW_PICTURE"];

	$file = CFile::ResizeImageGet($img, array('width'=>$arParams["VIEWED_IMG_WIDTH"], 'height'=>$arParams["VIEWED_IMG_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);

	$val["PICTURE"] = $file;
	$arResult[$key] = $val;
}


$arTmp = $arResult;
$arResult = array();
$arResult["ITEMS"] = $arTmp;


// cache hack to use items list in component_epilog.php
$this->__component->arResult["IDS"] = array();
foreach ($arResult["ITEMS"] as $key => $arElement)
{
	$this->__component->arResult["IDS"][] = $arElement["PRODUCT_ID"];
}
$this->__component->SetResultCacheKeys(array("IDS"));
?>