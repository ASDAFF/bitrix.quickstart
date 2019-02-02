<?php

namespace Cpeople\Classes\Infoblock;

class ObjectClass extends \Cpeople\Classes\Base\Object
{
    protected $properties;
    protected $fields;

    static $standardFields = array(
        'ID', 'CODE', 'EXTERNAL_ID', 'XML_ID', 'NAME',
        'IBLOCK_ID', 'IBLOCK_SECTION_ID',
        'ACTIVE', 'DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO',
        'SORT', 'PREVIEW_PICTURE', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE',
        'DETAIL_PICTURE', 'DETAIL_TEXT', 'DETAIL_TEXT_TYPE',
        'MODIFIED_BY', 'TAGS'
    );

    public function getProperties()
    {
        if (!isset($this->properties))
        {
            $this->properties = array();

            $rs = \CIBlockProperty::GetList(array('sort' => 'asc'), array('IBLOCK_ID' => $this->id));

            while($ar = $rs->Fetch())
            {
                $this->properties[$ar['CODE']] = new Property($ar);
            }
        }

        return $this->properties;
    }

    public function getProperty($key)
    {
        $this->getProperties();

        if (!array_key_exists($key, $this->properties))
        {
            throw new \Exception("Infoblock $this->id does not have property $key");
        }

        return $this->properties[$key];
    }

    public function getFields()
    {
        if (!isset($this->fields))
        {
            $this->fields = array();

            $res = \CIBlock::GetFields($this->id);

            foreach ($res as $k => $element)
            {
                $this->fields[$k] = new Field($k, $element);
            }

            foreach (self::$standardFields as $field)
            {
                if (!array_key_exists($field, $this->fields))
                {
                    $this->fields[$field] = array('CODE' => $field);
                }
            }
        }

        return $this->fields;
    }

    public function addProperty($propData)
    {
        $ibp = new \CIBlockProperty;
        return (bool) $ibp->Add($propData);
    }
}
