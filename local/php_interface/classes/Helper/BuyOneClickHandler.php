<?php
namespace Helper;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use \Bitrix\Sale\Order;
use \Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Delivery\Services\Manager as DeliveryManager;
use Bitrix\Sale\PaySystem\Manager as PaySystemManager;
use Bitrix\Main\Application;
use Bitrix\Main\UserTable;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

class BuyOneClickHandler extends BaseHandler
{
    private $email;
    private $phone;

    /**
     * BuyOneClickHandler constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->email = $request->getQuery("email");
        $this->phone = $request->getQuery("phone");
    }

    /**
     * Обрабатывает запрос.
     */
    public function Execute() {
        $this->Buy($this->email, $this->phone);
    }

    private function Buy($productId, $coupon) {

        if (empty($this->email) && empty($this->phone)) {
            return;
        }

        $userId = $this->GetUserId();
        $order = $this->CreateOrder($productId, $userId);
        $result = $order->save();

        if ($coupon) {
            $this->SetCoupon($coupon, $userId, $result->getId(), $order->getDiscount());
        }

        return $result->isSuccess();
    }

    private function GetUserId()
    {
        $filters = [];
        if ($this->email) {
            $filters["=EMAIL"] = $this->email;
        }
        if ($this->phone) {
            $filters["PERSONAL_PHONE"] = $this->phone;
        }

        $row = UserTable::getList(array(
            'select' => ['ID'],
            'filter' => $filters
        ))->fetch();

        if ($row) {
            $userId = (int)$row['ID'];
        } else {
            $userId = $this->CreateUser();
        }

        return $userId;
    }


    /**
     * @return int
     */
    private function CreateUser()
    {
        $fields = [];

        if ($this->phone) {
            $fields["LOGIN"] = str_replace([' ', '(', ')', '', '+', '-'], '', $this->phone);
            $fields["PERSONAL_PHONE"] = $this->phone;
        }

        if ($this->email) {
            $fields["EMAIL"] = $this->email;
            $fields["LOGIN"] = substr($this->email, 0, strpos($this->email, '@'));
        } else {
            $fields["EMAIL"] = $fields["LOGIN"] . "@temp-mail.temp";
        }
        $pass = md5($fields["EMAIL"]);
        $fields["PASSWORD"] = $pass;
        $fields["CONFIRM_PASSWORD"] = $pass;

        $newUser = new CUser;
        $userId = (int)$newUser->Add($fields);
        if ($userId <= 0) {
            Debug::dumpToFile($newUser->LAST_ERROR);
        }
        return $userId;
    }


    /**
     * @param $productId
     * @return array
     */
    private function GetProductFields($productId)
    {
        $productFields = [];
        $res = \CIBlockElement::GetList(
            [],
            ['IBLOCK' => 2, 'ID' => $productId],
            false,
            false,
            ["ID", "IBLOCK_ID", "NAME", "CATALOG_GROUP_1"]
        );
        if ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $productFields = [
                "PRICE" => $arFields["CATALOG_PRICE_1"],
                "CURRENCY" => $arFields["CATALOG_CURRENCY_1"],
                "NAME" => $arFields["NAME"],
                "QUANTITY" => 1
            ];
        }
        return $productFields;
    }

    /**
     * @param $order
     */
    private function SetPaymentFields($order)
    {
        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem(
            PaySystemManager::getObjectById(1)
        );

        $payment->setField("SUM", $order->getPrice());
        $payment->setField("CURRENCY", $order->getCurrency());
    }

    /**
     * @param $coupon
     * @param $userId
     * @param $orderId
     * @param $discount
     */
    private function SetCoupon($coupon, $userId, $orderId, $discount)
    {
        DiscountCouponsManager::init(
            DiscountCouponsManager::MODE_ORDER, [
                "userId" => $userId,
                "orderId" => $orderId
            ]
        );
        DiscountCouponsManager::add($coupon);
        $discounts = $discount;
        $discounts->calculate();
    }

    /**
     * @param $productId
     * @param $userId
     * @return Order
     */
    private function CreateOrder($productId, $userId)
    {
        Loader::includeModule("sale");
        Loader::includeModule("iblock");

        $basket = Basket::create(SITE_ID);
        $item = $basket->createItem("catalog", $productId);

        $order = Order::create(SITE_ID, $userId);
        $order->setPersonTypeId(1);

        $productFields = $this->GetProductFields($productId);
        $item->setFields($productFields);
        $order->setBasket($basket);

        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem(
            DeliveryManager::getObjectById(1)
        );

        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        foreach ($basket as $basketItem) {
            $item = $shipmentItemCollection->createItem($basketItem);
            $item->setQuantity($basketItem->getQuantity());
        }
        $this->SetPaymentFields($order);
        return $order;
    }
}