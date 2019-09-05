<?php
/**
 *  module
 * 
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */


namespace Site\Main;

/**
 * Базовый каталог модуля
 */
const BASE_DIR = __DIR__;

/*
\Bitrix\Main\Loader::registerAutoLoadClasses(
	'inid.main',
	array(
		'Site\main\datatable' => 'lib/data.php',
	)
);
*/

$event = new \Bitrix\Main\Event('site.main', 'onModuleInclude');
$event->send();