<?
global $MESS;

$langPath = str_replace("\\", "/", __FILE__);
$langPath = substr($langPath, 0, strlen($langPath) - strlen("/install/index.php"));
include(GetLangFileName($langPath . "/lang/", "/install/index.php"));

Class newkaliningrad_typography extends CModule
{
	var $MODULE_ID = "newkaliningrad.typography";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function newkaliningrad_typography() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
	   
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = getMessage('NK_MODULE_NAME');
		$this->MODULE_DESCRIPTION = getMessage('NK_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = "newkaliningrad";
		$this->PARTNER_URI = "http://newkaliningrad.ru";
	}

	function InstallDB($install_wizard = true) {
		RegisterModule($this->MODULE_ID);
		return true;
	}

	function UnInstallDB($arParams = Array()) {
		UnRegisterModule($this->MODULE_ID);
		return true;
	}

	function InstallFiles() {
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/newkaliningrad.typography/install/tools/newkaliningrad_typography/newkaliningrad_typography.php", 
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/newkaliningrad_typography/newkaliningrad_typography.php", true);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/newkaliningrad.typography/install/admin/htmleditor2/newkaliningrad_typography.js", 
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/htmleditor2/newkaliningrad_typography.js", true);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/newkaliningrad.typography/install/images/fileman/htmledit2/newkaliningrad_typography.gif", 
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/images/fileman/htmledit2/newkaliningrad_typography.gif", true);

		return true;
	}
	
	function UnInstallFiles() {
		DeleteDirFilesEx("/bitrix/tools/newkaliningrad_typography/");
		DeleteDirFilesEx("/bitrix/admin/htmleditor2/newkaliningrad_typography.js");
		DeleteDirFilesEx("/bitrix/images/fileman/htmledit2/newkaliningrad_typography.gif");

		return true;
	}


	function DoInstall() {
		RegisterModuleDependences("fileman", "OnBeforeHTMLEditorScriptsGet", $this->MODULE_ID, "newkaliningrad_typography", "addEditorScriptsHandler" );
		RegisterModuleDependences("fileman", "OnIncludeHTMLEditorScript", $this->MODULE_ID, "newkaliningrad_typography", "OnIncludeHTMLEditorHandler" );
		
		RegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", $this->MODULE_ID, "newkaliningrad_typography", "OnBeforeIBlockElementAddOrUpdateHandler");
		RegisterModuleDependences("iblock", "OnBeforeIBlockElementAdd", $this->MODULE_ID, "newkaliningrad_typography", "OnBeforeIBlockElementAddOrUpdateHandler");		 

		$this->InstallDB(false);
		$this->InstallFiles();
	}

	function DoUninstall() {
		UnRegisterModuleDependences("fileman", "OnBeforeHTMLEditorScriptsGet", $this->MODULE_ID, "newkaliningrad_typography", "addEditorScriptsHandler" );
		UnRegisterModuleDependences("fileman", "OnIncludeHTMLEditorScript", $this->MODULE_ID, "newkaliningrad_typography", "OnIncludeHTMLEditorHandler" );
		
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", $this->MODULE_ID, "newkaliningrad_typography", "OnBeforeIBlockElementAddOrUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementAdd", $this->MODULE_ID, "newkaliningrad_typography", "OnBeforeIBlockElementAddOrUpdateHandler");
		
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}