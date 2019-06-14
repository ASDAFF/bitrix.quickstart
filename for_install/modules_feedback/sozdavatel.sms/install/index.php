<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class sozdavatel_sms extends CModule
{
	var $MODULE_ID = "sozdavatel.sms";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function sozdavatel_sms()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = COMPRESSION_VERSION;
			$this->MODULE_VERSION_DATE = COMPRESSION_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("SMS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SMS_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
		$this->PARTNER_URI = "http://sozdavatel.ru";
	}

	function InstallDB($arParams = array())
	{
		RegisterModule("sozdavatel.sms");
		//RegisterModuleDependences("main", "OnPageStart", "compression", "CCompress", "OnPageStart", 1);

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		//UnRegisterModuleDependences("main", "OnPageStart", "compression", "CCompress", "OnPageStart");
		UnRegisterModule("sozdavatel.sms");

		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sozdavatel.sms/install/admin', $_SERVER['DOCUMENT_ROOT']."/bitrix/admin", true, true);
		//CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sozdavatel.sms/install/components', $_SERVER['DOCUMENT_ROOT']."/bitrix/components", true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sozdavatel.sms/install/images', $_SERVER['DOCUMENT_ROOT']."/bitrix/images", true, true);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sozdavatel.sms/install/admin', $_SERVER['DOCUMENT_ROOT']."/bitrix/admin");
		//DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sozdavatel.sms/install/components', $_SERVER['DOCUMENT_ROOT']."/bitrix/components/sozdavatel");
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sozdavatel.sms/install/images', $_SERVER['DOCUMENT_ROOT']."/bitrix/images/sozdavatel.sms");
		return true;
	}

	function DoInstall($update=false)
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		
		if (!$update)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("SMS_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/sozdavatel.sms/install/step.php");
		}	
	}

	function DoUninstall($update=false)
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallDB();
		$this->UnInstallFiles();
		if (!$update)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("COMPRESS_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/sozdavatel.sms/install/unstep.php");
		}
	}
}
?>