<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:31
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Rover\Fadmin\Layout\Admin\Input;

/**
 * Class File
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class File extends Input
{
    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        if (!$this->input instanceof \Rover\Fadmin\Inputs\File)
            return;

        $valueName  = $this->input->getValueName();
        $isImage    = $this->input->isImage();
        $value      = intval($this->input->getValue());

        if ($value):

            $file = \CFile::GetFileArray($value);

            echo '<code>' . $file['ORIGINAL_NAME'] . '</code><br>';

            if ($this->input->isImage())
                echo \CFile::ShowImage($value, 200, 200, "border=0", "", true) . '<br>';

        endif;

        $fileType = $isImage ? 'IMAGE' : '';

        echo \CFile::InputFile($valueName, $this->input->getSize(), $value, false, $this->input->getMaxSize(),
                $fileType, "class=typefile", 0, "class=typeinput", '', false, false)
            . '<br>';
    }
}