<?
IncludeModuleLangFile(__FILE__);

class mlife_vkgalery extends CModule
{
	var $MODULE_ID = "mlife.vkgalery";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function mlife_vkgalery()
	{
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("MLIFE_COMPANY_NAME");
		$this->PARTNER_URI = "http://mlife-media.by/";
		$this->MODULE_NAME = GetMessage("MLIFE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MLIFE_MODULE_DESCRIPTION");
		return true;
	}

	function DoInstall()
	{
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.vkgalery/install/components",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/components/mlife", true, true);
		RegisterModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
		UnRegisterModule($this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/components/mlife/mlife.vkgalery");
	}
}
?>