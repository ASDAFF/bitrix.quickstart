<?php

namespace Lema\Seo;

use \Lema\Common\App,
	\Lema\Common\Helper;

\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('iblock');

/**
 * Class ECommerce
 * @package Lema\Seo
 */
class ECommerce extends \Lema\Base\StaticInstance
{
    /**
     * @var loaded order
     */
    protected $order = null;
    /**
     * @var iblock id
     */
    protected $iblockId = null;

    /**
     * @var array of order properties
     */
    protected $properties = array();
    /**
     * @var array of product categories
     */
    protected $categories = array();
    /**
     * @var array of products
     */
    protected $products = array();

    /**
     * @var pay system name
     */
    protected $paySystemName = null;

    /**
     * @var delivery system name
     */
    protected $deliveryName = null;


    /**
     * Set id of iblock
     *
     * @param $iblockId id of iblock
     *
     * @access public
     */
    public function setIblockId($iblockId)
    {
        $this->iblockId = $iblockId;
        return $this;
    }

    /**
     * Load order by id
     *
     * @param $orderId id of order
     * @return $this
     *
     * @access public
     */
    public function load($orderId)
    {
        $this->order = \Bitrix\Sale\Order::load((int) $orderId);
        return $this;
    }

    /**
     * Load order by account_number
     *
     * @param $orderNumber account number of order
     * @return $this
     *
     * @access public
     */
    public function loadByAccNumber($orderNumber)
    {
        $this->order = \Bitrix\Sale\Order::loadByAccountNumber($orderNumber);
        return $this;
    }

    /**
     * Returns array of order properties
     *
     * @return array
     *
     * @access public
     */
    public function getOrderProps()
    {
        //check order loaded
        $this->checkOrder();

        $ret = array();
        $this->properties = $this->order->getPropertyCollection();
        if(empty($this->properties))
            return $ret;
        foreach($this->properties->getArray()['properties'] as $prop)
            $ret[$prop['CODE']] = current($prop['VALUE']);
        return $ret;
    }

    /**
     * Return name of pay system
     *
     * @param bool $needUpdate
     *
     * @return string
     *
     * @access public
     */
    public function getPaymentName($needUpdate = false)
    {
        //check order loaded
        $this->checkOrder();

        if(empty($this->paySystemName) || $needUpdate)
        {
            $paySystem = $this->order->getPaymentCollection();
            foreach ($paySystem as $payment)
            {
                $this->paySystemName = $payment->getPaymentSystemName();
                break;
            }
        }
        return $this->paySystemName;
    }

    /**
     * Return name of delivery system
     *
     * @param bool $needUpdate
     * @return string
     *
     * @access public
     */
    public function getDeliveryName($needUpdate = false)
    {
        //check order loaded
        $this->checkOrder();

        if(empty($this->deliveryName) || $needUpdate)
        {
            $deliveryIds = $this->order->getDeliverySystemId();
            foreach ($deliveryIds as $deliveryId)
            {
                $delivery = \Bitrix\Sale\Delivery\Services\Manager::getById((int) $deliveryId);
                $this->deliveryName = empty($delivery['NAME']) ? 'Доставка курьером' : $delivery['NAME'];
                break;
            }
        }
        return $this->deliveryName;
    }

    /**
     * Returns products categories
     *
     * @param bool $needUpdate
     * @throws \Exception
     * @return array
     *
     * @access public
     */
    public function getProductCategories($needUpdate = false)
    {
        //check iblock id
        if(empty($this->iblockId))
            throw new \Exception('Wrong IBLOCK');

        //check order loaded
        $this->checkOrder();

        if(empty($this->categories) || $needUpdate)
        {
            //get all product categories
            $this->categories = array();
            foreach ($this->order->getBasket() as $item)
            {
                $this->categories[$item->getProductId()] = null;
            }
            $res = \Bitrix\Iblock\ElementTable::getList(array(
                'filter' => array('IBLOCK_ID' => $this->iblockId, 'ID' => array_keys($this->categories)),
                'select' => array('IBLOCK_SECTION.NAME', 'ID'),
            ));
            while ($row = $res->fetch())
                $this->categories[$row['ID']] = $row['IBLOCK_ELEMENT_IBLOCK_SECTION_NAME'];
        }
        return $this->categories;
    }

    /**
     * Returns array of products
     *
     * @param bool $needUpdate
     * @return array
     *
     * @access public
     */
    public function getProducts($needUpdate = false)
    {
        //check order loaded
        $this->checkOrder();

        if(empty($this->products) || $needUpdate)
        {
            $this->products = array();
            $categories = $this->getProductCategories($needUpdate);
            foreach ($this->order->getBasket() as $item)
            {
                $this->products[] = array(
                    'sku' => $item->getProductId(),
                    'name' => $item->getField('NAME'),
                    'category' => $categories[$item->getProductId()],
                    'price' => $item->getPrice(),
                    'quantity' => $item->getQuantity(),
                );
            }
        }
        return $this->products;
    }

    /**
     * Returns data for Google
     *
     * @param bool $needUpdate
     * @return array
     *
     * @access public
     */
    public function getGoogleCode($needUpdate = false)
    {
        //check order loaded
        $this->checkOrder();

        $props = $this->getOrderProps($needUpdate);
        return array(
            'event' => 'ecomEvent',
            'fio' => (empty($props['NAME']) ? $props['COMPANY'] : $props['NAME']),
            'email' => $props['EMAIL'],
            'phone' => $props['PHONE'],
            'address' => $props['ADDRESS'],
            'comment' => (empty($this->order->getField('USER_DESCRIPTION')) ? $this->order->getField('COMMENTS') : $this->order->getField('USER_DESCRIPTION')),
            'delivery' => $this->getDeliveryName($needUpdate),
            'pay_system' => $this->getPaymentName($needUpdate),
            'transactionId' => $this->order->getId(),
            'transactionTotal' => $this->order->getPrice(),
            'transactionProducts' => $this->getProducts($needUpdate),
        );
    }

    /**
     * Returns data for Yandex
     *
     * @param bool $needUpdate
     * @return array
     *
     * @access public
     */
    public function getYandexCode($needUpdate = false)
    {
        //check order loaded
        $this->checkOrder();

        return array(
            'ecommerce' => array(
                'purchase' => array(
                    'actionField' => array(
                        'id' => $this->order->getId(),
                    ),
                    'products' => $this->getProducts($needUpdate),
                ),
            ),
        );
    }

    /**
     * Returns or output formatted script
     *
     * @param bool $return
     * @param bool $needUpdate
     * @return string
     *
     * @access public
     */
    public function jsonResult($return = false, $needUpdate = false)
    {
        $ret  = '<script type="text/javascript">window.dataLayer = window.dataLayer || [];' . PHP_EOL;
        $ret .= 'dataLayer.push(' . Helper::getJson($this->getGoogleCode($needUpdate)) . ');' . PHP_EOL;
        $ret .= 'dataLayer.push(' . Helper::getJson($this->getYandexCode($needUpdate)) . ');' . PHP_EOL;
        $ret .= '</script>' . PHP_EOL;
        if($return)
            return $ret;
        echo $ret;
    }
    /**
     * Set view content
     *
     * @param string $name
     * @param null|string $content
     *
     * @return void
     *
     * @access public
     */
    public function setViewContent($name = 'ecommerce', $content = null)
    {
        App::get()->AddViewContent($name, (isset($content) ? $content : $this->jsonResult(true)));
    }

    /**
     * Output view content
     *
     * @param string $name
     *
     * @return void
     *
     * @access public
     */
    public function show($view = 'ecommerce')
    {
        App::get()->ShowViewContent($view);
    }

    /**
     * Check is order loaded
     *
     * @return void
     *
     * @throws \Exception
     *
     * @access protected
     */
    protected function checkOrder()
    {
        if(empty($this->order))
            throw new \Exception('Order not loaded');
    }
}
