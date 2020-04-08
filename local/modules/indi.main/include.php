<?php
/**
 * Individ module
 *
 * Include модуля индивид
 *
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main;

/**
 * Базовый каталог модуля
 */
const BASE_DIR = __DIR__;

/*
\Bitrix\Main\Loader::registerAutoLoadClasses(
	'inid.main',
	array(
		'indi\main\datatable' => 'lib/data.php',
	)
);
*/

$event = new \Bitrix\Main\Event('indi.main', 'onModuleInclude');
$event->send();