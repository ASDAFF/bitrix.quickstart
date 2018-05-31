<?php
/**
 * Please no code in this file. Structure your local folder
 */
use BitrixQuickStart;


//Autoload
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');
require_once(dirname(__FILE__) . '/classes/Autoloader.php');
$autoloader = new \BitrixQuickStart\Autoloader();


//Consts
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/constants.php'))
    require_once(__DIR__ . '/include/constants.php');
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/config/const.php'))
    require_once($_SERVER["DOCUMENT_ROOT"] . '/local/config/const.php');

//Config
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/config/frontend.php'))
    require_once($_SERVER["DOCUMENT_ROOT"] . '/local/config/frontend.php');

//Handlers
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/handlers.php'))
    require_once(__DIR__ . '/include/handlers.php');

//Helpers
require_once(__DIR__ . '/include/helpers.php');

//Events
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/config/events.php'))
    require_once($_SERVER["DOCUMENT_ROOT"] . '/local/config/events.php');

require_once(__DIR__ . '/include/events/page.php');
require_once(__DIR__ . '/include/events/feedback.php');
require_once(__DIR__ . '/include/events/review.php');






