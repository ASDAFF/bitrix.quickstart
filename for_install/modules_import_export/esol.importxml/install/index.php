<?php

global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');

if (class_exists('esol_importxml')) return;

class esol_importxml extends CModule {

	var $MODULE_ID = 'esol.importxml';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';

	public function __construct() {

		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage("ESOL_PARTNER_NAME");
		$this->PARTNER_URI = 'http://esolutions.su/';

		$this->MODULE_NAME = GetMessage('ESOL_IMPORTXML_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ESOL_IMPORTXML_MODULE_DESCRIPTION');
	}

	public function DoInstall() {
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/panel/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/panel/', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/', true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes/', $_SERVER["DOCUMENT_ROOT"].'/bitrix/themes/', true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/php_interface/', $_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/', true, true);
		
		$this->InstallDB();
	}
	
	function InstallDB()
	{
		COption::SetOptionString($this->MODULE_ID, "GROUP_DEFAULT_RIGHT", "W");
		RegisterModule($this->MODULE_ID);
		return true;
	}

	public function DoUninstall() {
		DeleteDirFilesEx('/bitrix/js/'.$this->MODULE_ID.'/');
		DeleteDirFilesEx('/bitrix/panel/'.$this->MODULE_ID.'/');
		DeleteDirFilesEx('/bitrix/php_interface/include/'.$this->MODULE_ID.'/');
		DeleteDirFilesEx('/bitrix/themes/.default/icons/'.$this->MODULE_ID.'/');
		
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin/', $_SERVER["DOCUMENT_ROOT"].'/bitrix/admin/');
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes/.default/', $_SERVER["DOCUMENT_ROOT"].'/bitrix/themes/.default/');
		
		$this->UnInstallDB();
	}
	
	function UnInstallDB()
	{
		UnRegisterModule($this->MODULE_ID);
		return true;
	}
}
?>