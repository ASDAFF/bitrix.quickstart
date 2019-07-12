<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//$arResult["PREVIEW_TEXT"] = strip_tags($arResult["PREVIEW_TEXT"]);
$this->__component->arResult["OFFERS_IDS"] = array();

if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])){
	foreach($arResult["OFFERS"] as $arOffer){
		$this->__component->arResult["OFFERS_IDS"][] = $arOffer["ID"];
	}
}

if ($arParams['USE_COMPARE'])
{
	$delimiter = strpos($arParams['COMPARE_URL'], '?') ? '&' : '?';

	//$arResult['COMPARE_URL'] = str_replace("#ACTION_CODE#", "ADD_TO_COMPARE_LIST",$arParams['COMPARE_URL']).$delimiter."id=".$arResult['ID'];

	$arResult['COMPARE_URL'] = htmlspecialchars($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arResult['ID'], array("action", "id")));
}

if(is_array($arResult["DETAIL_PICTURE"]))
{
	$arFilter = '';
	if($arParams["SHARPEN"] != 0)
	{
		$arFilter = array(array("name" => "sharpen", "precision" => $arParams["SHARPEN"]));
	}
	$arFileTmp = CFile::ResizeImageGet(
		$arResult['DETAIL_PICTURE'],
		array("width" => $arParams["DISPLAY_DETAIL_IMG_WIDTH"], "height" => $arParams["DISPLAY_DETAIL_IMG_HEIGHT"]),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true, $arFilter
	);

	$arResult['DETAIL_PICTURE_350'] = array(
		'SRC' => $arFileTmp["src"],
		'WIDTH' => $arFileTmp["width"],
		'HEIGHT' => $arFileTmp["height"],
	);
	
	$arFileTmp = CFile::ResizeImageGet(
			$arResult['DETAIL_PICTURE'],
			array("width" => $arParams["DISPLAY_MORE_PHOTO_WIDTH"], "height" => $arParams["DISPLAY_MORE_PHOTO_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);
	$arResult['DETAIL_PICTURE_MF'] = array(
		'SRC' => $arFileTmp["src"],
		'WIDTH' => $arFileTmp["width"],
		'HEIGHT' => $arFileTmp["height"],
	);
}

if (is_array($arResult['MORE_PHOTO']) && count($arResult['MORE_PHOTO']) > 0)
{
	unset($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']);

	foreach ($arResult['MORE_PHOTO'] as $key => $arFile)
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array(array("name" => "sharpen", "precision" => $arParams["SHARPEN"]));
		}
		$arFileTmp = CFile::ResizeImageGet(
			$arFile,
			array("width" => $arParams["DISPLAY_MORE_PHOTO_WIDTH"], "height" => $arParams["DISPLAY_MORE_PHOTO_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);

		$arFile['PREVIEW_WIDTH'] = $arFileTmp["width"];
		$arFile['PREVIEW_HEIGHT'] = $arFileTmp["height"];
		$arFile['SRC_PREVIEW'] = $arFileTmp['src'];
				
		$arResult['MORE_PHOTO'][$key] = $arFile;
	}
}
$this->__component->SetResultCacheKeys(array("OFFERS_IDS"));
?>