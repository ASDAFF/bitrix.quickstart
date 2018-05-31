<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 16.05.2018
 * Time: 21:14
 */
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
define('PATH_TO__BASKET', '/personal/order/make/');
define('PATH_TO__ORDER', '/personal/order/make/');
