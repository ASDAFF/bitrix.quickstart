<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:37
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Preset\Input;

use Rover\Fadmin\Layout\Preset\Input;

/**
 * Class Iblock
 *
 * @package Rover\Fadmin\Layout\Preset\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Iblock extends Input
{
    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function showInput()
    {
        $this->adminInput->showInput();
    }
}