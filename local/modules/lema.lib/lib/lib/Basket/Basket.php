<?php

namespace Lema\Basket;

/*
 * include modules
 */
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('highloadblock');

/**
 * Class Basket
 * @package Lema\Basket
 */
class Basket extends HighloadBlock
{
    /**
     * @var Basket $_instance - instance for static call (not full singleton)
     */
    private static $_instance = null;


    /**
     * @var BasketPosition $basketPosition - object of product positions
     */
    private $basketPosition = null;

    /**
     * @var array $basketItems - array of items in basket
     */
    private $basketItems = array();

    /**
     * @var bool $loaded - status of basket loading
     */
    private $loaded = false;

    /**
     * @var string $userId - id of current user
     */
    private $userId = null;


    /**
     * Basket constructor.
     *
     * @param null $userId
     *
     * @access public
     */
    public function __construct($userId = null)
    {
        //get id of current user
        $userId = $this->getOrGenerateUserId($userId);

        //create positions for basket
        $this->basketPosition = new BasketPosition();

        //load basket
        $this->loadByUserId($userId);

        //remove old baskets
        $this->deleteOldBaskets();
    }

    /**
     * Create or return new instance of Basket (for static call)
     *
     * @param null $userId
     *
     * @return $this
     *
     * @access public
     */
    public static function get($userId = null)
    {
        if(null === static::$_instance)
            static::$_instance = new static($userId);
        return static::$_instance;
    }

    /**
     * Load basket for current user
     *
     * @param $userId
     *
     * @return $this
     *
     * @access public
     */
    public function loadByUserId($userId)
    {
        $this->loaded = false;

        //store current user
        $this->userId = $userId;

        //select basket for current user
        $dataClass = $this->getEntityDataClass(Settings::BASKET_HLBLOCK_ID);

        $res = $dataClass::getList(array(
            'select' => array('*'),
            'filter' => array('UF_UUID' => $this->userId),
        ));
        $data = array();

        while($row = $res->fetch())
            $data[$row['ID']] = $row;

        //basket for this user doesn't exists yet, create it
        if(empty($data))
        {
            //add new basket for current user
            $this->addRecord(
                Settings::BASKET_HLBLOCK_ID,
                array(
                    'UF_UUID' => $this->userId,
                    'UF_PRODUCT_POSITION' => null,
                    'UF_DATETIME' => \Bitrix\Main\Type\DateTime::createFromTimestamp(\strtotime(\date('Y-m-d 00:00:00'))),
                )
            );
        }

        //set basket items (also load additional info from IBLOCK)
        $this->basketItems = current($this->basketPosition->getBasketPositions($data));

        $this->loaded = true;

        return $this;
    }

    /**
     * Returns current basket
     *
     * @return array
     *
     * @access public
     */
    public function getBasket()
    {
        //check for basket load
        $this->checkLoaded();

        return $this->basketItems;
    }

    /**
     * Returns current basket ID
     *
     * @return int
     *
     * @access public
     */
    public function getBasketId()
    {
        //check for basket load
        $this->checkLoaded();

        //get basket
        $basket = $this->getBasket();

        return isset($basket['ID']) ? (int) $basket['ID'] : 0;
    }

    /**
     * Returns products of current basket
     *
     * @return array|mixed
     *
     * @access public
     */
    public function getProducts()
    {
        //check for basket load
        $this->checkLoaded();

        return empty($this->basketItems['PRODUCTS']) ? array() : $this->basketItems['PRODUCTS'];
    }

    /**
     * Check empty basket or not
     *
     * @return bool
     *
     * @access public
     */
    public function hasProducts()
    {
        //check for basket load
        $this->checkLoaded();

        return !empty($this->basketItems['PRODUCTS']);
    }

    /**
     * Returns total basket price
     * Also, price can be formatted
     *
     * @param bool $formatted
     * @param string $additionalData
     *
     * @return int
     *
     * @access public
     */
    public function getTotalPrice($formatted = false, $additionalData = ' руб.')
    {
        if(empty($this->basketItems['PRODUCTS']))
            return 0;
        $sum = 0;
        foreach($this->basketItems['PRODUCTS'] as $product)
            $sum += $product['SUM'];
        return $formatted ? $this->formatPrice($sum, $additionalData) : $sum;
    }

    /**
     * Returns count of positions in basket
     *
     * @return int
     *
     * @access public
     */
    public function getPositionsCount()
    {
        return empty($this->basketItems['PRODUCTS']) ? 0 : count($this->basketItems['PRODUCTS']);
    }

    /**
     * Returns count of products in basket
     *
     * @return int
     *
     * @access public
     */
    public function getProductsCount()
    {
        if(empty($this->basketItems['PRODUCTS']))
            return 0;
        $cnt = 0;
        foreach($this->basketItems['PRODUCTS'] as $product)
            $cnt += $product['QUANTITY'];
        return $cnt;
    }

    /**
     * Update product quantity for specific position
     *
     * @param $positionId
     * @param $changeCount
     *
     * @return bool
     *
     * @access public
     */
    public function updateCount($positionId, $changeCount)
    {
        //check for basket load
        $this->checkLoaded();

        //product not found
        if(empty($this->basketItems['PRODUCTS'][$positionId]))
            return false;

        //get specific product
        $product = $this->basketItems['PRODUCTS'][$positionId];

        //update count
        $result = $this->updateRecord(
            Settings::POSITIONS_HLBLOCK_ID,
            $positionId,
            array(
                'UF_QUANTITY' => $product['QUANTITY'] + $changeCount,
            )
        );

        $status = $result && $result->isSuccess();

        //update basket
        $this->loadByUserId($this->userId);

        return $status;
    }

    /**
     * Update specific position by given data
     *
     * @param $positionId
     * @param array $data
     *
     * @return mixed
     *
     * @access public
     */
    public function update($positionId, array $data)
    {
        //check for basket load
        $this->checkLoaded();

        //update record
        $status = $this->updateRecord(
            Settings::POSITIONS_HLBLOCK_ID,
            $positionId,
            $data
        );

        //update basket
        $this->loadByUserId($this->userId);

        return $status;
    }

    /**
     * Add new position or update existing
     *
     * @param array $data
     *
     * @return mixed
     *
     * @access public
     */
    public function add(array $data)
    {
        //check for basket load
        $this->checkLoaded();

        //get basket
        $basket = $this->getBasket();

        //check specific data
        $checkId  = isset($data['UF_PRODUCT'])  ? (int) $data['UF_PRODUCT']  : 0;
        $quantity = isset($data['UF_QUANTITY']) ? (int) $data['UF_QUANTITY'] : 0;

        if(empty($checkId) || empty($quantity))
            return false;

        //check exists
        $foundId = false;
        foreach($this->getProducts() as $positionId => $product)
        {
            //product is found, store it for update
            if($product['PRODUCT_ID'] == $checkId)
            {
                $foundId = $positionId;
                break;
            }
        }

        //record is found, just update count
        if($foundId)
        {
            $status = $this->updateCount($foundId, $quantity);
        }
        else
        {
            //add new record
            $result = $this->addRecord(
                Settings::POSITIONS_HLBLOCK_ID,
                $data
            );

            //successfully added
            if($result && $result->isSuccess())
            {
                //add id of new position
                $basket['UF_PRODUCT_POSITION'][] = $result->getId();

                //update
                $status = $this->updateRecord(
                    Settings::BASKET_HLBLOCK_ID,
                    $basket['ID'],
                    array(
                        'UF_PRODUCT_POSITION' => $basket['UF_PRODUCT_POSITION'],
                    )
                );
            }
        }

        //update basket
        $this->loadByUserId($this->userId);

        return $status;
    }

    /**
     * Delete item from position
     * Also delete empty positions
     * 
     * @param $removeId
     *
     * @return mixed
     *
     * @access public
     */
    public function delete($removeId)
    {
        //check for basket load
        $this->checkLoaded();

        //delete record
        $status = $this->deleteRecord(
            Settings::POSITIONS_HLBLOCK_ID,
            $removeId
        );

        //update basket
        $this->loadByUserId($this->userId);

        //remove empty positions
        $status = $status && $this->updateRecord(
            Settings::BASKET_HLBLOCK_ID,
            $this->getBasketId(),
            array(
                'UF_PRODUCT_POSITION' => array_keys($this->getProducts()),
            )
        );

        //update basket
        $this->loadByUserId($this->userId);

        return $status;
    }

    /**
     * Delete all items from basket
     *
     * @return bool|mixed
     *
     * @access public
     */
    public function deleteAll()
    {
        //check for basket load
        $this->checkLoaded();

        //get basket
        $basket = $this->getBasket();

        if(empty($basket['UF_PRODUCT_POSITION']))
            return false;

        //delete
        return $this->deleteMultiple($basket['UF_PRODUCT_POSITION']);
    }

    /**
     * Delete specific items from basket (specified ids)
     *
     * @param array $removeIds
     *
     * @return mixed
     *
     * @access public
     */
    public function deleteMultiple(array $removeIds)
    {
        //check for basket load
        $this->checkLoaded();

        //delete specific records
        $status = $this->deleteRecords(
            Settings::POSITIONS_HLBLOCK_ID,
            $removeIds
        );

        //update basket
        $this->loadByUserId($this->userId);

        return $status;
    }


    /**
     * Remove old basket ( > 3 day )
     *
     * @return mixed
     *
     * @access protected
     */
    protected function deleteOldBaskets()
    {
        $dataClass = $this->getEntityDataClass(Settings::BASKET_HLBLOCK_ID);

        //search baskets for delete
        $deletePositions = array();
        $res = $dataClass::getList(array(
            'select' => array('ID', 'UF_PRODUCT_POSITION'),
            'filter' => array('<UF_DATETIME' => \Bitrix\Main\Type\DateTime::createFromTimestamp(\strtotime('-3 day'))),
        ));
        while($row = $res->fetch())
        {
            //collect positions for delete
            $deletePositions = array_merge($deletePositions, $row['UF_PRODUCT_POSITION']);
            //delete basket
            $dataClass::delete($row['ID']);
        }
        //unique positions
        $deletePositions = array_unique(array_map('intval', $deletePositions));

        //delete positions
        return $this->deleteMultiple($deletePositions);
    }

    /**
     * Check is basket loaded
     *
     * @return void
     *
     * @throws \LogicException
     *
     * @access protected
     */
    protected function checkLoaded()
    {
        if(!$this->loaded)
            throw new \LogicException('Корзина не загружена');
    }
}