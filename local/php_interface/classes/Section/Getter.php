<?php

namespace Section;

class Getter extends \Base\Getter
{
    protected $arOrder = array('SORT' => 'asc');
    protected $arFilter = null;
    protected $arNavStartParams = null;
    protected $arSelectFields = null;
    protected $bIncCnt = false;
    protected $bAddUFToSelect = true;
    protected $className = '\Section\ObjectSection';

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
    public function fetchUserFields($value)
    {
        $this->fetchUserFields = (bool) $value;
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
    public function setSelectFields($arSelectFields)
    {
        $this->arSelectFields = $arSelectFields;
        return $this;
    }

    /**
     * @return Getter
     */
    public function setIncludeCount($value)
    {
        $this->bIncCnt = (bool) $value;
        return $this;
    }

    /**
     * @return Getter
     */
    public function checkPermissions($value)
    {
        $this->addFilter('CHECK_PERMISSIONS', ($value ? 'Y' : 'N'));
        return $this;
    }

    /**
     * @param $bAddUFToSelect
     * @return $this
     */
    public function setAddUFToSelect($bAddUFToSelect)
    {
        $this->bAddUFToSelect = $bAddUFToSelect;
        return $this;
    }

    /**
     * @return \Section\Object[]
     */
    public function get()
    {
        if (\Registry::bitrixCacheEnabled() && ($retval = $this->getCachedResult()))
        {
            return $retval;
        }

        $retval = array();

        if (!is_array($this->arSelectFields))
        {
            $this->arSelectFields = array();
        }

        if($this->bAddUFToSelect)
        {
            $this->arSelectFields[] = 'UF_*';
        }

        $resultSet = \CIBlockSection::GetList($this->arOrder, $this->arFilter, $this->bIncCnt, $this->arSelectFields, $this->arNavStartParams);

        if (isset($this->resultSetCallback))
        {
            $resultSet = call_user_func($this->resultSetCallback, $resultSet);
        }

        while ($section = $resultSet->GetNext())
        {
            foreach ((array) $this->callbacks as $callback)
            {
                if ($callbackResult = call_user_func($callback, $section))
                {
                    $section = $callbackResult;
                }
            }

            $key = $this->hydrateById ? $section['ID'] : count($retval);

            switch ($this->hydrationMode)
            {
                case self::HYDRATION_MODE_OBJECTS_ARRAY:
                    $className = $this->className;
                    $retval[$key] = new $className($section);
//                    $retval[$key] = new Object($section);
                break;


                default:
                    $retval[$key] = $section;
                break;
            }
        }

        if (\Registry::bitrixCacheEnabled())
        {
            $this->cacheResult($retval);
        }

        return $retval;
    }
}
