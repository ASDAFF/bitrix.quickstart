<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));

IncludeModuleLangFile(__FILE__);
if(class_exists("altasib.ping")) return;

Class altasib_ping extends CModule
{
	var $MODULE_ID = "altasib.ping";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	function altasib_ping()
	{
		$arModuleVersion = array();

		$this->MODULE_NAME = GetMessage("PING_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("PING_MODULE_DISCRIPTION");

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		} else {
			$this->MODULE_VERSION = '1.0';
			$this->MODULE_VERSION_DATE = '2012-01-27';
		}
		$this->PARTNER_NAME = "ALTASIB";
		$this->PARTNER_URI = "http://www.altasib.ru/";
	}

	function DBInstall()
	{
		global $DB;
		$res = $DB->Query("CREATE TABLE IF NOT EXISTS `altasib_table_ping` (
				COUNT int NOT NULL AUTO_INCREMENT,
				ID int(11) not null,
				SITE_ID varchar(255),
				DATE date,
				TIME time,
				NAME varchar(255),
				URL varchar(255),
				ERROR varchar(500),
				A boolean,
				PRIMARY KEY (COUNT)
			)
		");

		$res = $DB->Query("CREATE TABLE IF NOT EXISTS `altasib_ping_log` (
				COUNT int NOT NULL AUTO_INCREMENT,
				ID int(11) not null,
				SITE_ID varchar(255),
				DATE date,
				TIME time,
				NAME varchar(255),
				URL varchar(255),
				SEACH varchar(255),
				RESULT varchar(255),
				PRIMARY KEY (COUNT)
			)
		");
	}

	function DBUninstall()
	{
		global $DB;
		$res = $DB->Query("DROP TABLE IF EXISTS `altasib_table_ping`");
		$res = $DB->Query("DROP TABLE IF EXISTS `altasib_ping_log`");
	}

	function DoInstall()
	{
		if (IsModuleInstalled("altasib.ping")) {
			$this->DoUninstall();
			return;

		} else {
			global $DB, $APPLICATION, $step;
			$step = IntVal($step);
			RegisterModule("altasib.ping");
			$this->InstallFiles();
			RegisterModuleDependences("iblock","OnAfterIBlockElementAdd","altasib.ping","CAltasibping","onUpdatesEvent","100");
			RegisterModuleDependences("iblock","OnAfterIBlockElementUpdate","altasib.ping","CAltasibping","OnUpdateElement","100");
			RegisterModuleDependences("main", "OnPanelCreate", "altasib.ping", "CAltasibpingOther", "AddStatPingButtontoPannel", "100");
			$this->DBInstall();
			COption::SetOptionString("altasib.ping", "send_blog_ping_address", "http://ping.blogs.yandex.ru/RPC2\r\nhttp://rpc.weblogs.com/RPC2\r\nhttp://blogsearch.google.com/ping/RPC2");
			COption::SetOptionString("altasib.ping", "url_impotant_params","ID,IBLOCK_ID,SECTION_ID,ELEMENT_ID,PARENT_ELEMENT_ID,FID,TID,MID,UID,VOTE_ID,print,goto");


			$GLOBALS["errors"] = $this->errors;

			$APPLICATION->IncludeAdminFile(GetMessage("PING_INSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/install/step1.php");
		}
	}

	function DoUninstall()
	{
		global $DB, $APPLICATION, $step;
		$step = IntVal($step);
		UnRegisterModuleDependences("iblock","OnAfterIBlockElementAdd","altasib.ping","CAltasibping","onUpdatesEvent");
		UnRegisterModuleDependences("iblock","OnAfterIBlockElementUpdate","altasib.ping","CAltasibping","OnUpdateElement");
		UnRegisterModuleDependences("main", "OnPanelCreate", "altasib.ping", "CAltasibpingOther", "AddStatPingButtontoPannel");
		$this->UnInstallFiles();
		COption::RemoveOption("altasib.ping");
		UnRegisterModule("altasib.ping"); 
		$this->DBUninstall();
		$APPLICATION->IncludeAdminFile(GetMessage("PING_UNINSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/install/unstep1.php");
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/install/admin",$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images",true,true);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/images/altasib.ping");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/install/admin",$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/install/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		return true;
	}
}
?>