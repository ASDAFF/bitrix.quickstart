<?
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);

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

if (!Loader::includeModule('sale') || !Loader::includeModule('catalog'))
    return;

$APPLICATION->IncludeComponent(
    "custom:sale.bestsellers",
    ".default",
    array(
        "LINE_ELEMENT_COUNT" => $request['lineElementCount'] ?  $request['lineElementCount'] : 4,
        "PAGE_ELEMENT_COUNT" => $request['count'],
        "ACTION_VARIABLE" => "action",
        "ADDITIONAL_PICT_PROP_2" => "MORE_PHOTO",
        "ADDITIONAL_PICT_PROP_3" => "MORE_PHOTO",
        "ADD_PROPERTIES_TO_BASKET" => "N",
        "AJAX_MODE" => "Y",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "BASKET_URL" => "/personal/cart/",
        "BY" => "AMOUNT",
        "CACHE_TIME" => "86400",
        "CACHE_TYPE" => "A",
        "CART_PROPERTIES_2" => array(
            0 => "SALELEADER,,",
        ),
        "CART_PROPERTIES_3" => array(
            0 => ",",
        ),
        "COMPONENT_TEMPLATE" => ".default",
        "CONVERT_CURRENCY" => "N",
        "DETAIL_URL" => "",
        "DISPLAY_COMPARE" => "Y",
        "FILTER" => array(
            0 => "N",
            1 => "P",
            2 => "F",
        ),
        "HIDE_NOT_AVAILABLE" => "N",
        "LABEL_PROP_2" => "-",
        "LABEL_PROP_3" => "-",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PERIOD" => "2",
        "PRICE_CODE" => array(
            0 => "BASE",
        ),
        "PRICE_VAT_INCLUDE" => "Y",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_SUBSCRIPTION" => "N",
        "PROPERTY_CODE_2" => array(
            0 => "KEYWORDS",
            1 => "META_DESCRIPTION",
            2 => ",",
            3 => "",
        ),
        "PROPERTY_CODE_3" => array(
            0 => ",",
        ),
        "SHOW_DISCOUNT_PERCENT" => "Y",
        "SHOW_IMAGE" => "Y",
        "SHOW_NAME" => "Y",
        "SHOW_OLD_PRICE" => "Y",
        "SHOW_PRICE_COUNT" => "1",
        "SHOW_PRODUCTS_2" => "Y",
        "SHOW_PRODUCTS_3" => "N",
        "TEMPLATE_THEME" => "blue",
        "USE_PRODUCT_QUANTITY" => "Y"
    ),
    false
);