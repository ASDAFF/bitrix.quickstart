<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:00
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Inputs\Params;

/**
 * Trait MaxLength
 *
 * @package Rover\Fadmin\Inputs\Params
 */
trait MaxLength
{
    /**
     * @var int
     */
    protected $maxLength;

    /**
     * @return int
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param $maxLength
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = intval($maxLength);

        return $this;
    }
}