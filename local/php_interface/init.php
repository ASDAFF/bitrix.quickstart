<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/constants.php"))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/constants.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/handlers.php"))
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/handlers.php");


use BitrixQuickStart;

// autoloading
require_once(dirname(__FILE__) . '/Autoloader.php');
$autoloader = new \BitrixQuickStart\Autoloader();

/**
 * Event handling.
 *
 * We strongly recommend to group event handlers in classes.
 *
 * For example, you can handle events "OnBeforeUserAdd" and "OnBeforeUserUpdate"
 * with methods UserHandlers::OnBeforeUserAdd() and UserHandlers::OnBeforeUserUpdate(), like this:
 *
 * AddEventHandler("main", "OnBeforeUserAdd", Array("UserHandlers", "OnBeforeUserAdd"));
 */
