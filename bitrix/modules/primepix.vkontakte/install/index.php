<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class primepix_vkontakte extends CModule
{
	var $MODULE_ID = "primepix.vkontakte";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function primepix_vkontakte()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("PARTNER_URI");
		$this->MODULE_NAME = GetMessage("MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
	}

	function InstallFiles()
	{
		global $DOCUMENT_ROOT;
		CopyDirFiles($DOCUMENT_ROOT."/bitrix/modules/primepix.vkontakte/install/components", 
			$DOCUMENT_ROOT."/bitrix/components", True, True);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/primepix/vkontakte.like/");
		DeleteDirFilesEx("/bitrix/components/primepix/vkontakte.group/");
		DeleteDirFilesEx("/bitrix/components/primepix/vkontakte.poll/");
		DeleteDirFilesEx("/bitrix/components/primepix/vkontakte.comments/");
		DeleteDirFilesEx("/bitrix/components/primepix/vkontakte.recommended/");
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallFiles();
		RegisterModule("primepix.vkontakte");
		$APPLICATION->IncludeAdminFile(GetMessage("INSTALL_TITLE"), 
			$DOCUMENT_ROOT."/bitrix/modules/primepix.vkontakte/install/step.php");
	}
	
	function DoUnInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallFiles();
		UnRegisterModule("primepix.vkontakte");
		$APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), 
			$DOCUMENT_ROOT."/bitrix/modules/primepix.vkontakte/install/unstep.php");
	}
}
?>