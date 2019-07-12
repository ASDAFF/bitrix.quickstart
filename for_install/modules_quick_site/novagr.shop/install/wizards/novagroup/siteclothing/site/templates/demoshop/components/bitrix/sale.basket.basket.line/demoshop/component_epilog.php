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
	
	
	// если идет запрос на добавление товара в корзину - то проверяем, есть ли остатки для этого товара, если нет - то
	// не добавляем товар в корзину
	if ($_REQUEST["action"] == "ADD2BASKET" && $_REQUEST["id"] && $_REQUEST["ajax_buy"] == 1) {
		
		if( CModule::IncludeModule("catalog") ) {
		} else {
			die('Не установлены модули Инфоблоки или Каталог');
		}
		
		
		// получаем корзину для текущего пользователя
		
		$arBasketItems = array();
		
		$dbBasketItems = CSaleBasket::GetList(
				array(
						"NAME" => "ASC",
						"ID" => "ASC"
				),
				array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => "NULL"
				),
				false,
				false,
				array("ID",
						"CALLBACK_FUNC",
						"MODULE",
						"PRODUCT_ID",
						"QUANTITY",
						"DELAY",
						"CAN_BUY",
						"PRICE",
						"WEIGHT")
		);
		// ищем в корзине товар с нужным ID
		while ($arItems = $dbBasketItems->Fetch())
		{
			/*if (strlen($arItems["CALLBACK_FUNC"]) > 0)
			{
				CSaleBasket::UpdatePrice($arItems["ID"],
						$arItems["CALLBACK_FUNC"],
						$arItems["MODULE"],
						$arItems["PRODUCT_ID"],
						$arItems["QUANTITY"]);
				$arItems = CSaleBasket::GetByID($arItems["ID"]);
			}*/
			if ($arItems["PRODUCT_ID"] == $_REQUEST["id"] ) {
				
				// получаем остатки по товару
				$arProduct = CCatalogProduct::GetByID($_REQUEST["id"]);
				$quantityProduct = $arProduct["QUANTITY"];
				// в том случае если количество равно остаткам для товара - не даем положить его в корзину еще раз
				$quantityProductInBasket = $arItems["QUANTITY"];
				
				if ($quantityProductInBasket >=$quantityProduct) {
					
					$result = array();
					
					global $APPLICATION;
					$APPLICATION->RestartBuffer();
					$result['status'] = 'ERROR';
					$result['type'] = 'PRODUCT_EXCEEDED_LIMIT';
					
					
					$resultJson = json_encode($result);
					die($resultJson);					
				}
				break;
			} // if ($arItems["PRODUCT_ID"] == $_REQUEST["id"] ) {
		
		}
		
	}
	
	
	function BasketLineAjaxResponse()
	{
		global $APPLICATION;
		$APPLICATION->RestartBuffer();    
		$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "demoshop", $GLOBALS['BASKET_RESPONSE_AJAX_PARAMS'], false, array('HIDE_ICONS' => 'Y'));
                
		$buffer = ob_get_contents();
		ob_end_clean();
		//echo $buffer;        
		$result['status'] = 'OK';
		$result['type'] = '';
		
		//корректируем результат в зависимости от кодировки
		$rsSites = CSite::GetByID(SITE_ID);
		$arSite = $rsSites->Fetch();
		//echo "<pre>"; print_r($arSite["CHARSET"]); echo "</pre>";
		
		if (strtolower($arSite["CHARSET"]) == "windows-1251") {
			$buffer = iconv('windows-1251', 'UTF-8', $buffer);
		}    
		
		$result['html'] = $buffer;
				
		$resultJson = json_encode($result);
		die($resultJson);		
	}

	AddEventHandler('main', 'OnBeforeLocalRedirect', 'BasketLineAjaxResponse');
}
?>