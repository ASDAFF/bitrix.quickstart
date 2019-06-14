<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 14:55
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Selectbox
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Selectbox extends Input
{
    /**
     * @param bool $empty
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showLabel($empty = false)
    {
        if ($this->input->isMultiple())
            parent::showMultiLabel();
        else
            parent::showLabel($empty);
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        if (!$this->input instanceof \Rover\Fadmin\Inputs\Selectbox)
            return;

        $value      = $this->input->getValue();
        $valueId    = $this->input->getValueId();
        $valueName  = $this->input->getValueName();
        $multiple   = $this->input->isMultiple();

        ?><select
        <?=$this->input->getDisabled() ? 'disabled="disabled"': '';?>
        name="<?=$valueName . ($multiple ? '[]' : '')?>"
        id="<?=$valueId?>"
        size="<?=$this->input->getSize()?>"
        <?=$multiple ? ' multiple="multiple" ' : ''?>>
        <?php

        foreach($this->input->getOptions() as $v => $k){
            if ($multiple) {
                $selected = is_array($value) && in_array($v, $value)
                    ? true
                    : false;
            } else {
                $selected = $value==$v ? true : false;
            }

            ?><option value="<?=$v?>"<?=$selected ? " selected=\"selected\" ": ''?>><?=$k?></option><?php
        }
        ?>
        </select>
        <?php
    }
}