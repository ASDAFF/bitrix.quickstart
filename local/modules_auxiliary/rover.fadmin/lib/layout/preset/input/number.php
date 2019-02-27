<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:40
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Preset\Input;

class Number extends Text
{
    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        $this->adminInput->showInput();
    }
}