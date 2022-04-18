<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('digitalwand.mvc',
    array(
        'DigitalWand\MVC\BaseComponent' => 'lib/BaseComponent.php',
        'DigitalWand\MVC\AjaxException' => 'lib/AjaxException.php',
        'DigitalWand\MVC\RestException' => 'lib/RestException.php',
        'DigitalWand\MVC\ActionTrait' => 'lib/ActionTrait.php',
        'DigitalWand\MVC\ComponentBuilder' => 'lib/ComponentBuilder.php',
    )
);
