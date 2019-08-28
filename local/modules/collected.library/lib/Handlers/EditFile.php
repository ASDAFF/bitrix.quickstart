<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Handlers;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class File
 * @package Collected\Handlers
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