<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!$arResult['MODULES']['catalog'])
	return;

$arDefaultParams = array(
	'LABEL_PROP' => '-',
	'OFFER_ADD_PICT_PROP' => '-',
	'OFFER_TREE_PROPS' => array('-'),
	'PRODUCT_SUBSCRIPTION' => 'N',
	'SHOW_DISCOUNT_PERCENT' => 'N',
	'SHOW_OLD_PRICE' => 'N',
	'SHOW_MAX_QUANTITY' => 'N',
	'DISPLAY_COMPARE' => 'N',
	'MESS_BTN_BUY' => '',
	'MESS_BTN_ADD_TO_BASKET' => '',
	'MESS_BTN_SUBSCRIBE' => '',
	'MESS_BTN_COMPARE' => '',
	'MESS_NOT_AVAILABLE' => '',
	'USE_VOTE_RATING' => 'N',
	'VOTE_DISPLAY_AS_RATING' => 'rating',
	'USE_COMMENTS' => 'N',
	'BLOG_USE' => 'N',
	'VK_USE' => 'N',
	'FB_USE' => 'N',
);
$arParams = array_merge($arDefaultParams, $arParams);

if ('Y' != $arParams['PRODUCT_DISPLAY_MODE'])
	$arParams['PRODUCT_DISPLAY_MODE'] = 'N';

$arParams['LABEL_PROP'] = trim($arParams['LABEL_PROP']);
if ('-' == $arParams['LABEL_PROP'])
	$arParams['LABEL_PROP'] = '';
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ('-' == $arParams['OFFER_ADD_PICT_PROP'])
	$arParams['OFFER_ADD_PICT_PROP'] = '';
if (!is_array($arParams['OFFER_TREE_PROPS']))
	$arParams['OFFER_TREE_PROPS'] = array($arParams['OFFER_TREE_PROPS']);
foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
{
	$value = (string)$value;
	if ('' == $value || '-' == $value)
		unset($arParams['OFFER_TREE_PROPS'][$key]);
}
if (empty($arParams['OFFER_TREE_PROPS']) && isset($arParams['OFFERS_CART_PROPERTIES']) && is_array($arParams['OFFERS_CART_PROPERTIES']))
{
	$arParams['OFFER_TREE_PROPS'] = $arParams['OFFERS_CART_PROPERTIES'];
	foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
	{
		$value = (string)$value;
		if ('' == $value || '-' == $value)
			unset($arParams['OFFER_TREE_PROPS'][$key]);
	}
}
if ('Y' != $arParams['PRODUCT_SUBSCRIPTION'])
	$arParams['PRODUCT_SUBSCRIPTION'] = 'N';
if ('Y' != $arParams['SHOW_DISCOUNT_PERCENT'])
	$arParams['SHOW_DISCOUNT_PERCENT'] = 'N';
if ('Y' != $arParams['SHOW_OLD_PRICE'])
	$arParams['SHOW_OLD_PRICE'] = 'N';
if ('Y' != $arParams['SHOW_MAX_QUANTITY'])
	$arParams['SHOW_MAX_QUANTITY'] = 'N';
if ('Y' != $arParams['DISPLAY_COMPARE'])
	$arParams['DISPLAY_COMPARE'] = 'N';
$arParams['DISPLAY_COMPARE'] = 'N';

$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
if ('Y' != $arParams['USE_VOTE_RATING'])
	$arParams['USE_VOTE_RATING'] = 'N';
if ('vote_avg' != $arParams['VOTE_DISPLAY_AS_RATING'])
	$arParams['VOTE_DISPLAY_AS_RATING'] = 'rating';
if ('Y' != $arParams['USE_COMMENTS'])
	$arParams['USE_COMMENTS'] = 'N';
if ('Y' != $arParams['BLOG_USE'])
	$arParams['BLOG_USE'] = 'N';
if ('Y' != $arParams['VK_USE'])
	$arParams['VK_USE'] = 'N';
if ('Y' != $arParams['FB_USE'])
	$arParams['FB_USE'] = 'N';
if ('Y' == $arParams['USE_COMMENTS'])
{
	if ('N' == $arParams['BLOG_USE'] && 'N' == $arParams['VK_USE'] && 'N' == $arParams['FB_USE'])
		$arParams['USE_COMMENTS'] = 'N';
}

$arEmptyPreview = false;
$strEmptyPreview = $this->GetFolder().'/images/no_photo.png';
if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
{
	$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
	if (!empty($arSizes))
	{
		$arEmptyPreview = array(
			'SRC' => $strEmptyPreview,
			'WIDTH' => intval($arSizes[0]),
			'HEIGHT' => intval($arSizes[1])
		);
	}
	unset($arSizes);
}
unset($strEmptyPreview);

$arSKUPropList = array();
$arSKUPropIDs = array();
$arSKUPropKeys = array();
$boolSKU = false;
$strBaseCurrency = '';
$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);

$arResult['SHOW_SLIDER'] = false;
$arResult['CHECK_QUANTITY'] = false;
if (!isset($arResult['CATALOG_MEASURE_RATIO']))
	$arResult['CATALOG_MEASURE_RATIO'] = 1;
if (!isset($arResult['CATALOG_QUANTITY']))
	$arResult['CATALOG_QUANTITY'] = 0;
$arResult['CATALOG_QUANTITY'] = (
	0 < $arResult['CATALOG_QUANTITY'] && is_float($arResult['CATALOG_MEASURE_RATIO'])
	? floatval($arResult['CATALOG_QUANTITY'])
	: intval($arResult['CATALOG_QUANTITY'])
);
$arResult['CATALOG'] = false;
$arResult['LABEL'] = false;
if (!isset($arResult['CATALOG_SUBSCRIPTION']) || 'Y' != $arResult['CATALOG_SUBSCRIPTION'])
	$arResult['CATALOG_SUBSCRIPTION'] = 'N';

if ('' != $arParams['LABEL_PROP'] && isset($arResult['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']]))
{
	$arResult['LABEL'] = true;
	$arProp = $arResult['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']];
	if ('N' == $arProp['MULTIPLE'] && 'L' == $arProp['PROPERTY_TYPE'] && 'C' == $arProp['LIST_TYPE'])
	{
		$arResult['LABEL_VALUE'] = $arProp['NAME'];
	}
	else
	{
		$arResult['LABEL_VALUE'] = (is_array($arProp['DISPLAY_VALUE'])
			? implode(' / ', $arProp['DISPLAY_VALUE'])
			: $arProp['DISPLAY_VALUE']
		);
	}
	unset($arResult['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']]);
}

if ($arResult['MODULES']['catalog'])
{
	$arResult['CATALOG'] = true;
	if (!isset($arResult['CATALOG_TYPE']))
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;

	switch ($arResult['CATALOG_TYPE'])
	{
		case CCatalogProduct::TYPE_SET:
			$arResult['OFFERS'] = array();
			$arResult['CATALOG_MEASURE_RATIO'] = 1;
			$arResult['CATALOG_QUANTITY'] = 0;
			$arResult['CHECK_QUANTITY'] = false;
			break;
		case CCatalogProduct::TYPE_PRODUCT:
		default:
			$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
			break;
	}
}
else
{
	$arResult['CATALOG_TYPE'] = 0;
	$arResult['OFFERS'] = array();
}

if (!is_array($arResult['PREVIEW_PICTURE']))
	$arResult['PREVIEW_PICTURE'] = $arResult['DETAIL_PICTURE'];
if (empty($arResult['PREVIEW_PICTURE']))
	$arResult['PREVIEW_PICTURE'] = $arEmptyPreview;

if (!empty($arResult["MORE_PHOTO"]))
{
    $arResult['SHOW_SLIDER'] = true;
    $arResult["MORE_PHOTO_COUNT"] = count($arResult["MORE_PHOTO"]);
}

if (!empty($arResult['DISPLAY_PROPERTIES']))
{
	foreach ($arResult['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
	{
		if ('F' == $arDispProp['PROPERTY_TYPE'])
			unset($arResult['DISPLAY_PROPERTIES'][$propKey]);
	}
}

if (is_array($arResult['PROPERTIES']['MANUALS']['VALUE'])) {
    foreach ($arResult['PROPERTIES']['MANUALS']['VALUE'] as $value) {
        $arResult['MANUALS_FILES'][] = CFile::GetFileArray($value);
    }
}

$ufRes = CIBlockSection::GetList(Array(SORT=>"ASC"), $arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ID"=>$arResult["SECTION"]["PATH"][0]["ID"]), true,$arSelect=Array("UF_CERTS")); 

while($certsAr = $ufRes->GetNext()){
    foreach ($certsAr['UF_CERTS'] as $cert) {
        $arResult['CERTS'][] = CFile::GetFileArray($cert);
    }
}

$arResult['SKU_PROPS'] = $arSKUPropList;

$arResult['GOOD_FEATURES'] = getGoodFeaturesHighload(
	array(
		'ID' => 5,
		'NAME' => 'Features',
		'TABLE_NAME' => $arResult['PROPERTIES']['FEATURES']['USER_TYPE_SETTINGS']['TABLE_NAME']
	),
	$arResult['PROPERTIES']['FEATURES']['VALUE'] ? $arResult['PROPERTIES']['FEATURES']['VALUE'] : array(),
	array('UF_NAME', 'UF_FILE'),
	array(
		'height' => 300,
		'width' => 300,
	)
);
?>
