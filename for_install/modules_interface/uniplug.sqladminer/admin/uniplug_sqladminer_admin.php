<?require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global CDatabase $DB */

global $USER;
global $APPLICATION;

if ( !$USER->IsAdmin() ) {
	$APPLICATION->AuthForm("");
}

IncludeModuleLangFile(__FILE__);

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

function adminer_object() {
	include_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/uniplug.sqladminer/vendor/plugins/plugin.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/uniplug.sqladminer/vendor/plugins/bitrix.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/uniplug.sqladminer/vendor/plugins/dump-zip.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/uniplug.sqladminer/vendor/plugins/dump-bz2.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/uniplug.sqladminer/vendor/plugins/dump-date.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/uniplug.sqladminer/vendor/plugins/dump-json.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/uniplug.sqladminer/vendor/plugins/version-noverify.php";

	include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn.php');
	/** @var string $DBHost */
	/** @var string $DBLogin */
	/** @var string $DBName */
	/** @var string $DBPassword */

	global $APPLICATION;

	$plugins = array(
		new AdminerDumpZip(),
		new AdminerDumpBz2(),
		new AdminerDumpJson(),
		new AdminerDumpDate(),
		new AdminerVersionNoverify(),
		new AdminerBitrix($DBHost, $DBLogin, $DBName, $DBPassword, $APPLICATION->GetCurDir()),
	);

	return new AdminerPlugin($plugins);
}

include $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/uniplug.sqladminer/vendor/adminer-mysql.php";
