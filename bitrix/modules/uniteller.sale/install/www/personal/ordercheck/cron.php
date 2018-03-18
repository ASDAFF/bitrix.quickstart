<?php
/**
 * Вызывается из cron.bat. Ставит константу и выполняет пролог, для того, чтобы отработал агент UnitellerAgent();.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
ignore_user_abort(true);
set_time_limit(0);

// Сообщает агенту, что его вызывает крон.
define('UNITELLER_AGENT', true);

// Все подключения модулей в 1C-Bitrix производятся относительно $_SERVER['DOCUMENT_ROOT'].
chdir(dirname(__FILE__) . '../../../');
$_SERVER['DOCUMENT_ROOT'] = getcwd();

// Агент вызывается в прологе.
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');