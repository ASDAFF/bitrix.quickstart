<?php

namespace Lm\Components;

/**
 * Class IncludeArea
 * @package Lm\Components
 */
class IncludeArea extends \Lm\Base\Component
{
    protected static $componentName = 'bitrix:main.include';
    protected static $params = array(
        'AREA_FILE_SHOW' => 'file',
        'PATH' => 'include/file.php',
    );
}
