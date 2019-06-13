<?php
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
//define('BX_PUBLIC_TOOLS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$bUTF = (defined('BX_UTF') && BX_UTF === true);
$iIncludeResult = CModule::IncludeModuleEx('karudo.vcs');
IncludeModuleLangFile(__FILE__);

function vcs_win2utf(&$str) {
	$str = CVCSMain::GetAPPLICATION()->ConvertCharset($str, 'CP1251', 'UTF-8');
}
function vcs_utf2win(&$str) {
	$str = CVCSMain::GetAPPLICATION()->ConvertCharset($str, 'UTF-8', 'CP1251');
}

$DEBUG = false && CVCSConfig::KarudoDevelMode();

$arParams = $_REQUEST;
if (!$bUTF) {
	array_walk_recursive($arParams, 'vcs_utf2win');
}
$arResult = array('status' => 'ok');

try {
	if (!check_bitrix_sessid()) {
		throw new CVCSAjaxExceptionAuthError(GetMessage("VCS_ERROR_INV_SESSION"));
	}
	if ($iIncludeResult == MODULE_DEMO_EXPIRED) {
		throw new CVCSAjaxExceptionSystemError(GetMessage("VCS_ERROR_EXPIRED"));
	}
	if (!CVCSMain::GetUSER()->IsAdmin()) {
		throw new CVCSAjaxExceptionAuthError(GetMessage("VCS_ERROR_NO_RIGHTS"));
	}

	$class = empty($arParams['service']) ? '' : (string) $arParams['service'];
	$method = empty($arParams['cmd']) ? '' : (string) $arParams['cmd'];

	if (empty($arParams['request']) || !is_array($arParams['request'])) {
		$arRequset = array();
	} else {
		$arRequset = $arParams['request'];
	}

	if (!empty($arParams['add_params']) && is_array($arParams['add_params'])) {
		$arRequset = array_merge($arRequset, $arParams['add_params']);
	}

	$class = 'CVCSAjax' . ucfirst($class);
	$method = 'cmd' . ucfirst($method);

	if (!class_exists($class) || !is_subclass_of($class, 'CVCSAjaxService')) {
		throw new CVCSAjaxExceptionSystemError(GetMessage('VCS_ERROR_NO_CLASS'));
	}
	$Obj = new $class;
	//$arResult['qwe'] = "$class, $method";
	$callback = array($Obj, $method);

	if (!is_callable($callback)) {
		throw new CVCSAjaxExceptionSystemError(GetMessage('VCS_ERROR_NO_METHOD'));
	}

	if ($DEBUG) CVCSMain::GetDB()->DebugToFile = true;
	$arResult['response'] = call_user_func($callback, $arRequset);
	if ($DEBUG) CVCSMain::GetDB()->DebugToFile = false;


} catch (CVCSAjaxException $e) {
	$arResult['status'] = 'error';
	$arResult['error'] = array(
		'text' => $e->getMessage(),
		'type' => $e->GetType(),
	);
}

if (!$bUTF) {
	array_walk_recursive($arResult, 'vcs_win2utf');
}
header("Content-Type: application/json; charset=utf-8");
echo json_encode($arResult);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
