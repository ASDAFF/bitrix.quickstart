<? define ('START_SCRIPT_TIME', time());
//Проверку пути и крона
if ($_REQUEST['ajax'] === 'Y') {
	define('REQUEST_TYPE', 'ajax');
} else {
	if (!isset($_SERVER["DOCUMENT_ROOT"]) || empty($_SERVER["DOCUMENT_ROOT"])) {
		$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . '/../../../..');
		define('REQUEST_TYPE', 'cron');
		define("BX_CRONTAB", true);
	}
}

//Устанавливаем флаги на отключение статистики и проверки прав доступа
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_NO_ACCELERATOR_RESET', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

//Снимаем ограничение на время выполнения скрипта
set_time_limit(0);
ignore_user_abort(true);