<?php
/**
 * Bitrix vars
 *
 * @global CUser     $USER
 * @global CMain     $APPLICATION
 * @global CDatabase $DB
 */

use Bitrix\Main,
	 Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\Web,
	 Bitrix\Main\Localization\Loc;

if(ini_get('short_open_tag') == 0 && strtoupper(ini_get('short_open_tag')) != 'ON')
	die("Error: short_open_tag parameter must be turned in php.ini\n");

define('NO_KEEP_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
//define('BX_CRONTAB_SUPPORT', true);

if(!$_SERVER['DOCUMENT_ROOT'])
	$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../../../..');


//$ttfile = dirname(__FILE__) . '/1_txt.php';
//file_put_contents($ttfile, "<pre>" . print_r(date('d-m-Y H:i:s', time()), 1) . "</pre>\n", FILE_APPEND);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

//ID=1 lang=ru

//error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
//ini_set('memory_limit', '250M');


//Игнорирует отключение пользователя и позволяет скрипту быть запущенным постоянно
//Перебиваем возможные настройки лимитов в dbconn.php
@ignore_user_abort(true); // run script in background
@set_time_limit(0); // run script forever

/*$interval=60*15; // do every 15 minutes...
do{
	// add the script that has to be ran every 15 minutes here
	// ...
	sleep($interval); // wait 15 minutes
}while(true);*/

Loc::loadMessages(__FILE__);

while(@ob_end_flush())
	;

/*while(ob_get_level()) {
	ob_end_flush();
}*/
//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

$mgu = memory_get_usage(true);

if(!function_exists('getMemoryUsage')) {
	function getMemoryUsage($mgu = 0)
	{
		$memory = $mgu ? memory_get_usage(true) - $mgu : memory_get_usage(true);
		return \CFile::FormatSize($memory, 0);
	}
}

if(!Loader::includeModule('api.export'))
	die('api.export module error');

if(!Loader::includeModule('iblock'))
	die('iblock module error');

use Api\Export\ProfileTable;
use Api\Export\Log;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();


$pOrder = array('SORT' => 'ASC', 'ID' => 'ASC');
if($profileId = intval($request['ID'])) {
	$pFilter['=ID'] = $profileId;
}
else {
	$pFilter = array('=ACTIVE' => 'Y');
}

$rsProfile = ProfileTable::getList(array(
	 'order'  => $pOrder,
	 'filter' => $pFilter,
));

while($arFields = $rsProfile->fetch()) {
	ProfileTable::decodeFields($arFields);

	$tmpDir = Application::getDocumentRoot() . '/bitrix/tmp';
	if(!is_dir($tmpDir))
		if(!mkdir($tmpDir, 0755, true))
			die('Error! Can\'t make tmp folder');

	$lockFile = $tmpDir . '/api_export_' . $arFields['ID'] . '.lock';
	$lockFp   = fopen($lockFile, 'w');

	// Если блокировку получить не удалось, значит профиль еще выполняется
	if(!flock($lockFp, LOCK_EX | LOCK_NB)) {
		continue;
	}

	if($arFields['IBLOCK_ID']) {
		$start      = microtime(true);
		$strftime   = '%d.%m.%Y %H:%M:%S';
		$LAST_START = new \Bitrix\Main\Type\DateTime();
		$logName    = strftime('%Y-%m-%d_%H-%M-%S') . '__' . $arFields['ID'];

		Log::write(Loc::getMessage('AETC_PROFILE'), $arFields['ID'], $logName);
		Log::write(Loc::getMessage('AETC_PROCESS'), getmypid(), $logName);
		Log::write(Loc::getMessage('AETC_MEMORY'), ini_get('memory_limit'), $logName);
		Log::write(Loc::getMessage('AETC_EXPORT_LAST_START'), strftime($strftime), $logName);


		/** @var Main\Entity\Event $event */
		$event = new Main\Event('api.export', 'onBeforeExport', $arFields);
		$event->send();

		if($event->getResults()) {
			/** @var Main\EventResult $eventResult */
			foreach($event->getResults() as $eventResult) {
				if($eventResult->getType() == Main\EventResult::SUCCESS) {
					if($eventResultData = $eventResult->getParameters()) {
						$arFields = $eventResultData;
						unset($eventResultData);
					}
				}
			}
		}

		//Start write
		$export = new CApiYamarketExport($arFields);
		$export->writeHeader();

		$STEP           = 1;
		$ITEMS_COUNT    = 0;
		$ELEMENTS_COUNT = 0;
		$OFFERS_COUNT   = 0;
		$PROGRESS_COUNT = 0;

		do {
			$result = $export->writeOffers();

			Log::write(
				 Loc::getMessage('AETC_EXPORT_STEP', array('#STEP#' => $STEP, '#ITEMS#' => $result['LAST_ITEMS_COUNT'], '#MEMORY#' => getMemoryUsage())),
				 sprintf('%.2F', (microtime(true) - $start)),
				 $logName
			);

			$STEP++;
			$ITEMS_COUNT    += $result['LAST_ITEMS_COUNT'];
			$ELEMENTS_COUNT += $result['LAST_ELEMENTS_COUNT'];
			$OFFERS_COUNT   += $result['LAST_OFFERS_COUNT'];

			if($arFields['STEP_LIMIT'] == 0 || $result['LAST_ELEMENTS_COUNT'] < $arFields['STEP_LIMIT'])
				break;
		}
		while($result['LAST_ELEMENTS_COUNT'] > 0);


		//End write
		$export->writeFooter();
		$export->saveXML();

		unset($export, $result);


		$LAST_END    = new \Bitrix\Main\Type\DateTime();
		$end         = microtime(true);
		$lastRunTime = sprintf('%.2F', $end - $start);
		$totalMemory = getMemoryUsage($mgu);

		Log::write(Loc::getMessage('AETC_EXPORT_LAST_END'), strftime($strftime), $logName);
		Log::write(Loc::getMessage('AETC_EXPORT_TOTAL_ELEMENTS'), $ITEMS_COUNT, $logName);
		Log::write(Loc::getMessage('AETC_EXPORT_TOTAL_RUN_TIME'), $lastRunTime, $logName);
		Log::write(Loc::getMessage('AETC_EXPORT_TOTAL_MEMORY'), $totalMemory, $logName);

		$arUpdateFields = array(
			 'LAST_START'     => $LAST_START,
			 'LAST_END'       => $LAST_END,
			 'TOTAL_ITEMS'    => $ITEMS_COUNT,
			 'TOTAL_ELEMENTS' => $ELEMENTS_COUNT,
			 'TOTAL_OFFERS'   => $OFFERS_COUNT,
			 'TOTAL_RUN_TIME' => $lastRunTime,
			 'TOTAL_MEMORY'   => $totalMemory,
		);

		ProfileTable::update($arFields['ID'], $arUpdateFields);


		/** @var Main\Entity\Event $event */
		$event = new Main\Event('api.export', 'onAfterExport', array_merge($arFields, $arUpdateFields));
		$event->send();

		unset($start, $end, $strftime, $total_offers, $LAST_START, $LAST_END, $total_items, $total_offers, $lastRunTime, $totalMemory, $STEP, $mgu);
	}

	// По окончании работы необходимо снять блокировку и удалить файл
	register_shutdown_function(function () use ($lockFp, $lockFile) {
		flock($lockFp, LOCK_UN);
		@unlink($lockFile);
	});
}

unset($rsProfile, $arFields);



/*
$xhprof_path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/xhprof/xhprof-0.9.4/xhprof_lib/utils';
$xhprof_data = xhprof_disable();
include_once $xhprof_path . "/xhprof_lib.php";
include_once $xhprof_path . "/xhprof_runs.php";
$xhprof_runs = new XHProfRuns_Default();
$run_id      = $xhprof_runs->save_run($xhprof_data, "api_export");
*/

die();