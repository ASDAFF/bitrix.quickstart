<?php

namespace Lema\Template;

/**
 * Class Item
 * @package Lema\Template
 */
class Item
{
    /**
     * @var array
     */
    protected $arItem = array();

    /**
     * @var null
     */
    protected $editAreaId = null;

    /**
     * Item constructor.
     * @param array $arItem
     * @param null $editAreaId
     */
    public function __construct(array $arItem, $editAreaId = null)
    {
        if(isset($editAreaId))
            $this->editAreaId = $editAreaId;

        $this->arItem = $arItem;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        return isset($this->arItem[$name]) ? $this->arItem[$name] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function propEmpty($key)
    {
        return empty($this->arItem['PROPERTIES'][$key]['VALUE']);
    }

    /**
     * @param $key
     * @return bool
     */
    public function propFilled($key)
    {
        return !$this->propEmpty($key);
    }

    /**
     * @param $name
     * @param bool $key
     * @return null
     */
    public function prop($name, $key = false)
    {
        if(empty($key))
            return isset($this->arItem['PROPERTIES'][$name]) ? $this->arItem['PROPERTIES'][$name] : null;

        return isset($this->arItem['PROPERTIES'][$name][$key]) ? $this->arItem['PROPERTIES'][$name][$key] : null;
    }

    /**
     * @param $name
     * @return null
     */
    public function propName($name)
    {
        return $this->prop($name, 'NAME');
    }

    /**
     * @param $name
     * @return null
     */
    public function propVal($name)
    {
        return $this->prop($name, 'VALUE');
    }
    /**
     * @param $name
     * @return null
     */
    public function propValue($name)
    {
        return $this->prop($name, 'VALUE');
    }
    /**
     * @param $name
     * @return null
     */
    public function propText($name)
    {
        $prop = $this->propValue($name);
        return isset($prop, $prop['TEXT']) ? $prop['TEXT'] : null;
    }

    /**
     * @param $name
     * @return null
     */
    public function propXmlId($name)
    {
        return $this->prop($name, 'VALUE_XML_ID');
    }

    /**
     * @param $name
     * @return null
     */
    public function propId($name)
    {
        return $this->prop($name, 'ID');
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->get('ID');
    }

    /**
     * @return mixed|null
     */
    public function getName()
    {
        return $this->get('NAME');
    }

    /**
     * @return mixed|null
     */
    public function previewText()
    {
        return $this->get('PREVIEW_TEXT');
    }

    /**
     * @return mixed|null
     */
    public function detailText()
    {
        return $this->get('DETAIL_TEXT');
    }

    /**
     * @return mixed|null
     */
    public function listUrl()
    {
        return $this->get('LIST_PAGE_URL');
    }

    /**
     * @return mixed|null
     */
    public function sectionUrl()
    {
        return $this->get('SECTION_PAGE_URL');
    }

    /**
     * @return mixed|null
     */
    public function detailUrl()
    {
        return $this->get('DETAIL_PAGE_URL');
    }

    /**
     * @return null
     */
    public function previewPicture($key = 'SRC')
    {
        if(empty($this->arItem['PREVIEW_PICTURE']))
            return false;
        return empty($this->arItem['PREVIEW_PICTURE'][$key]) ? null : $this->arItem['PREVIEW_PICTURE'][$key];
    }

    /**
     * @return null
     */
    public function detailPicture($key = 'SRC')
    {
        if(empty($this->arItem['DETAIL_PICTURE']))
            return false;
        return empty($this->arItem['DETAIL_PICTURE'][$key]) ? null : $this->arItem['DETAIL_PICTURE'][$key];
    }

    /**
     * @param bool $withHtml
     * @return null|string
     */
    public function editId($withHtml = true)
    {
        return $withHtml ? ' id="' . $this->editAreaId . '"' : $this->editAreaId;
    }
}