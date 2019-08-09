<?
@set_time_limit(0);
if(!defined('NOT_CHECK_PERMISSIONS')) define('NOT_CHECK_PERMISSIONS', true);
if(!defined('BX_CRONTAB')) define("BX_CRONTAB", true);
if(!defined('ADMIN_SECTION')) define("ADMIN_SECTION", true);
if(!ini_get('date.timezone') && function_exists('date_default_timezone_set')){@date_default_timezone_set("Europe/Moscow");}
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/../../../..');
if(!array_key_exists('REQUEST_URI', $_SERVER)) $_SERVER["REQUEST_URI"] = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
@set_time_limit(0);
$moduleId = 'kda.exportexcel';
$moduleRunnerClass = 'CKDAExportExcelRunner';
\Bitrix\Main\Loader::includeModule("iblock");
\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule("currency");
\Bitrix\Main\Loader::includeModule($moduleId);
$PROFILE_ID = $argv[1];

/*Remove old dirs*/
CKDAExportUtils::RemoveTmpFiles(0);
/*/Remove old dirs*/

$arProfiles = array_map('trim', explode(',', $PROFILE_ID));
foreach($arProfiles as $PROFILE_ID)
{
	if(strlen($PROFILE_ID)==0)
	{
		echo date('Y-m-d H:i:s').": profile id is not set\r\n";
		continue;
	}
	
	$oProfile = new CKDAExportProfile();
	$arProfileFields = $oProfile->GetFieldsByID($PROFILE_ID);
	if($arProfileFields['ACTIVE']=='N')
	{
		echo date('Y-m-d H:i:s').": profile is not active\r\n"."Profile id = ".$PROFILE_ID."\r\n\r\n";
		continue;
	}
	
	$arParams = $oProfile->GetProccessParamsFromPidFile($PROFILE_ID);
	if($arParams===false)
	{
		echo date('Y-m-d H:i:s').": export in process\r\n"."Profile id = ".$PROFILE_ID."\r\n\r\n";
		continue;
	}

	$SETTINGS_DEFAULT = $SETTINGS = $EXTRASETTINGS = null;
	$oProfile = new CKDAExportProfile();
	$oProfile->Apply($SETTINGS_DEFAULT, $SETTINGS, $PROFILE_ID);
	$oProfile->ApplyExtra($EXTRASETTINGS, $PROFILE_ID);
	$params = array_merge($SETTINGS_DEFAULT, $SETTINGS);
	$params['MAX_EXECUTION_TIME'] = 0;

	$arParams = array();
	$arResult = $moduleRunnerClass::ExportIblock($params, $EXTRASETTINGS, array(), $PROFILE_ID);

	echo date('Y-m-d H:i:s').": export complete\r\n"."Profile id = ".$PROFILE_ID."\r\n".CUtil::PhpToJSObject($arResult)."\r\n\r\n";
}
?>