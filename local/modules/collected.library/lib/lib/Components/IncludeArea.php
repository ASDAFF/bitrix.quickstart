<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Components;

/**
 * Class IncludeArea
 * @package Collected\Components
 */
class IncludeArea extends \Collected\Base\Component
{
    protected static $componentName = 'bitrix:main.include';
    protected static $params = array(
        'AREA_FILE_SHOW' => 'file',
        'PATH' => 'include/file.php',
    );
}
