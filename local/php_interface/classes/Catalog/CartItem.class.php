<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 16.06.14
 * Time: 11:56
 */

namespace Cpeople\Classes\Catalog;

use Bitrix\Main\DB\Exception;

class CartItem implements \ArrayAccess
{
    protected $container;
    protected $productClassName = '\Cpeople\Classes\Catalog\Product';

    /**
     * @var \Cpeople\Classes\Catalog\Cart
     */
    protected $cart;

    /**
     * @var \Cpeople\Classes\Block\Object
     */
    protected $product;

    public function __construct($data = array(), \Cpeople\Classes\Catalog\Cart $cart)
    {
        $this->container = $data;
        $this->cart = $cart;
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

    public function getProduct($className = null)
    {
        if (!isset($this->product))
        {
            if (empty($className)) $className = $this->productClassName;

            $this->product = \Cpeople\Classes\Block\Getter::instance()
                ->setClassName($className)
                ->getById($this['PRODUCT_ID']);
        }

        return $this->product;
    }

    public function getSum()
    {
        return $this->getDiscountPrice() * $this->getQuantity();
    }

    public function getSumWODiscount()
    {
        return $this->getOldPrice() * $this->getQuantity();
    }

    public function getPrice()
    {
        return $this['PRICE'];
    }

    public function getWeight()
    {
        return $this['WEIGHT'];
    }

    public function getBitrixDiscounts()
    {
        return \CCatalogDiscount::GetDiscountByProduct($this->getProduct()->id);
    }

    public function getDiscountPrice()
    {
        return $this->getProduct()->getDiscountPrice();
        /*return \CCatalogProduct::CountPriceWithDiscount(
            $this->getOldPrice(),
            $this['CURRENCY'],
            $this->getBitrixDiscounts()
        );*/
    }

    public function getOldPrice()
    {
        return $this->getProduct()->getOldPrice();
    }

    public function getQuantity()
    {
        return $this['QUANTITY'];
    }

    public function remove()
    {
        \CSaleBasket::Delete($this['ID']);
    }

    public function setQuantity($quantity)
    {
        $this['QUANTITY'] = floatval($quantity);
        $this->save();
    }

    public function save()
    {
        return \CSaleBasket::Update($this['ID'], array(
            'PRICE' => $this['PRICE'],
            'QUANTITY' => $this['QUANTITY'],
            'CURRENCY' => $this['CURRENCY'],
        ));

        $cart->setTainted(true);
    }

    public function hasDiscount()
    {
        return $this->getOldPrice() > $this->getDiscountPrice();
    }

    public function getDiscountValue()
    {
//        return $this->getBitrixDiscounts()[0]['VALUE'];
        return $this->hasDiscount() ? 100 - round(100 * $this->getDiscountPrice() / $this->getOldPrice()) : false;
    }

    public function getName()
    {
        return $this['NAME'];
    }
}