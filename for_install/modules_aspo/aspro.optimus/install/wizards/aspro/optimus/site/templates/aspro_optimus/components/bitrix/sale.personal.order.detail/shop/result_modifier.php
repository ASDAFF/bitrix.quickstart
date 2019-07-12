<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$cp = $this->__component;
if (is_object($cp))
{
	CModule::IncludeModule('iblock');

	$hasDiscount = false;
	$hasProps = false;
	$productSum = 0;
	$basketRefs = array();

	$noPict = array(
		'SRC' => SITE_TEMPLATE_PATH.'/images/no_photo_small.png'
	);

	if(is_readable($nPictFile = $_SERVER['DOCUMENT_ROOT'].$noPict['SRC']))
	{
		$noPictSize = getimagesize($nPictFile);
		$noPict['WIDTH'] = $noPictSize[0];
		$noPict['HEIGHT'] = $noPictSize[1];
	}

	foreach($arResult["BASKET"] as $k => &$prod)
	{
		if(floatval($prod['DISCOUNT_PRICE']))
			$hasDiscount = true;
		if(!empty($prod['PROPS']))
			$hasProps = true;

		$productSum += $prod['PRICE'] * $prod['QUANTITY'];

		$basketRefs[$prod['PRODUCT_ID']][] =& $arResult["BASKET"][$k];

		if($prod['DETAIL_PICTURE'])
			$prod['PICTURE'] = $prod['DETAIL_PICTURE_THUMB'];
		elseif($prod['PREVIEW_PICTURE'])
			$prod['PICTURE'] = $prod['PREVIEW_PICTURE_THUMB'];
		else
			$prod['PICTURE'] = $noPict;
	}

	$arResult['HAS_DISCOUNT'] = $hasDiscount;
	$arResult['HAS_PROPS'] = $hasProps;

	$arResult['PRODUCT_SUM_FORMATTED'] = SaleFormatCurrency($productSum, $arResult['CURRENCY']);

	if($img = intval($arResult["DELIVERY"]["STORE_LIST"][$arResult['STORE_ID']]['IMAGE_ID']))
	{

		$pict = CFile::ResizeImageGet($img, array(
			'width' => 150,
			'height' => 90
		), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);

		if(strlen($pict['src']))
			$pict = array_change_key_case($pict, CASE_UPPER);

		$arResult["DELIVERY"]["STORE_LIST"][$arResult['STORE_ID']]['IMAGE'] = $pict;
	}
}
?>