<?php

namespace Lema\Seo;

use \Lema\Common\Helper;
/**
 * Class GTMFormSubmit
 * @package Lema\Seo
 */
class GTMFormSubmit extends \Lema\Base\StaticInstance
{
    /**
     * @var event name
     */
    protected $event = null;
    /**
     * @var element classes
     */
    protected $elementClasses = null;
    /**
     * @var element classes
     */
    protected $elementId = null;
    /**
     * @var array of elements
     */
    protected $elements = array();

    /**
     * Set event name
     *
     * @param string $event event name
     * @return $this
     *
     * @access public
     */
    public function setEvent($event = 'gtm.formSubmit')
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Set element class(es)
     *
     * @param $classes element class(es)
     * @return $this
     *
     * @access public
     */
    public function setElementClasses($classes)
    {
        $this->elementClasses = $classes;
        return $this;
    }

    /**
     * Set element id
     *
     * @param $classes element id
     * @return $this
     *
     * @access public
     */
    public function setElementId($id)
    {
        $this->elementId = $id;
        return $this;
    }

    /**
     * Set element value by index
     *
     * @param $index element index
     * @param $value element value
     * @return $this
     *
     * @access public
     */
    public function setElement($index, $value)
    {
        $this->elements[$index] = $value;
        return $this;
    }

    /**
     * Set elements from array
     *
     * @param array $data array of element values
     * @return $this
     *
     * @access public
     */
    public function setElements(array $data = array())
    {
        foreach($data as $index => $value)
            $this->setElement($index, $value);
        return $this;
    }
    /**
     * Return json string or array of gtm.formsubmit data
     *
     * @return string|array
     *
     * @access public
     */
    public function getResult($inJson = false)
    {
        $result = array(
            'event' => $this->event,
        );

        if(!empty($this->elementClasses))
            $result['gtm.elementClasses'] = $this->elementClasses;
        if(!empty($this->elementId))
            $result['formId'] = $this->elementId;

        foreach($this->elements as $index => $value)
            $result['gtm.element.' . $index . '.value'] = $value;
        return $inJson ? Helper::getJson($result) : $result;
    }
    /**
     * Return generated script
     *
     * @param bool $return
     * @return string
     *
     * @access public
     */
    public function jsonResult($return = false, $jsonOptions = JSON_UNESCAPED_UNICODE)
    {
        $ret  = '<script type="text/javascript">window.dataLayer = window.dataLayer || [];' . PHP_EOL;
        $ret .= 'dataLayer.push(' . Helper::getJson($this->getResult(), $jsonOptions) . ');' . PHP_EOL;
        $ret .= '</script>' . PHP_EOL;
        if($return)
            return $ret;
        echo $ret;
    }
}