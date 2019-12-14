<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
foreach ($arResult['ITEMS'] as $key => $arItem) {	
	if(
			$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_TYPE']]['VALUE_XML_ID']=='video' &&
			$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_MP4']]['VALUE']!='' &&
			$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_WEBM']]['VALUE']!='' 
		) {
			$arResult['ITEMS'][$key]['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_MP4']]['FILE_PATH_MP4'] = CFile::GetPath($arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_MP4']]['VALUE']);
			$arResult['ITEMS'][$key]['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_WEBM']]['FILE_PATH_WEBM'] = CFile::GetPath($arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_WEBM']]['VALUE']);
		}
}