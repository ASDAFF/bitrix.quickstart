<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

if(empty($arParams["SHOW_COUNT_LVL1"]))
	$arParams["SHOW_COUNT_LVL1"] = 8;
if(IntVal($arParams["SHOW_COUNT_LVL2"])<0)
	$arParams["SHOW_COUNT_LVL2"] = 11;

$arSizes = array("width"=>250,"height"=>250);
$noPhotoFileID = COption::GetOptionInt('redsign.devfunc', 'no_photo_fileid', 0);
if($noPhotoFileID>0)
{
	$arResult["NO_PHOTO"] = CFile::ResizeImageGet($noPhotoFileID,$arSizes,BX_RESIZE_IMAGE_PROPORTIONAL);
}