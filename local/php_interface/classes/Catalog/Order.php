<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 19.06.14
 * Time: 16:28
 */

namespace Catalog;

use Sale;

class Order implements \ArrayAccess
{
    protected $id;
    protected $container;
    protected $props;
    protected $propsRaw;
    protected $orderProps = null;// objects
    protected $products = null;

    protected $statusTitles = array(
        'N' => 'Принят, ожидается оплата',
        'P' => 'Оплачен, формируется к отправке',
        'F' => 'Выполнен'
    );

    public function __construct($id)
    {
        if (is_array($id))
        {
            $this->container = $id;
            $this->id = $id['ID'];
        }
        elseif($id)
        {
            $this->id = $id;
            $this->container = \CSaleOrder::GetByID($id);
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->container[] = $value;
        }
        else
        {
            $this->container[$offset] = $value;
        }
    }
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function __get($key)
    {
        $key = strtoupper($key);

        if (!isset($this->container[$key]))
        {
            throw new Exception('Object ' . get_class($this) . ' does not have property ' . $key);
        }

        return $this->container[$key];
    }

    public function getRaw()
    {
        return $this->container;
    }

    public function getProperties()
    {
        if (!isset($this->props))
        {
            $this->props = $this->propsRaw = array();

            $res = \CSaleOrderPropsValue::GetList(array('SORT' => 'ASC'), array('ORDER_ID' => $this->id));

            while ($row = $res->Fetch())
            {
                $this->props[$row['CODE']] = $row['VALUE'];
                $this->propsRaw[] = $row;
            }
        }

        return $this->props;
    }

    public function getLocationId()
    {
        $props = $this->getProperties();
        return $props['LOCATION'];
    }

    public function isPayed()
    {
        return $this['PAYED'] == 'Y';
    }

    public function setPayed($value)
    {
        $value = (bool) $value ? 'Y' : 'N';

        if ($value == 'Y')
        {
            \CSaleOrder::PayOrder($this['ID'], 'Y');
        }
        else
        {
            $data = array(
                'PAYED' => (bool) $value ? 'Y' : 'N',
                'DATE_PAYED' => Date(\CDatabase::DateFormatToPHP(\CLang::GetDateFormat('FULL', LANG))),
                'USER_ID' => $this['USER_ID'],
            );

            return \CSaleOrder::Update($this['ID'], $data);
        }
    }

    public function getStatusString()
    {
        return $this->statusTitles[$this['STATUS_ID']];
    }

    public static function getOrders($userId)
    {
        if(!\CModule::IncludeModule("sale")) throw new Exception("can't load sale module");

        $orders = self::getOrdersList($userId);

        if(!$orders) return false;

        return $orders;
    }

    /**
     * @param bool $userId
     * @return self[]
     */
    public static function getOrdersList($userId = false)
    {
        $arFilter = $userId ? array('USER_ID' => $userId) : array();

        $rs = \CSaleOrder::GetList(array('ID' => 'DESC'), $arFilter, false, false, array());

        $orders = array();
        while($ar = $rs->GetNext(true, false))
        {
            $orders[] = new static($ar);
        }

        return $orders;
    }

    /**
     * @param $orderId
     * @param bool $userId
     * @return self
     */
    public static function getOrderById($orderId, $userId = false)
    {
        $arFilter = array('ID' => $orderId);
        if($userId)
        {
            $arFilter['USER_ID'] = $userId;
        }

        $rs = \CSaleOrder::GetList(array('ID' => 'DESC'), $arFilter, false, false, array());

        if($ar = $rs->GetNext(true, false))
        {
            return new static($ar);
        }

        return false;
    }

    public function getPrice()
    {
        return $this->container['PRICE'] - $this->getDeliveryPrice();
    }

    public function getDeliveryPrice()
    {
        return $this->container['PRICE_DELIVERY'];
    }

    public function getTotalPrice()
    {
        return $this->container['PRICE'];
    }

    public function getPriceFormatted()
    {
        return number_format($this->getPrice(), 2, '', ' ');
    }

    public function getDeliveryPriceFormatted()
    {
        return number_format($this->getDeliveryPrice(), 2, '', ' ');
    }

    public function getTotalPriceFormatted()
    {
        return number_format($this->getTotalPrice(), 2, '', ' ');
    }

    public function getDate($format = 'd.m.Y')
    {
        return FormatDate($format, strtotime($this->container['DATE_INSERT']));
    }

    public function getCurrency()
    {
        return $this->container['CURRENCY'];
    }

    public function getId()
    {
        return $this->container['ID'];
    }

    private function fillDelivery()
    {
        $this->container['DELIVERY'] = Sale\Delivery::getDelivery($this->container['DELIVERY_ID']);
    }

    public function getDeliveryName()
    {
        if(!isset($this->container['DELIVERY'])) $this->fillDelivery();
        return is_object($this->container['DELIVERY']) ? $this->container['DELIVERY']->getName() : false;
    }

    private function fillPayment()
    {
        $this->container['PAYMENT'] = Sale\Payment::getPayment($this->container['PAY_SYSTEM_ID']);
    }

    public function getPaymentName()
    {
        if(!isset($this->container['PAYMENT'])) $this->fillPayment();
        return is_object($this->container['PAYMENT']) ? $this->container['PAYMENT']->getName() : false;
    }

    public function getOrderProps()
    {
        if($this->orderProps === null)
        {
            $this->orderProps = Sale\OrderPropsValue::getOrderPropsValue($this->getId(), true);
        }

        return $this->orderProps;
    }

    public function getOrderPropValue($code)
    {
        $orderProps = $this->getOrderProps();
        return isset($orderProps[$code]) ? $orderProps[$code]->getValue() : false;
    }

    public function getProducts($cartClassName = '\Catalog\Cart')
    {
        if($this->products === null)
        {
            $cart = new $cartClassName();
            $this->products = $cart->getItems($this->container['ID']);
        }

        return $this->products;
    }
}