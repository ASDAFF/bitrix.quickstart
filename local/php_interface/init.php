<?php
/**
 * Please no code in this file. Structure your local folder
 */

use BitrixQuickStart;

//Autoload
require_once(dirname(__FILE__) . '/classes/Autoloader.php');
$autoloader = new \BitrixQuickStart\Autoloader();

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php'))
    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');

//Consts
if (file_exists(__DIR__ . '/config/const.php'))
    require_once(__DIR__ . '/config/const.php');

//Config
if (file_exists(__DIR__ . '/config/frontend.php'))
    require_once(__DIR__ . '/config/frontend.php');

//Events
if (file_exists(__DIR__ . '/config/events.php'))
    require_once(__DIR__ . '/config/events.php');

//Handlers
if (file_exists(__DIR__ . '/include/handlers.php'))
    require_once(__DIR__ . '/include/handlers.php');

// BitrixHelper
if (file_exists(__DIR__ . '/vendor/bitrix-helper/src/autoload.php'))
    require_once(__DIR__ . '/vendor/bitrix-helper/src/autoload.php');


// Окружение
if (file_exists(__DIR__ . '/vendor/bitrix-helper/src/autoload.php'))
    require_once(__DIR__ . '/helpers/environment.php');