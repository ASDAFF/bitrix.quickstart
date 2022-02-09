<?
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/d7.php"))
{
	function getmicrotime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	require_once(dirname(__FILE__)."/../bx_root.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/lib/loader.php");

	/** @var $application \Bitrix\Main\HttpApplication */
	$application = \Bitrix\Main\HttpApplication::getInstance();

	$application->turnOnCompatibleMode();
	$application->setInputParameters(
		$_GET, $_POST, $_FILES, $_COOKIE, $_SERVER, $_ENV
	);

	$application->initialize();

	$page = new \Bitrix\Main\PublicPage();
	$application->setPage($page);

	$application->start();

	CMain::PrologActions();

	return;
}

define("START_EXEC_PROLOG_BEFORE_1", microtime());
$GLOBALS["BX_STATE"] = "PB";
if(isset($_REQUEST["BX_STATE"])) unset($_REQUEST["BX_STATE"]);
if(isset($_GET["BX_STATE"])) unset($_GET["BX_STATE"]);
if(isset($_POST["BX_STATE"])) unset($_POST["BX_STATE"]);
if(isset($_COOKIE["BX_STATE"])) unset($_COOKIE["BX_STATE"]);
if(isset($_FILES["BX_STATE"])) unset($_FILES["BX_STATE"]);

if(!isset($USER)) {global $USER;}
if(!isset($APPLICATION)) {global $APPLICATION;}
if(!isset($DB)) {global $DB;}

require_once(dirname(__FILE__)."/../include.php");

CMain::PrologActions();
?>