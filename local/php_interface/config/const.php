<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 26.02.2018
 * Time: 20:55
 */

define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/local/logs/AddMessage2Log.txt");

/**
 * IBLOCK IDs
 */
define('IBLOCK_ID__CATALOG', IBlockData::getByCode('CATALOG'));// Чтобы решить проблему с разными ID инфоблоков на разных сайтах. При старте страницы получаем инфоблок по его символьному коду и вкладываем в константу.
//define("IBLOCK_ID__CATALOG", 4);
define("IBLOCK_ID__OFFERS", 11);
define("IBLOCK_ID__VIDEO", 9);
//...
//остальные используемые инфоблоки
//...
/**
 * PATHS
 */
define('SITE_PATH_LOGO', '/local/assets/frontend/images/logo');                 //путь до логотипов сайта
define('PATH_CSS', '/local/assets/frontend/css');                               //путь до файлов со стилями
define('PATH_JS', '/local/assets/frontend/js');                                 //путь до файлов со js скриптами
define('PATH_GLOBAL_CSS', '/local/assets/frontend/css/global.css');             //путь до файла со глобальными стилями
define('PATH_GLOBAL_JS', '/local/assets/frontend/js/global.js');                //путь до файлов со js скриптами
define('PATH_RESPONSIVE_CSS', '/local/assets/frontend/css/responsive.css');
define('PATH_AJAX', '/local/ajax/');                                            //путь до директории ajax
define('PATH_AJAX_JS', '/local/ajax/ajax.js');                                  //путь до директории ajax
define('PATH_BOWER_COMPONENTS', '/local/assets/bower_components');              //путь до файлов библиотек bower
define('PATH_LIBRARY', '/local/assets/lib');                                    //путь до файлов библиотек
define('PATH_INCLUDE', '/include/');