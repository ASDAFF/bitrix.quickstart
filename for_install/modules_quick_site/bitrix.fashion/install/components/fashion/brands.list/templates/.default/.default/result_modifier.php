<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach($arResult["ITEMS"] as &$arItem)
{
    $arItem["DETAIL_PICTURE"] = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width" => "150", "height" => "320"), BX_RESIZE_IMAGE_PROPORTIONAL, true);
}
?>