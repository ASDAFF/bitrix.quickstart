<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arResult['bDiscount'] = (is_array($arResult['PRICE']['DISCOUNT']) && count($arResult['PRICE']['DISCOUNT']) > 0);

if ($arResult['bDiscount'])
	$arResult['PRICE']['DISCOUNT_PRICE_F'] = CurrencyFormat(
		$arResult['PRICE']['DISCOUNT_PRICE'], 
		$arResult['PRICE']['DISCOUNT']['CURRENCY']
	);

if ($arResult['PRICE']['PRICE']['PRICE'])
	$arResult['PRICE']['PRICE_F'] = CurrencyFormat(
		$arResult['PRICE']['PRICE']['PRICE'], 
		$arResult['PRICE']['PRICE']['CURRENCY']
	);

$arResult['DESCRIPTION'] = '';

if ($arResult['PREVIEW_TEXT'])
{
	$arResult['DESCRIPTION'] = 
		$arResult['PREVIEW_TEXT_TYPE'] == 'html' 
		? $arResult['PREVIEW_TEXT'] 
		: htmlspecialchars($arResult['PREVIEW_TEXT']);
}
elseif ($arResult['DETAIL_TEXT'])
{
	$arResult['DESCRIPTION'] = 
		$arResult['DETAIL_TEXT_TYPE'] == 'html' 
		? $arResult['DETAIL_TEXT'] 
		: htmlspecialchars($arResult['DETAIL_TEXT']);
}

if(is_array($arResult["PICTURE"]))
{
	$arFilter = '';
	if($arParams["SHARPEN"] != 0)
	{
		$arFilter = array(array("name" => "sharpen", "precision" => $arParams["SHARPEN"]));
	}
	$arFileTmp = CFile::ResizeImageGet(
		$arResult['PICTURE'],
		array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true, $arFilter
	);
	$arResult['PICTURE_PREVIEW'] = array(
		'SRC' => $arFileTmp["src"],
		'WIDTH' => $arFileTmp["width"],
		'HEIGHT' => $arFileTmp["height"],
	);
}
?>