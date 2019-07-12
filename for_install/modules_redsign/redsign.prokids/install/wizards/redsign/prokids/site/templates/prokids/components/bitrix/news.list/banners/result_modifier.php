<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(empty($arParams['RSGOPRO_CHANGE_SPEED']))
	$arParams['RSGOPRO_CHANGE_SPEED'] = 2000;

if(empty($arParams['RSGOPRO_CHANGE_DELAY']))
	$arParams['RSGOPRO_CHANGE_DELAY'] = 8000;

if(IntVal($arParams['RSGOPRO_BANNER_HEIGHT'])<1)
	$arParams['RSGOPRO_BANNER_HEIGHT'] = 0;
foreach($arResult['ITEMS'] as $key => $arItem)
{
	if(IntVal($arParams['RSGOPRO_BANNER_HEIGHT'])<1)
	{
		if($arItem['DETAIL_PICTURE']['HEIGHT']>$arParams['RSGOPRO_BANNER_HEIGHT'])
			$arParams['RSGOPRO_BANNER_HEIGHT'] = $arItem['DETAIL_PICTURE']['HEIGHT'];
	}
	
	if(
		$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_TYPE']]['VALUE_XML_ID']=='video' &&
		$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_MP4']]['VALUE']!='' &&
		$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_WEBM']]['VALUE']!='' &&
		$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_PIC']]['VALUE']!=''
	) {
		$arResult['ITEMS'][$key]['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_MP4']]['FILE_PATH_MP4'] = CFile::GetPath($arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_MP4']]['VALUE']);
		$arResult['ITEMS'][$key]['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_WEBM']]['FILE_PATH_WEBM'] = CFile::GetPath($arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_WEBM']]['VALUE']);
		$arResult['ITEMS'][$key]['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_PIC']]['FILE_PATH_PIC'] = CFile::GetPath($arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_PIC']]['VALUE']);
	}
}