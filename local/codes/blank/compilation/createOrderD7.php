<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Sale,
    Bitrix\Sale\Order,
    Bitrix\Sale\DiscountCouponsManager;
    
if (!Loader::IncludeModule('sale'))
    die();

function getPropertyByCode($propertyCollection, $code)  {
    foreach ($propertyCollection as $property)
    {
        if($property->getField('CODE') == $code)
            return $property;
    }
}

$siteId = \Bitrix\Main\Context::getCurrent()->getSite();

$fio = 'Пупкин Василий';
$phone = '9511111111';
$email = 'pupkin@mail.ru';

$currencyCode = Option::get('sale', 'default_currency', 'RUB');

DiscountCouponsManager::init();

$order = Order::create($siteId, \CSaleUser::GetAnonymousUserID());

$order->setPersonTypeId(1);
$basket = Sale\Basket::loadItemsForFUser(\CSaleBasket::GetBasketUserID(), $siteId)->getOrderableItems();

/* Действия над товарами
$basketItems = $basket->getBasketItems();
foreach ($basketItems as $basketItem) {
    
}
*/

$order->setBasket($basket);

/*Shipment*/
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem();
$shipment->setFields([
   'DELIVERY_ID' => 1,
   'CURRENCY' => $currencyCode
]);
/**/

/*Payment*/
$paymentCollection = $order->getPaymentCollection();
$extPayment = $paymentCollection->createItem();
$extPayment->setFields([
   'PAY_SYSTEM_ID' => 1,
]);
/**/

$order->doFinalAction(true);
$propertyCollection = $order->getPropertyCollection();

$emailProperty = getPropertyByCode($propertyCollection, 'EMAIL');
$emailProperty->setValue($email);

$phoneProperty = getPropertyByCode($propertyCollection, 'PHONE');
$phoneProperty->setValue($phone);

$order->setField('CURRENCY', $currencyCode);
$order->setField('COMMENTS', 'Комментарии');

$order->save();

$orderId = $order->GetId();
