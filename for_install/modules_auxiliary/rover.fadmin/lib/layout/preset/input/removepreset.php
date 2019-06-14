<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:16
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Preset\Input;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Removepreset
 *
 * @package Rover\Fadmin\Layout\Preset\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Removepreset extends Submit
{
    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function draw()
    {
        $this->adminInput->draw();
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        $this->adminInput->showInput();
    }
}