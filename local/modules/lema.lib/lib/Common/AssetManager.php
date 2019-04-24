<?php

namespace Lema\Common;


/**
 * Class AssetManager
 * @package Lema\Common
 */
class AssetManager extends \Lema\Base\StaticInstance
{
    /**
     * @var \Bitrix\Main\Page\Asset|null
     */
    private $assets = null;

    /**
     * AssetManager constructor.
     *
     * @access public
     */
    public function __construct()
    {
        $this->assets = Asset::get();
    }

    /**
     * Add js files from array to asset list
     *
     * @param array $data
     * @return $this
     *
     * @access public
     */
    public function addJsArray(array $data = array())
    {
        foreach($data as $file)
            $this->assets->addJs($file);
        return $this;
    }

    /**
     * Add css files from array to asset list
     *
     * @param array $data
     * @return $this
     *
     * @access public
     */
    public function addCssArray(array $data = array())
    {
        foreach($data as $file)
            $this->assets->addCss($file);
        return $this;
    }

    /**
     * Wrapper of \CJSCore::init for simple call
     *
     * @param array $data
     * @return $this
     *
     * @access public
     */
    public function init(array $data = array())
    {
        \CJSCore::init($data);
        return $this;
    }

}