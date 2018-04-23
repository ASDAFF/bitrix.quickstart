<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$PXPL_MODULE_ID = 'primepix.propertylink';

CModule::IncludeModule($PXPL_MODULE_ID);
CModule::IncludeModule('iblock');


$action   = isset($_REQUEST['action']) ? $_REQUEST['action'] : FALSE;
$isAjax   = isset($_REQUEST['ajax']);
$propLink = new Primepix\CpxPropertylink;
$exepts   = new Primepix\CPxGetExepts;

$exeptions = $exepts->getIblockExp($_REQUEST['params']['iblock_id']);

switch($action) {

	case 'linkProperties':
		$result = $propLink->linkProperties($_REQUEST['params'], $exeptions);
		break;

	case 'clearProperties':
		$result = $propLink->clearProperties($_REQUEST['params']['iblock_id']);
		break;

	case 'getStat':
		$result = $propLink->getStat($_REQUEST['params']['iblock_id']);
		break;

	default:
		$result = $propLink->returnError('invalid action');
		break;
}

if ($isAjax) {
	echo json_encode($result);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');


