<?
global $USER;

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

// Платежные системы
$arResult['PAY_SYSTEMS'] = array();
$paySystems = CSalePaySystemAction::GetList(
	array(),
	array(
		'ACTIVE' => 'Y'
	),
	false,
	false,
	array(
		'ID',
		'NEW_WINDOW'
	)
);
while ($paySystem = $paySystems->GetNext()) {
	$arResult['PAY_SYSTEMS'][$paySystem['ID']] = $paySystem;
}

// Список продуктов
$arResult['ORDERS_PRODUCTS_ID'] = array();

// Недостающие данные по заказам
foreach ($arResult['ORDERS'] as &$order) {
	$currency = $order['ORDER']['CURRENCY'];
	
	// Месяц словом
	$order['ORDER']['DATE_INSERT_FORMATED'] = ToLower(FormatDate('d F Y', MakeTimeStamp($order['ORDER']['DATE_INSERT'])));
	
	// Платежная система
	$order['PAYMENT'] = $arResult['INFO']['PAY_SYSTEM'][$order['ORDER']['PAY_SYSTEM_ID']];
	$order['PAY_SYSTEM'] = $arResult['PAY_SYSTEMS'][$order['ORDER']['PAY_SYSTEM_ID']];
	
	$order['ORDER']['DISCOUNT_PRICE'] = 0;
	
	foreach ($order['BASKET_ITEMS'] as &$basketItem) {
		$basketItem['PRICE_FORMATED'] = CurrencyFormat($basketItem['PRICE'], $currency);
		
		$basketItem['PRICE_SUM'] = $basketItem['PRICE'] * $basketItem['QUANTITY'];
		$basketItem['PRICE_SUM_FORMATED'] = CurrencyFormat($basketItem['PRICE_SUM'], $currency);
		
		$basketItem['PRICE_WO_DISCOUNT'] = $basketItem['DISCOUNT_PRICE'] + $basketItem['PRICE'];
		$basketItem['PRICE_WO_DISCOUNT_FORMATED'] = CurrencyFormat($basketItem['PRICE_WO_DISCOUNT'], $currency);
		
		$order['ORDER']['DISCOUNT_PRICE'] += $basketItem['DISCOUNT_PRICE'] * $basketItem['QUANTITY'];
		
		$arResult['ORDERS_PRODUCTS_ID'][] = $basketItem['PRODUCT_ID']; 
	}
	unset($basketItem);
	
	$order['ORDER']['PRICE_DELIVERY_FORMATED'] = CurrencyFormat($order['ORDER']['PRICE_DELIVERY'], $currency);
	
	$order['ORDER']['PRICE_WO_DELIVERY'] = $order['ORDER']['PRICE'] - $order['ORDER']['PRICE_DELIVERY'];
	$order['ORDER']['PRICE_WO_DELIVERY_FORMATED'] = CurrencyFormat($order['ORDER']['PRICE_WO_DELIVERY'], $currency);
	
	$order['ORDER']['PRICE_WO_DISCOUNT'] = $order['ORDER']['PRICE_WO_DELIVERY'] + $order['ORDER']['DISCOUNT_PRICE'];
	$order['ORDER']['PRICE_WO_DISCOUNT_FORMATED'] = CurrencyFormat($order['ORDER']['PRICE_WO_DISCOUNT'], $currency);
	
	$order['ORDER']['DISCOUNT_PRICE_FORMATED'] = CurrencyFormat($order['ORDER']['DISCOUNT_PRICE'], $currency);
	$order['ORDER']['DISCOUNT_PERCENT'] = $order['ORDER']['PRICE_WO_DISCOUNT'] > 0 ? 100 * $order['ORDER']['DISCOUNT_PRICE'] / $order['ORDER']['PRICE_WO_DISCOUNT'] : 0;
	$order['ORDER']['DISCOUNT_PERCENT_FORMATED'] = roundEx($order['ORDER']['DISCOUNT_PERCENT'], 0) . '%';
}
unset($order);

$arResult['ORDERS_PRODUCTS'] = array();
if ($arResult['ORDERS_PRODUCTS_ID']) {
	// Продукты
	$products = CIBlockElement::GetList(
		array(),
		array(
			//'IBLOCK_ID' => \Site\Main\Iblock\Prototype::getIdByCode('catalog'), 
			'ACTIVE' => 'Y',
			'ID' => $arResult['ORDERS_PRODUCTS_ID']
		),
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID',
			'NAME',
			'PREVIEW_PICTURE',
			'DETAIL_PICTURE'
		)
	);
	while ($product = $products->GetNext()) {
		$arResult['ORDERS_PRODUCTS'][$product['ID']] = $product;
	}
	
	// Недостающие данные по заказам
	foreach ($arResult['ORDERS'] as &$order) {
		foreach ($order['BASKET_ITEMS'] as &$basketItem) {
			if ($arResult['ORDERS_PRODUCTS'][$basketItem['PRODUCT_ID']]['PREVIEW_PICTURE'] > 0) {
				$basketItem['PICTURE'] = $arResult['ORDERS_PRODUCTS'][$basketItem['PRODUCT_ID']]['PREVIEW_PICTURE'];
			} elseif ($arResult['ORDERS_PRODUCTS'][$basketItem['PRODUCT_ID']]['DETAIL_PICTURE'] > 0) {
				$basketItem['PICTURE'] = $arResult['ORDERS_PRODUCTS'][$basketItem['PRODUCT_ID']]['DETAIL_PICTURE'];
			}
			
			// Получаем комплекты
			if ($basketItem['SET_PARENT_ID'] > 0) {
				$basketItem['SET_ITEMS'] = array();
				
				$productSets = CCatalogProductSet::getAllSetsByProduct($basketItem['PRODUCT_ID'], CCatalogProductSet::TYPE_SET);
				if ($productSets) {
					$setIDs = array();
					foreach ($productSets as $productSet) {
						foreach($productSet['ITEMS'] as $productSetItem) {
							$setIDs[] = $productSetItem['ITEM_ID'];
						}
					}
					
					if ($setIDs) {
						$setItems = CIBlockElement::GetList(
							array(),
							array(
								'ID' => $setIDs,
								'ACTIVE' => 'Y'
							),
							false,
							false,
							array(
								'ID',
								'IBLOCK_ID',
								'NAME',
								'DETAIL_PAGE_URL',
								'PREVIEW_PICTURE',
								'DETAIL_PICTURE'
							)
						);
						while ($setItem = $setItems->GetNext()) {
							$basketItem['SET_ITEMS'][] = $setItem;
						}
					}
				}
			}
		}
		unset($basketItem);
	}
	unset($order);
}

//Site\Main\Console::log($arResult);