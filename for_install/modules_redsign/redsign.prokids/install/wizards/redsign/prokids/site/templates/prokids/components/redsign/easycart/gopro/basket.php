<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$IS_AJAX = false;
if($_REQUEST['rsec_ajax_post']=='Y' && $_REQUEST['rsec_mode']=='basket')
{
	$IS_AJAX = true;
	$APPLICATION->RestartBuffer();
}

?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket", 
	"rs_easycart", 
	array(
		"COLUMNS_LIST" => array(),
		"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
		"HIDE_COUPON" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"USE_PREPAYMENT" => "N",
		"QUANTITY_FLOAT" => "N",
		"SET_TITLE" => "Y",
		"ACTION_VARIABLE" => "action",
		"OFFERS_PROPS" => array(),
	),
	false
);?><?

if($IS_AJAX)
{
	die();
}