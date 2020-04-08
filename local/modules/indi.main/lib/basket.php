<?php
/**
 *  module
 *
 * @category
 * @link        http://.ru
 * @revision    $Revision$
 * @date        $Date$
 */

namespace Indi\Main;

use \Bitrix\Main\Loader;
use \Bitrix\Main;
use \Bitrix\Sale;
use \Bitrix\Sale\Fuser;
use \Bitrix\Sale\BasketComponentHelper;

if (!Loader::includeModule('sale')) {
    throw new Main\Exception("Sale module is't installed.");
}

/**
 * Работа с корзиной
 */
class Basket
{
    protected $basket = false;

    /**
     * Basket constructor.
     */
    public function __construct()
    {
        $this->basket = Sale\Basket::loadItemsForFUser(Fuser::getId(), Main\Context::getCurrent()->getSite());
    }


    /**
     * Добавляет товар в корзину
     *
     * @param int $productId - ID товара
     * @param int $quantity - количество
     * @return int
     */
    public function add($productId = 0, $quantity = 1)
    {
        if ($productId > 0) {

            $item = $this->basket->getExistsItem('catalog', $productId);

            if ($item) {
                $item->setField('QUANTITY', $item->getQuantity() + $quantity);
                $item->save();
            } else {
                $item = $this->basket->createItem('catalog', $productId);
                $item->setFields(array(
                    'QUANTITY' => $quantity,
                    'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                    'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
                    'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                ));
                $item->save();
            }
//            \Indi\Main\Util::dump($this->basket->getOrderableItems());
//            die();
            $this->basket->getOrderableItems();
            $this->basket->save();
        }

        return $this->getCount();
    }


    /**
     * Возвращает количество товаров в корзине
     *
     */
    public function getCount()
    {
        $fuserId = Fuser::getId(true);
        $this->basket = Sale\Basket::loadItemsForFUser($fuserId, Main\Context::getCurrent()->getSite());
        return array_sum($this->basket->getQuantityList());
    }


    /**
     * Возращает полную стоимость корзины
     *
     * @return mixed
     */
    function getPrice(){

        $fuserId = Fuser::getId(true);
        return BasketComponentHelper::getFUserBasketPrice($fuserId, SITE_ID);
    }
}