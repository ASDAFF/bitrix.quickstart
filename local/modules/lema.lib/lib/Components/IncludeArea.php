<?php

namespace Lema\Components;

/**
 * Class IncludeArea
 * @package Lema\Components
 */
class IncludeArea extends \Lema\Base\Component
{
    protected static $componentName = 'bitrix:main.include';
    protected static $params = array(
        'AREA_FILE_SHOW' => 'file',
        'PATH' => 'include/file.php',
    );
}
