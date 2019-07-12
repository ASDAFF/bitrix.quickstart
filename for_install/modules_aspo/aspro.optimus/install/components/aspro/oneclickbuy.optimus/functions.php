<?
function getPropertyByCode($propertyCollection, $code){
	foreach ($propertyCollection as $property){
		if($property->getField('CODE') == $code){
			return $property;
		}
	}
}
function checkNewVersionExt($module="main"){
	if($info = CModule::CreateModuleObject($module)){
		$testVersion = '16.0.30';
		
		if(CheckVersion($info->MODULE_VERSION, $testVersion)){
			return true;
		}else{
			return false;
		}
	}
	return false;
}

function placeOrder($registeredUserID, $basketUserID, $newOrder, $arOrderDat, $POST){
	\Bitrix\Sale\DiscountCouponsManager::init();
	$deliveryName = $paymentName = "";
	if(class_exists('\Bitrix\Sale\Delivery\Services\Manager'))
	{
		$service = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($newOrder["DELIVERY_ID"]);
		if(is_object($service))
		{
			if ($service->isProfile())
				$arDelivery['DELIVERY_NAME'] = $service->getNameWithParent();
			else
				$arDelivery['DELIVERY_NAME'] = $service->getName();
			$deliveryName = $arDelivery["DELIVERY_NAME"];
		}
		else
		{
			$deliveryName = "QUICK_ORDER";
		}
	}
	else
	{
		$deliveryName = "QUICK_ORDER";
	}

	if(class_exists('\Bitrix\Sale\PaySystem\Manager'))
	{
		$service = \Bitrix\Sale\PaySystem\Manager::getObjectById($newOrder["PAY_SYSTEM_ID"]);
		if(is_object($service))
			$paymentName=$service->getField('NAME');
		else
			$paymentName = "QUICK_ORDER";
	}
	else
	{
		$paymentName = "QUICK_ORDER";
	}
	
	$siteId = \Bitrix\Main\Context::getCurrent()->getSite();
			
	$order = Bitrix\Sale\Order::create($siteId, $basketUserID);
	
	$order->setPersonTypeId($newOrder['PERSON_TYPE_ID']);
	
	/*Basket start*/
	$basket = Bitrix\Sale\Basket::loadItemsForFUser($basketUserID, $siteId)->getOrderableItems();
	
	// action for basket items
	/*
		$basketItems = $basket->getBasketItems();
		foreach ($basketItems as $basketItem){}
	*/
	
	CSaleBasket::UpdateBasketPrices($basketUserID, $siteId);
	Bitrix\Sale\Compatible\DiscountCompatibility::stopUsageCompatible();
	$order->setBasket($basket);
	/*Basket end*/
	
	/*Shipment start*/
	$shipmentCollection = $order->getShipmentCollection();
	$shipment = $shipmentCollection->createItem();
	$shipment->setField('CURRENCY', $arOrderDat["CURRENCY"]);
	$shipmentItemCollection = $shipment->getShipmentItemCollection();
	foreach ($order->getBasket() as $item)
	{
		$shipmentItem = $shipmentItemCollection->createItem($item);
		$shipmentItem->setQuantity($item->getQuantity());
	}
	
	$shipment->setFields(
		array(
			'DELIVERY_ID' => $newOrder["DELIVERY_ID"],
			'DELIVERY_NAME' => $deliveryName
		)
	);

	$shipmentCollection->calculateDelivery();
	/*Shipment end*/

	/*Payment start*/
	$paymentCollection = $order->getPaymentCollection();
	$extPayment = $paymentCollection->createItem();
	$extPayment->setFields(
		array(
			'PAY_SYSTEM_ID' => $newOrder['PAY_SYSTEM_ID'],
			'PAY_SYSTEM_NAME' => $paymentName,
		)
	);
	/*Payment end*/
	
	$order->doFinalAction(true);
	
	/*Order fields start*/
	$order->setField('CURRENCY', $arOrderDat["CURRENCY"]);
	$order->setFields(
		array(
			'USER_DESCRIPTION' => $POST['ONE_CLICK_BUY']['COMMENT'],
			'COMMENTS' => GetMessage('FAST_ORDER_COMMENT'),
		)
	);
	/*Order fields end*/
	
	$order->setFieldNoDemand('USER_ID', $registeredUserID);
	
	/*Props start*/
	$propertyCollection = $order->getPropertyCollection();
	if($POST['ONE_CLICK_BUY']['EMAIL']){
		$obProperty = getPropertyByCode($propertyCollection, 'EMAIL');
		$obProperty->setValue($POST['ONE_CLICK_BUY']['EMAIL']);
	}
	/*Props end*/
	
	$r=$order->save();
	if (!$r->isSuccess()){
		die(getJson(GetMessage('ORDER_CREATE_FAIL'), 'N', implode('<br />', (array)$r->getErrors())));
	}
	
	return $r;
}
?>