<?
IncludeModuleLangFile(__FILE__);

class inseco_slider extends CModule
{
	var $MODULE_ID = "inseco.slider";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function inseco_slider()
	{        		
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("INSECO_COMPANY_NAME");
		$this->PARTNER_URI = "http://inseco-ltd.com/";
		$this->MODULE_NAME = GetMessage("INSECO_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("INSECO_MODULE_DESCRIPTION");
		return true;
	}

	function DoInstall()
	{
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/inseco.slider/install/components",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/components/inseco", true, true);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/inseco.slider/install/js",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/inseco", true, true);
		RegisterModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
		UnRegisterModule($this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/components/inseco/inseco.slider");
	}
}
?>