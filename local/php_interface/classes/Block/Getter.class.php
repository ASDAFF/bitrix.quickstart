<?php

namespace Cpeople\Classes\Block;

class Getter extends \Cpeople\Classes\Base\Getter
{
    protected $arGroupBy = null;
    protected $arNavStartParams = null;
    protected $arSelectFields = null;
    protected $className = '\Cpeople\Classes\Block\Object';

    protected $total;

    /**
     * @static
     * @return Getter
     */
    static function instance()
    {
        return new self;
    }

    /**
     * @return Getter
     */
    public function setGroupBy($arGroupBy)
    {
        $this->arGroupBy = $arGroupBy;
        return $this;
    }

    /**
     * @return Getter
     */
    public function setNavStartParams($arNavStartParams)
    {
        $this->arNavStartParams = $arNavStartParams;
        return $this;
    }

    /**
     * @return Getter
     */
    public function setPageSize($size)
    {
        $this->arNavStartParams['nPageSize'] = (int) $size;
        return $this;
    }

    /**
     * @param $pageNum
     * @return Getter
     */
    public function setPageNum($pageNum)
    {
        $this->arNavStartParams['iNumPage'] = (int) $pageNum;
        return $this;
    }

    /**
     * @param $pagingSize
     * @param $pageNum
     * @return Getter
     */
    public function paginate($pagingSize, $pageNum)
    {
        $this->setPageSize($pagingSize);
        $this->setPageNum(intval($pageNum) < 1 ? 1 : intval($pageNum));
        return $this;
    }

    /**
     * @return Getter
     */
    public function setSelectFields($arSelectFields)
    {
        $this->arSelectFields = $arSelectFields;
        return $this;
    }

    /**
     * @return Getter
     */
    public function addCallback($callback)
    {
        if (!is_callable($callback))
        {
            throw new \Exception('Passed callback is not callable, ' . __METHOD__);
        }

        $this->callbacks[] = $callback;
        return $this;
    }

    /**
     * @return Getter
     */
    public function setResultSetCallback($callback)
    {
        if (!is_callable($callback))
        {
            throw new \Exception('Passed callback is not callable, ' . __METHOD__);
        }

        $this->resultSetCallback = $callback;
        return $this;
    }

    /**
     * @return \CDBResult|\CIBlockResult|mixed|string
     */
    public function getResult()
    {
        $element = new \CIBlockElement;

        return $element->GetList(
            $this->arOrder,
            $this->arFilter,
            empty($this->arGroupBy) ? null : $this->arGroupBy,
            empty($this->arNavStartParams) ? null : $this->arNavStartParams,
            $this->arSelectFields
        );
    }

    /**
     * @return \Cpeople\Classes\Block\Object[]
     */
    public function get()
    {
        if (\Cpeople\Classes\Registry::bitrixCacheEnabled() && ($retval = $this->getCachedResult()))
        {
            return $retval;
        }

        $retval = array();

        $resultSet = $this->getResult();

        if (isset($this->resultSetCallback))
        {
            $resultSet = call_user_func($this->resultSetCallback, $resultSet);
        }

        $key = -1;

        while ($obRes = $resultSet->GetNextElement())
        {
            switch ($this->fetchMode)
            {
                case self::FETCH_MODE_FIELDS:
                    $element = $obRes->GetFields();
                    break;

                case self::FETCH_MODE_PROPERTIES:
                    $element = $obRes->getProperties();
                    break;

                default:
                    $element = array_merge($obRes->GetFields(), $obRes->getProperties());
                    break;
            }

            foreach ((array) $this->callbacks as $callback)
            {
                if ($callbackResult = call_user_func($callback, $element))
                {
                    $element = $callbackResult;
                }
            }

            $key = $this->hydrateById ? $element['ID'] : ++$key;

            switch ($this->hydrationMode)
            {
                case self::HYDRATION_MODE_OBJECTS_ARRAY:
                case self::HYDRATION_MODE_OBJECTS_COLLECTION:
                    $className = $this->className;
                    $retval[$key] = new $className($element);
                break;


                default:
                    $retval[$key] = $element;
                break;
            }
        }

        if ($this->hydrationMode == self::HYDRATION_MODE_OBJECTS_COLLECTION)
        {
            $retval = new Collection($retval);
        }

        if (\Cpeople\Classes\Registry::bitrixCacheEnabled())
        {
            $this->cacheResult($retval);
        }

        return $retval;
    }

    /**
     * @return \Cpeople\Classes\Block\Object
     */
    public function getByCode($code, $iblockId = null)
    {
        $this->setHydrationMode(self::HYDRATION_MODE_OBJECTS_ARRAY)->addFilter('CODE', $code);

        if ($iblockId)
        {
            $this->addFilter('IBLOCK_ID', $iblockId);
        }

        return $this->getOne();
    }

    public function getFoundRows()
    {
        $getter = clone $this;

        // в фильтре есть сложная логика, простая группировка не даст нужного
        // результата, получаем все элементы и считаем из
        if (true || array_key_exists(0, $this->arFilter))
        {
            $getter
                ->setNavStartParams(array())
                ->setOrder(array())
                ->setFetchMode(self::FETCH_MODE_FIELDS)
                ->setHydrationMode(self::HYDRATION_MODE_ARRAY)
                ->setSelectFields(array('ID'))
            ;

            if ($this->cacheManager)
            {
                $getter->setCacheManager($this->cacheManager);
            }

            $this->total = (int) count($getter->get());
        }
        else
        {
            $res = $getter
                ->setNavStartParams(null)
                ->setGroupBy(array('IBLOCK_ID'))
                    ->getResult()
                        ->Fetch();

            $this->total = empty($res) ? false : $res['CNT'];
        }

        return $this->total;
    }

    /**
     * @return \paging
     */
    public function getPagingObject($urlTemplate, $total = null)
    {
        if (isset($total))
        {
            $this->total = $total;
        }

        if (!isset($this->total))
        {
            $this->total = $this->getFoundRows();
        }

        $paging = new \Cpeople\paging($this->arNavStartParams['iNumPage'], $this->total, $this->arNavStartParams['nPageSize']);
        $paging->setFormat($urlTemplate);

        return $paging;
    }
}
