<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(count($arResult["ITEMS"]) > 0){
foreach($arResult["ITEMS"]  as $key => $arItem)
{
	$arItem['bDiscount'] = (is_array($arItem['PRICE']['DISCOUNT']) && count($arItem['PRICE']['DISCOUNT']) > 0);
	
	if ($arItem['bDiscount'])
		$arItem['PRICE']['DISCOUNT_PRICE_F'] = CurrencyFormat(
			$arItem['PRICE']['DISCOUNT_PRICE'], 
			$arItem['PRICE']['DISCOUNT']['CURRENCY']
		);
	
	if ($arItem['PRICE']['PRICE']['PRICE'])
		$arItem['PRICE']['PRICE_F'] = CurrencyFormat(
			$arItem['PRICE']['PRICE']['PRICE'], 
			$arItem['PRICE']['PRICE']['CURRENCY']
		);
	
	$arItem['DESCRIPTION'] = '';
	
	if ($arItem['PREVIEW_TEXT'])
	{
		$arItem['DESCRIPTION'] = 
			$arItem['PREVIEW_TEXT_TYPE'] == 'html' 
			? $arItem['PREVIEW_TEXT'] 
			: htmlspecialchars($arItem['PREVIEW_TEXT']);
	}
	elseif ($arItem['DETAIL_TEXT'])
	{
		$arItem['DESCRIPTION'] = 
			$arItem['DETAIL_TEXT_TYPE'] == 'html' 
			? $arItem['DETAIL_TEXT'] 
			: htmlspecialchars($arItem['DETAIL_TEXT']);
	}
	
	if(is_array($arItem["PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array(array("name" => "sharpen", "precision" => $arParams["SHARPEN"]));
		}
		$arFileTmp = CFile::ResizeImageGet(
			$arItem['PICTURE'],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);
	
		$arItem['PICTURE_PREVIEW'] = array(
			'SRC' => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
	$arResult["ITEMS"][$key] = $arItem;
}
}
?>