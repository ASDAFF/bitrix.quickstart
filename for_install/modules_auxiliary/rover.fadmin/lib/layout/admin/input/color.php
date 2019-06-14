<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:23
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Color
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Color extends Input
{
    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        ?><input
        <?=$this->input->getDisabled() ? 'disabled="disabled"': '';?>
        id="<?=$this->input->getValueId()?>"
        type="color"
        value="<?=$this->input->getValue()?>"
        name="<?=$this->input->getValueName()?>"><?php
    }
}