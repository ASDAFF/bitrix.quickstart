<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:44
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Inputs\Params;

trait Options
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param array $options
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function getOptions()
    {
        return $this->options;
    }
}