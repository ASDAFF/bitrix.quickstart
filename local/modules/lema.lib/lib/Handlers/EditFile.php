<?php

namespace Lema\Handlers;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class File
 * @package Lema\Handlers
 */
class EditFile
{
    /**
     * @param array $arFields
     */
    public static function beforeChange($abs_path, $content)
    {
        if(false === strpos($abs_path, '/personal/profile/index.php'))
            return true;

        $GLOBALS['APPLICATION']->ThrowException('Изменение данного файла запрещено через визуальный редактор.');

        return false;
    }
}