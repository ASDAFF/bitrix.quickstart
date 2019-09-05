<?php
/**
 * @category	
 * @link		http://.ru
 * @revision	$Revision: 2062 $
 * @date		$Date: 2014-10-23 14:18:32 +0400 (Чт, 23 окт 2014) $
 */

setlocale(LC_ALL, 'ru_RU.cp1251'); 
setlocale(LC_NUMERIC, 'C'); 

@set_time_limit(0);
@ignore_user_abort(true);

define('AUX_NO_PERSISTENT', true);
define('BX_CRONTAB', true);
define('NO_AGENT_CHECK', true);

$pathParts = explode('/', dirname(__FILE__));
$bitrixRoot = implode('/', array_slice($pathParts, 0, -1));

if (!$_SERVER['DOCUMENT_ROOT']) {
	$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = implode('/', array_slice($pathParts, 0, -2));
	
	define('NO_KEEP_STATISTIC', true);
	define('NOT_CHECK_PERMISSIONS', true);
}

require $bitrixRoot . '/modules/main/include/prolog_before.php';