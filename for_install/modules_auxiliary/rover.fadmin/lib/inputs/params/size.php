<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 14:58
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Inputs\Params;

/**
 * Trait Size
 *
 * @package Rover\Fadmin\Inputs\Params
 */
trait Size
{
    /**
     * @var
     */
    protected $size;

    /**
     * @return int
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param $size
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setSize($size)
    {
        $this->size = intval($size);

        return $this;
    }
}