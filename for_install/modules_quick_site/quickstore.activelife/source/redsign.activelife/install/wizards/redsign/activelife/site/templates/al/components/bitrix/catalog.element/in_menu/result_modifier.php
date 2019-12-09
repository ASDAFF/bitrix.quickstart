<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arAllPicParams = array('MORE_PHOTO_CODE'=>$arParams['ADDITIONAL_PICT_PROP'],'SKU_MORE_PHOTO_CODE'=>$arParams['OFFER_ADDITIONAL_PICT_PROP']);
$arSizes = array('WIDTH'=>'200','HEIGHT'=>'200');
$arResult['IMAGES'] = RSDevFuncOffersExtension::GetAllPictures($arSizes,$arResult,$arAllPicParams);