<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 14:31
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Text
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 *
 * @param \Rover\Fadmin\Inputs\Text $input
 */
class Text extends Input
{
    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        if (!$this->input instanceof \Rover\Fadmin\Inputs\Text)
            return;

        ?><input
            type="text"
            <?=$this->input->getDisabled() ? 'disabled="disabled"': '';?>
            id="<?=$this->input->getValueId()?>"
            size="<?=$this->input->getSize()?>"
            maxlength="<?=$this->input->getMaxLength()?>"
            value="<?=$this->input->getValue()?>"
            name="<?=$this->input->getValueName()?>"><?php
    }
}