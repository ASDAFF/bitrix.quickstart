<?php

namespace Lema\Basket;

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('highloadblock');

/**
 * Class HighloadBlock
 * @package Lema\Basket
 */
class HighloadBlock
{
    /**
     * @var array $hlEntities - array of compiled entities (for cache)
     */
    protected $hlEntities = array();


    /**
     * Returns formatted price
     *
     * @param $price
     * @param string $additionalData
     *
     * @return string
     *
     * @access public
     */
    public function formatPrice($price, $additionalData = ' руб.')
    {
        return number_format($price, 2, '.', ' ') . $additionalData;
    }


    /**
     * Returns compiled entity
     *
     * @param $hIblockId
     *
     * @return mixed
     *
     * @access protected
     */
    protected function getEntity($hIblockId)
    {
        //entity not compiled yet, compile it
        if(empty($this->hlEntities[$hIblockId]))
        {
            $hlData = \Bitrix\Highloadblock\HighloadBlockTable::getById($hIblockId)->fetch();
            $this->hlEntities[$hIblockId] = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlData);
        }

        //return compiled entity
        return $this->hlEntities[$hIblockId];
    }

    /**
     * Returns data class of compiled entity
     *
     * @param $hIblockId
     *
     * @return mixed
     *
     * @access protected
     */
    protected function getEntityDataClass($hIblockId)
    {
        return $this->getEntity($hIblockId)->getDataClass();
    }

    /**
     * Returns Query object for specific hliblock (query builder)
     *
     * @param $hIblockId
     *
     * @return \Bitrix\Main\Entity\Query
     *
     * @access protected
     */
    protected function getQueryBuilder($hIblockId)
    {
        return new \Bitrix\Main\Entity\Query($this->getEntity($hIblockId));
    }

    /**
     * Update specific record by specific id with given data
     *
     * @param $hlBlockId
     * @param $itemId
     * @param array $data
     *
     * @return mixed
     *
     * @access protected
     */
    protected function updateRecord($hlBlockId, $itemId, array $data)
    {
        $dataClass = $this->getEntityDataClass($hlBlockId);

        //update record
        return $dataClass::update($itemId, $data);
    }

    /**
     * Add record to specific hlblock with given data
     *
     * @param $hlBlockId
     * @param array $data
     *
     * @return mixed
     *
     * @access protected
     */
    protected function addRecord($hlBlockId, array $data)
    {
        $dataClass = $this->getEntityDataClass($hlBlockId);

        //add record
        return $dataClass::add($data);
    }

    /**
     * Delete records from specific hlblock
     *
     * @param $hlBlockId
     * @param array $removeIds
     *
     * @return bool
     *
     * @access protected
     */
    protected function deleteRecords($hlBlockId, array $removeIds)
    {
        $dataClass = $this->getEntityDataClass($hlBlockId);

        $status = true;

        //delete records
        foreach($removeIds as $removeId)
            $status = $status && $dataClass::delete($removeId);

        return $status;
    }

    /**
     * Delete record from specific block
     *
     * @param $hlBlockId
     * @param $removeId
     *
     * @return mixed
     *
     * @access protected
     */
    protected function deleteRecord($hlBlockId, $removeId)
    {
        $dataClass = $this->getEntityDataClass($hlBlockId);

        //delete record
        return $dataClass::delete($removeId);
    }

    /**
     * Returns exists or generated userID
     *
     * @param $userId
     *
     * @return mixed
     *
     * @access protected
     */
    protected function getOrGenerateUserId($userId)
    {
        if(null === $userId)
            $userId = Settings::getUserId();
        return $userId;
    }
}