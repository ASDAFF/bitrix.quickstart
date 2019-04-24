<?php

namespace Lema\Basket;

use \Lema\Common\Helper;
use \Lema\Forms\Form,
    \Lema\IBlock\Element;


/**
 * Class BasketHandler
 * @package Lema\Basket
 */
class BasketHandler extends \Lema\Base\StaticInstance
{
    protected $basket = null;
    protected $currency = null;
    protected $lid = null;
    protected $fUserId = null;

    /**
     * Basket constructor.
     *
     * @access public
     */
    public function __construct()
    {
        //load modules
        $this->loadModules(array('sale', 'catalog', 'iblock'));

        //set variables
        $this->setVariables();
    }

    /**
     * Load given modules
     *
     * @param array $modules
     *
     * @access protected
     */
    protected function loadModules(array $modules = array())
    {
        if(empty($modules))
            return ;
        foreach($modules as $module)
            \Bitrix\Main\Loader::includeModule($module);
    }

    /**
     * Set variables for current site
     *
     * @return void
     *
     * @access protected
     */
    protected function setVariables()
    {
        $this->lid = \Bitrix\Main\Context::getCurrent()->getSite();
        $this->currency = \Bitrix\Currency\CurrencyManager::getBaseCurrency();
        $this->fUserId = \Bitrix\Sale\Fuser::getId();

        $this->basket = \Bitrix\Sale\Basket::loadItemsForFUser($this->fUserId, $this->lid);
    }

    /**
     * Returns FUSER_ID
     *
     * @return null
     *
     * @access public
     */
    public function getFUserId()
    {
        return $this->fUserId;
    }

    /**
     * Returns basket
     *
     * @return null
     *
     * @access public
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Returns basket items count (count of positions)
     *
     * @return int
     *
     * @access public
     */
    public function getItemsCount()
    {
        return count($this->getBasket()->getQuantityList());
    }

    /**
     * Returns products count of basket
     *
     * @return float|int
     *
     * @access public
     */
    public function getGoodsCount()
    {
        return array_sum($this->getBasket()->getQuantityList());
    }

    /**
     * Add product to basket
     *
     * @param array $items
     * @return bool
     *
     * @access public
     */
    public function add(array $items = array())
    {
        $basket = $this->getBasket();
        if(empty($basket))
            return false;

        $iblockId = null;
        $properties = array();
        foreach($items as $k => $itemInfo)
        {
            $quantity = empty($itemInfo['QUANTITY']) ? 1 : (int) $itemInfo['QUANTITY'];
            $fields = array(
                'QUANTITY' => $quantity,
                'CURRENCY' => (isset($itemInfo['CURRENCY']) ? $itemInfo['CURRENCY'] : $this->currency),
                'LID' => (isset($itemInfo['LID']) ? $itemInfo['LID'] : $this->lid),
                'PRODUCT_PROVIDER_CLASS' => '\\CCatalogProductProvider',
            );
            if(isset($itemInfo['PRICE']))
            {
                $fields['PRICE'] = $itemInfo['PRICE'];
                $fields['CUSTOM_PRICE'] = 'Y';
            }

            $addProps = array();

            if(!empty($itemInfo['PROPERTIES']))
            {

                if(empty($iblockId))
                {
                    $iblockId = Element::getById($itemInfo['PRODUCT_ID']);
                    $iblockId = $iblockId['IBLOCK_ID'];
                }
                if(empty($properties))
                {
                    $res = \CIBlockElement::GetProperty($iblockId, $itemInfo['PRODUCT_ID']);
                    while($ob = $res->GetNext())
                    {
                        $properties[$ob['CODE']] = array(
                            'NAME' => $ob['NAME'],
                            'CODE' => $ob['CODE'],
                            'VALUE' => '',
                            'SORT' => $ob['SORT'],
                        );
                    }
                }
                //var_dump($properties);
                foreach($itemInfo['PROPERTIES'] as $code => $value)
                {
                    if(isset($properties[$code]))
                    {
                        $properties[$code]['VALUE'] = $value;
                        $addProps[] = $properties[$code];
                    }
                }
            }

            if ($item = $this->getBasketItem($itemInfo['PRODUCT_ID'], (isset($fields['PRICE']) ? $fields['PRICE'] : null), $addProps)) {
                $fields['QUANTITY'] = $item->getQuantity() + $quantity;
            }
            else {
                $item = $basket->createItem('catalog', $itemInfo['PRODUCT_ID']);
            }

            $item->setFields($fields);

            if(!empty($addProps))
                $item->getPropertyCollection()->setProperty($addProps);
        }
        return $basket->save();
    }

    /**
     * Delete product from basket by item (position) id
     *
     * @param array $itemIds
     * @return bool
     *
     * @access public
     */
    public function delete($itemIds = array())
    {
        $itemIds = (array) $itemIds;

        $basket = $this->getBasket();
        if(empty($basket))
            return false;
        foreach($itemIds as $id)
            $basket->getItemById($id)->delete();
        return $basket->save();
    }

    /**
     * Returns basket items
     *
     * @return mixed
     *
     * @access public
     */
    public function getBasketItems()
    {
        return $this->getBasket()->getBasketItems();
    }

    /**
     * Returns basket items with all product fields
     *
     * @param $iblockId
     * @return array
     *
     * @access public
     */
    public function getBasketItemsWithFields($iblockId)
    {
        $items = $this->getBasketItems();
        $fields = array();

        foreach($items as $item)
            $fields[$item->getProductId()] = null;

        $fields = Element::getListD7($iblockId, array('filter' => array('ID' => array_keys($fields))));

        return array(
            'items' => $items,
            'fields' => $fields,
        );
    }

    /**
     * Returns basket items with props
     *
     * @param array $propCodes
     * @return array
     *
     * @access public
     */
    public function getBasketItemsWithProps(array $propCodes)
    {
        $items = array();
        foreach($this->getBasket()->getBasketItems() as $item)
        {
            $props = array();
            foreach($propCodes as $propCode)
                $props[$propCode] = $item->getField($propCode);
            $items[$item->getId()] =$props;
        }
        return $items;
    }

    /**
     * Get basket item by product id
     *
     * @param $productId
     * @return bool
     *
     * @access public
     */
    public function getBasketItem($productId, $price = NULL, array $props = array())
    {
        $needCheckProps = !empty($props);

        foreach($this->getBasket() as $item)
        {
            if($item->getProductId() == $productId && (isset($price) ? $item->getField('PRICE') == $price : true))
            {
                if($needCheckProps)
                {
                    $found = true;
                    $count = 0;
                    foreach($item->getPropertyCollection() as $prop)
                    {
                        ++$count;
                        $code = $prop->getField('CODE');
                        $found = $found && isset($props[$code]) && $props[$code] == $prop->getField('VALUE');
                    }
                    if($count <= count($props) && $found)
                        return $item;
                }
                else
                    return $item;
            }
        }
        return false;
    }

    /**
     * Delete all products from basket
     *
     * @access public
     */
    public function clear()
    {
        \CSaleBasket::DeleteAll();
    }

    /**
     * Fast buy click action
     * @param Form $form
     * @param $iblockId
     * @param array $addParams
     * @param $messageId
     * @param array $messageParams
     * @param bool $cp1251
     * @return array
     *
     * @access public
     */
    public function fastOrderAction(Form $form, $iblockId, array $addParams, $messageId, array $messageParams=array(), $cp1251 = false)
    {
        if($form->validate())
        {
            $status = true;

            \Bitrix\Main\Loader::includeModule('iblock');

            $res = \CIBlockElement::GetByID((int) $form->getField('PRODUCT_ID'));
            if(!($row = $res->GetNext()))
                $status = false;

            $getFieldMethod = $cp1251 ? 'getFieldCP1251' : 'getField';

            $defMsgParams = array(
                'NAME' => $form->{$getFieldMethod}('NAME'),
                'PHONE' => $form->getField('PHONE'),
                'QUANTITY' => $form->getField('QUANTITY'),
                'PRODUCT' => $this->encodeWithCharset($row['NAME'], $cp1251),
                'PRODUCT_URL' => Helper::getFullUrl($row['DETAIL_PAGE_URL']),
            );
            foreach($defMsgParams as $k => $v)
                if(empty($messageParams[$k]))
                    $messageParams[$k] = $v;

            $status = $status && $form->sendMessage($messageId, $messageParams);

            $status = $status && $form->addRecord($iblockId, $addParams);

            if($status)
                return array('status' => $status);

            return array('errors' => $form->getErrors());
        }
        else
            return array('errors' => $form->getErrors());
    }

    /**
     * Returns formatted price
     *
     * @return mixed
     *
     * @access public
     */
    public function getFormattedPrice()
    {
        return SaleFormatCurrency($this->getBasket()->getPrice(), $this->currency);
    }

    /**
     * Returns formatted base price
     *
     * @return mixed
     *
     * @access public
     */
    public function getFormattedBasePrice()
    {
        return SaleFormatCurrency($this->getBasket()->getBasePrice(), $this->currency);
    }

    /**
     * Add product to cart (because ::add expected argument of array type)
     *
     * @param array $data
     * @return bool
     *
     * @access public
     */
    public function addAction(array $data = array())
    {
        return $this->add(array($data));
    }

    /**
     * Delete product from cart (because ::delete expected argument of array type)
     *
     * @param array $data
     * @return bool
     *
     * @access public
     */
    public function deleteAction($data = array())
    {
        return $this->delete(array($data));
    }

    /**
     * @TODO make it...
     *
     * Add product to compare list
     *
     * @param array $data
     *
     * @access public
     */
    public function compareAction(array $data = array())
    {

    }

    /**
     * Returns encoded value
     *
     * @param $value
     * @param bool $cp1251
     * @return string
     *
     * @access public
     */
    public static function encodeWithCharset($value, $cp1251 = false)
    {
        return $cp1251 ? Helper::enc(Helper::utf8ToCP1251($value)) : Helper::enc($value);
    }
}