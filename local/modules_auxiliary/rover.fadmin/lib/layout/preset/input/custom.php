<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 14:21
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Preset\Input;

use Rover\Fadmin\Layout\Preset\Input;

/**
 * Class Custom
 *
 * @package Rover\Fadmin\Layout\Preset\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Custom extends Input
{
    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function draw()
    {
        echo $this->input->getLabel();
    }

    /**
     * for capability
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput(){}
}