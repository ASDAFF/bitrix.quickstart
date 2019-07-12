<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if ($arParams['USE_COMPARE'])
{
	$delimiter = strpos($arParams['COMPARE_URL'], '?') ? '&' : '?';

	//$arResult['COMPARE_URL'] = str_replace("#ACTION_CODE#", "ADD_TO_COMPARE_LIST",$arParams['COMPARE_URL']).$delimiter."id=".$arResult['ID'];

	$arResult['COMPARE_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arResult['ID'], array("action", "id")));
}


if (empty($arResult["PROPERTIES"]["TITLE"]['VALUE']))
{
	$arResult['TITLE'] = $arResult['NAME'];
}

if(is_array($arResult["DETAIL_PICTURE"]))
{
	$arFilter = '';
	if($arParams["SHARPEN"] != 0)
	{
		$arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
	}
	$arFileTmp = CFile::ResizeImageGet(
		$arResult['DETAIL_PICTURE'],
		array("width" => $arParams["DISPLAY_DETAIL_IMG_WIDTH"], "height" => $arParams["DISPLAY_DETAIL_IMG_HEIGHT"]),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true, $arFilter
	);

	$arResult['DETAIL_PICTURE_280'] = array(
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
			$arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
		}
		$arFileTmp = CFile::ResizeImageGet(
			$arFile,
			array("width" => $arParams["DISPLAY_MORE_PHOTO_WIDTH"], "height" => $arParams["DISPLAY_MORE_PHOTO_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);

		$arFile['PREVIEW_WIDTH'] = $arFileTmp["width"];
		$arFile['PREVIEW_HEIGHT'] = $arFileTmp["height"];


		$arFile['SRC'] = $arFileTmp['src'];
		$arResult['MORE_PHOTO'][$key]["PREVIEW"] = $arFile;
	}
}

if (CModule::IncludeModule('currency'))
{
	if (isset($arResult['DISPLAY_PROPERTIES']['MINIMUM_PRICE']))
		$arResult['DISPLAY_PROPERTIES']['MINIMUM_PRICE']['DISPLAY_VALUE'] = FormatCurrency($arResult['DISPLAY_PROPERTIES']['MINIMUM_PRICE']['VALUE'], CCurrency::GetBaseCurrency());
	if (isset($arResult['DISPLAY_PROPERTIES']['MAXIMUM_PRICE']))
		$arResult['DISPLAY_PROPERTIES']['MAXIMUM_PRICE']['DISPLAY_VALUE'] = FormatCurrency($arResult['DISPLAY_PROPERTIES']['MAXIMUM_PRICE']['VALUE'], CCurrency::GetBaseCurrency());
}

$this->__component->SetResultCacheKeys(array("ELEMENT"));
$this->__component->SetResultCacheKeys(array("OFFERS_IDS"));

function replaceUrl( $value ){
	if(strpos($value, "href")){
		$str = explode(">", $value);
		$str = explode("<", $str[1]);
		$value = $str[0];
	}
	return $value;
}

foreach($arResult["DISPLAY_PROPERTIES"] as $k => $v){
	if($k != "SIZE" && $k != "MANUFACTURER" &&  $v["PROPERTY_TYPE"] == "E"){
		if( is_array($v["DISPLAY_VALUE"]) ){
			foreach($v["DISPLAY_VALUE"] as $key => $val){
				$arResult["DISPLAY_PROPERTIES"][$k]["DISPLAY_VALUE"][$key] = replaceUrl($val);
			}
		}else{
			$arResult["DISPLAY_PROPERTIES"][$k]["DISPLAY_VALUE"] = replaceUrl($v["DISPLAY_VALUE"]);
		}
	}
}

$this->__component->arResult["ELEMENT"] = $arResult;
?>

