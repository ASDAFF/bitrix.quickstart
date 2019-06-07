<?
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;
CUtil::JSPostUnescape();

$moduleId = 'api.orderstatus';

$arResult  = array();
if(!check_bitrix_sessid())
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TD_SESSION_EXPIRED'),
	);
}

if(!Loader::includeModule($moduleId))
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TD_API_MODULE_ERROR'),
	);
}

if(!Loader::includeModule('sale'))
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TD_SALE_MODULE_ERROR'),
	);
}

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D')
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TD_ACCESS_DENIED'),
	);
}


/////////////////////////////////////////////////////////////////
//                           EXEC
/////////////////////////////////////////////////////////////////
if(empty($arResult))
{
	$arResult = array(
		'result'  => 'ok',
		'message'  => '',
		'defaults' => Loc::getMessage('AOS_TD_INSTALL_DEFAULTS'),
	);
}

$APPLICATION->RestartBuffer();
echo Bitrix\Main\Web\Json::encode($arResult);
die();
?>