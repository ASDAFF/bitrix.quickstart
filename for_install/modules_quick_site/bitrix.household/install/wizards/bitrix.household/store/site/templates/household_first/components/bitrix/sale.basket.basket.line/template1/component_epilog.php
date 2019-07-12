<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!$arParams['BUY_URL_SIGN'] && $arParams['BUY_URL_SIGN'] !== false)
	$arParams['BUY_URL_SIGN'] = 'action=ADD2BASKET';

if (
	$_REQUEST['ajax_buy']
	&& $arParams['BUY_URL_SIGN'] 
	&& (false !== strpos($_SERVER['REQUEST_URI'], $arParams['BUY_URL_SIGN']))
)
{
	$arNewParams = array();
	foreach ($arParams as $key => $value)
	{
		if (substr($key, 0, 1) == '~' && $key != '~BUY_URL_SIGN')
		{
			$arNewParams[substr($key, 1)] = $value;
		}
	}
	
	$arNewParams['BUY_URL_SIGN'] = false;
	$GLOBALS['BASKET_RESPONSE_AJAX_PARAMS'] = $arNewParams;

	function BasketLineAjaxResponse()
	{
		global $APPLICATION;
		$APPLICATION->RestartBuffer();
		$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", ".default", $GLOBALS['BASKET_RESPONSE_AJAX_PARAMS'], false, array('HIDE_ICONS' => 'Y'));

		die();
	}

	AddEventHandler('main', 'OnBeforeLocalRedirect', 'BasketLineAjaxResponse');
}
?>