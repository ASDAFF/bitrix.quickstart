<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:35
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Hidden
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Hidden extends Text
{
    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function draw()
    {
        $this->showInput();
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        if (!$this->input instanceof \Rover\Fadmin\Inputs\Hidden)
            return;

        ?><input
        <?=$this->input->getDisabled() ? 'disabled="disabled"': '';?>
        id="<?=$this->input->getValueId()?>"
        maxlength="<?=$this->input->getMaxLength()?>"
        type="hidden"
        value="<?=$this->input->getValue()?>"
        name="<?=$this->input->getValueName()?>"><?php
    }
}