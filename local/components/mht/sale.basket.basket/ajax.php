<?
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main\Loader;

if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
{
	$siteID = trim($_REQUEST['site_id']);
	if ($siteID !== '' && preg_match('/^[a-z0-9_]{2}$/i', $siteID) === 1)
	{
		define('SITE_ID', $siteID);
	}
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

//if (!check_bitrix_sessid() || !$request->isPost())
//	return;

if (!Loader::includeModule('sale') || !Loader::includeModule('catalog'))
	return;

$params = array();

$params = Array
(
    'PATH_TO_ORDER' => 'test_order.php',
    'HIDE_COUPON' => 'N',
    'COLUMNS_LIST' => Array
        (
            0 => 'NAME',
            1 => 'DISCOUNT',
            2 => 'WEIGHT',
            3 => 'PROPS',
            4 => 'DELETE',
            5 => 'DELAY',
            6 => 'TYPE',
            7 => 'PRICE',
            8 => 'QUANTITY',
            9 => 'PREVIEW_PICTURE',
        ),
    'PRICE_VAT_SHOW_VALUE' => 'N',
    'COUNT_DISCOUNT_4_ALL_QUANTITY' => 'N',
    'USE_PREPAYMENT' => 'N',
    'QUANTITY_FLOAT' => 'N',
    'SET_TITLE' => 'Y',
    'ACTION_VARIABLE' => 'action',
    'CACHE_TYPE' => 'A',
    'COMPATIBLE_MODE' => 'N',
    'DEFERRED_REFRESH' => 'Y',
    'PATH_TO_BASKET' => '/personal/cart/',
    'AUTO_CALCULATION' => 'Y',
    'WEIGHT_KOEF' => 1000,
    'WEIGHT_UNIT' => 'кг',
    'COLUMNS_LIST_EXT' => Array
        (
            '0' => 'PREVIEW_PICTURE',
            '1' => 'DISCOUNT',
            '2' => 'DELETE',
            '3' => 'DELAY',
            '4' => 'TYPE',
            '5' => 'SUM',
        ),

    'OFFERS_PROPS' => Array
        (
        ),
    'CORRECT_RATIO' => 'Y',
    'BASKET_IMAGES_SCALING' => 'adaptive',
    'LABEL_PROP' => Array
        (
        ),

    'LABEL_PROP_MOBILE' => Array
        (
        )
,
    'LABEL_PROP_POSITION' => 'top-left',
    'SHOW_DISCOUNT_PERCENT' => 'Y',
    'DISCOUNT_PERCENT_POSITION' => 'bottom-right',
    'BASKET_WITH_ORDER_INTEGRATION' => 'N',
    'BASKET_MAX_COUNT_TO_SHOW' =>5,
    'BASKET_HAS_BEEN_REFRESHED' => 'N',
    'SHOW_RESTORE' => 'Y',
    'USE_GIFTS' => 'Y',
    'GIFTS_PLACE' => 'BOTTOM',
    'GIFTS_PAGE_ELEMENT_COUNT' => 4,
    'TEMPLATE_THEME' => 'blue',
    'DISPLAY_MODE' => 'extended',
    'USE_DYNAMIC_SCROLL' => 'Y',
    'SHOW_FILTER' => 'Y',
    'PRICE_DISPLAY_MODE' => 'Y',
    'TOTAL_BLOCK_DISPLAY' => Array
        (
            0 => 'top',
        ),

    'PRODUCT_BLOCKS_ORDER' => Array
        (
            0 => 'props',
            1 => 'sku',
            2 => 'columns',
        ),
    'USE_PRICE_ANIMATION' => 'Y',
    'USE_ENHANCED_ECOMMERCE' => 'N',
    'DATA_LAYER_NAME' => 'dataLayer',
    'BRAND_PROPERTY' => '',
);

global $APPLICATION;




$APPLICATION->IncludeComponent(
	'bitrix:sale.basket.basket',
	'.default',
	$params
);