<?php
/**
 * Скрипт обработки AJAX установки/снятия лайка для элемента
 * Принимает в параметре запроса ID ИД элемента ИБ
 * если текущий пользоавтель уже стави лайк - происходит отмена лайка
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
$ID = intval($request->get('ID'));
$arResult = ['RESULT' => 0];

//$APPLICATION->RestartBuffer();
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');


if ($ID > 0 && \Bitrix\Main\Loader::includeModule('vasoft.likeit')) {
	$arResult['RESULT'] = LikeTable::setLike($ID);
	$arResult['ID'] = $ID;
}
echo json_encode($arResult);
die();
