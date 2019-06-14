<?php
/**
 * Скрипт обработки AJAX запроса количества лайков по списку элементов
 * Принимает в параметре запроса IDS массив ИД элементов ИБ
 *
 * module: vasoft.likeit
 */
use Bitrix\Main\Application;
use Vasoft\Likeit\LikeTable;

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$arIDs = $request->get('IDS');
$arResult = ['RESULT' => 0, 'ITEMS' => []];

$APPLICATION->RestartBuffer();
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

if (\Bitrix\Main\Loader::includeModule('vasoft.likeit')) {
	$arResult['ITEMS'] = LikeTable::getStatList($arIDs);
	$arResult['RESULT'] = 1;
}
echo json_encode($arResult);
die();
