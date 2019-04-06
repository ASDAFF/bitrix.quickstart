<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../..');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
define('WDA_CRON', true);
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS',true); 
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
set_time_limit(0);
ignore_user_abort(true);
if (CModule::IncludeModule('asdaff.mass')) {
	CWDA::CronExec($argv);
}
?>