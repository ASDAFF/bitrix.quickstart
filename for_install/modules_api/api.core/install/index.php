<?
use Bitrix\Main\ModuleManager,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class api_core extends CModule
{
	var $MODULE_ID           = 'api.core';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION      = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME         = GetMessage("api.core_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.core_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.core_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.core_PARTNER_URI");
	}

	function InstallFiles()
	{
		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/css", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/css/" . $this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/" . $this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/images", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images/". $this->MODULE_ID, true, true);

		return true;
	}

	function UnInstallFiles()
	{
		//DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');
		DeleteDirFilesEx('/bitrix/components/api/bb.editor/');
		DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/');
		DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID . '/');
		DeleteDirFilesEx('/bitrix/images/' . $this->MODULE_ID . '/');

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->InstallFiles();

		ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->UnInstallFiles();

		ModuleManager::unRegisterModule($this->MODULE_ID);

		return true;
	}
}

?>