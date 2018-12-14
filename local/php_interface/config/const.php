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
define('SITE_PATH_LOGO', '/local/codenails/frontend/images/logo/');             //путь до логотипов сайта
define('PATH_TEMPLATE_CSS', '/local/codenails/frontend/css/');                  //путь до файлов со стилями
define('PATH_TEMPLATE_JS', '/local/codenails/frontend/js/');                    //путь до файлов со js скриптами
define('SITE_PATH_LIB', '/local/codenails/frontend/lib/');                      //путь до файлов библиотек
define('PATH_INCLUDE', '/include/');                                            //путь до включаемых областей
define('PATH_AJAX', '/local/codenails/ajax/');                                  //путь до директории ajax
