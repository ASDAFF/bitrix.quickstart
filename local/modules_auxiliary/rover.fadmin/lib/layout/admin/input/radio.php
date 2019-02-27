<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:43
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Radio
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Radio extends Input
{
    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        if (!$this->input instanceof \Rover\Fadmin\Inputs\Radio)
            return;

        $value = $this->input->getValue();

        foreach ($this->input->getOptions() as $optionValue => $optionName):

            ?><label><input
            type="radio"
            <?=$this->input->getDisabled() ? 'disabled="disabled"': '';?>
            name="<?=$this->input->getValueName()?>"
            id="<?=$this->input->getValueId()?>"
            value="<?=$optionValue?>"
            <?=$value == $optionValue ? ' checked="checked "' : ''?>
            ><?=$optionName?></label><?php

        endforeach;
    }
}