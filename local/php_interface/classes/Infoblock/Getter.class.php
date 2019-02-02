<?php

namespace Cpeople\Classes\Infoblock;

class Getter extends \Cpeople\Classes\Base\Getter
{
    protected $className = '\Cpeople\Classes\Infoblock\Object';

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
     * @return \CDBResult|\CIBlockResult|mixed|string
     */
    public function getResult()
    {
        $element = new \CIBlockElement;

        return \CIBlock::GetList(
            $this->arOrder,
            $this->arFilter,
            false
        );
    }

    public function get()
    {
        $retval = array();

        $resultSet = $this->getResult();

        if (isset($this->resultSetCallback))
        {
            $resultSet = call_user_func($this->resultSetCallback, $resultSet);
        }

        $key = -1;

        while ($element = $resultSet->Fetch())
        {
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

        return $retval;
    }
}
