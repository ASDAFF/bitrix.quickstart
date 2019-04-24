<?php

namespace Lema\Sberbank;

\Bitrix\Main\Loader::includeModule('iblock');

/**
 * Order object simulation
 *
 */
class NotSaleOrder
{
    private $order = null;

    private $iblockId = null;


    /**
     * NotSaleOrder constructor.
     *
     * @param $iblockId
     */
    public function __construct($iblockId)
    {
        $this->iblockId = (int) $iblockId;
    }

    /**
     * @param $iblockId
     *
     * @return static
     */
    public static function get($iblockId)
    {
        return new static($iblockId);
    }

    /**
     * load order by id
     *
     * @param $orderId
     * @return $this
     */
    public function load($elementId)
    {
        $res = \CIBlockElement::GetList(
            array(),
            array('IBLOCK_ID' => $this->iblockId, 'ID' => (int) $elementId),
            false,
            array('nTopCount' => 1),
            array('ID', 'PROPERTY_ELEMENT', 'PROPERTY_COUNT')
        );
        if($row = $res->Fetch())
        {
            $pRes = \CIBlockElement::GetList(
                array(),
                array('ID' => (int) $row['PROPERTY_ELEMENT_VALUE']),
                false,
                array('nTopCount' => 1),
                array('PROPERTY_PRICE', 'PROPERTY_QUANTITY')
            );
            if($pRow = $pRes->Fetch())
            {
                $row = array_merge($row, $pRow);
            }
            $this->order = $row;
        }

        return $this;
    }

    /**
     * load order by account number
     *
     * @param $orderNumber
     * @return $this
     */
    public function loadByAccNumber($orderNumber)
    {
        $res = \CIBlockElement::GetList(
            array(),
            array('IBLOCK_ID' => $this->iblockId, 'NAME' => $orderNumber),
            false,
            array('nTopCount' => 1),
            array('ID', 'PROPERTY_ELEMENT', 'PROPERTY_COUNT')
        );

        if($row = $res->Fetch())
        {
            $pRes = \CIBlockElement::GetList(
                array(),
                array('ID' => (int) $row['PROPERTY_ELEMENT_VALUE']),
                false,
                array('nTopCount' => 1),
                array('PROPERTY_PRICE', 'PROPERTY_QUANTITY')
            );
            if($pRow = $pRes->Fetch())
            {
                $row = array_merge($row, $pRow);
            }
            $this->order = $row;
        }

        return $this;
    }

    /**
     * Returns current element (order) id
     *
     * @return bool|int
     */
    public function getId()
    {
        return isset($this->order['ID']) ? (int) $this->order['ID'] : false;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setField($name, $value)
    {
        \CIBlockElement::SetPropertyValuesEx($this->getId(), $this->iblockId, array($name => $value));

        return $this;
    }

    /**
     * Check can be order payed
     *
     * @return bool
     */
    public function canPay()
    {
        return isset($this->order['PROPERTY_QUANTITY_VALUE'], $this->order['PROPERTY_COUNT_VALUE']) &&
            $this->order['PROPERTY_QUANTITY_VALUE'] > 0 &&
            $this->order['PROPERTY_QUANTITY_VALUE'] >= $this->order['PROPERTY_COUNT_VALUE'];
    }

    /**
     * Set order payed status
     *
     * @param $value
     *
     * @return NotSaleOrder
     */
    public function setPayd($value)
    {
        return $this->setField('PAYED', array('VALUE' => $value));
    }

    /**
     * Returns payed order or not
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->order['PROPERTY_PAYED_VALUE'] == 'Да';
    }

    /**
     * Returns current order full price
     *
     * @return int|float
     */
    public function getPrice()
    {
        $price  = empty($this->order['PROPERTY_PRICE_VALUE']) ? 0 : $this->order['PROPERTY_PRICE_VALUE'];
        $price *= empty($this->order['PROPERTY_COUNT_VALUE']) ? 1 : $this->order['PROPERTY_COUNT_VALUE'];
        return $price;
    }

    /**
     * Reload order (save new data to order)
     *
     * @return bool
     */
    public function save()
    {
        $this->load($this->getId());
        return true;
    }
}
