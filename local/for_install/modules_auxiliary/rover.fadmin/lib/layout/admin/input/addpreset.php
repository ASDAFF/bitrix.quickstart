<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:07
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Bitrix\Main\Localization\Loc;
use \Rover\Fadmin\Inputs\Addpreset as AddPresetInput;

Loc::loadMessages(__FILE__);
/**
 * Class Addpreset
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Addpreset extends Submit
{
    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function draw()
    {
        $this->showLabel(true);
        $this->showInput();
        $this->showHelp();
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        if (!$this->input instanceof AddPresetInput)
            return;

        $this->customInputName  = \Rover\Fadmin\Inputs\Addpreset::$type;
        $this->customInputValue = $this->input->getTab()->getSiteId() . AddPresetInput::SEPARATOR . $this->input->getDefault();

        parent::showInput();

        $popup = $this->input->getPopup();

        if ($popup === false)
            return;

        $text       = $popup ? : Loc::getMessage('rover-fa__ADDPRESET_TEXT');
        $default    = $this->input->getDefault() ?: Loc::getMessage('rover-fa__ADDPRESET_DEFAULT');

        ?>
        <script>
            (function()
            {
                document.getElementById('<?=$this->input->getValueId()?>').onclick = function()
                {
                    var presetName = prompt('<?=$text ?>', '<?=$default?>');

                    if (presetName == null)
                        return false;

                    if (!presetName.length) {
                        alert('<?=Loc::getMessage('rover-fa__ADDPRESET_ALERT')?>');
                        return false;
                    }

                    this.setAttribute('value', '<?=$this->input->getTab()->getSiteId() . AddPresetInput::SEPARATOR?>' + presetName);
                    return true;
                }
            })();
        </script>
        <?php
    }
}