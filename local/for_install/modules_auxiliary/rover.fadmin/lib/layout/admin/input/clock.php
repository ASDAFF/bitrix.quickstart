<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:22
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Clock
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Clock extends Input
{
    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        global $APPLICATION;

        $APPLICATION->IncludeComponent("bitrix:main.clock", "", array(
            "INPUT_ID"      => "",
            "INPUT_NAME"    => $this->input->getValueName(),
            "INPUT_TITLE"   => $this->input->getLabel(),
            "INIT_TIME"     => $this->input->getValue(),
            "STEP"          => "5"
        ),
            array('HIDE_ICONS' => 'Y')
        );
    }
}