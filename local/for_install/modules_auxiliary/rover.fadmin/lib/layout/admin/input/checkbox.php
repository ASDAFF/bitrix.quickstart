<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 14:53
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Checkbox
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Checkbox extends Input
{
    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        ?><input
            type="checkbox"
            <?=$this->input->getDisabled() ? 'disabled="disabled"': '';?>
            id="<?=$this->input->getValueId()?>"
            name="<?=$this->input->getValueName()?>"
            value="Y"<?=($this->input->getValue() == "Y")?" checked=\"checked\"":'';?>/><?php
    }
}