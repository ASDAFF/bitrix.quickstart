<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class ithive_oxml extends CModule
{
	var $MODULE_ID = 'ithive.oxml';																							
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $COMPONENT_NAME = 'ithive.oxml';
	
	function ithive_oxml()
	{        		
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		
		include($path."/version.php");
		
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->MODULE_NAME = GetMessage("ITHIVE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ITHIVE_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("ITHIVE_PARTNER_NAME");
		$this->PARTNER_URI = "http://www.it-hive.ru/";
	}
	
	
	function DoInstall() 
	{		
		global $APPLICATION, $step;
		$step = IntVal($step);
		
		if ($step < 2) 
		{
			$APPLICATION->IncludeAdminFile(GetMessage("ITHIVE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->COMPONENT_NAME."/install/step1.php");
		}
		else if($step == 2) 
		{
		
			$GLOBALS["errors"] = $this->errors;
			
			$APPLICATION->IncludeAdminFile(GetMessage("ITHIVE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->COMPONENT_NAME."/install/step2.php");
			
		}
		else
		{
			$arOptions = unserialize(COption::GetOptionString('ithive.oxml', 'options'));
			
			$this->InstallOxmlFile($arOptions['site']['dir_to_install'], $arOptions['site']['random_name']);
			$this->InstallDirFiles();
			RegisterModule($this->MODULE_ID);
			$APPLICATION->IncludeAdminFile(GetMessage("ITHIVE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->COMPONENT_NAME."/install/step3.php");
			
				
		}
	}

	function DoUninstall(){       
		UnRegisterModule($this->MODULE_ID);
		$this->UnInstallDirFiles();
	}
	
	function InstallOxmlFile($dir = '/', $name = false) {
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->COMPONENT_NAME."/install/public_html/openboomapp.php", $_SERVER["DOCUMENT_ROOT"]."$dir/$name", true, true); // Install oxml/openboomapp.oxml into root directory
		
	}
	
	function InstallDirFiles() {	
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->COMPONENT_NAME."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/ithive/", true, true);
		
		return true;
	}
	
	function UnInstallDirFiles() {
		COption::RemoveOption('ithive.oxml', 'options');
		DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/ithive/oxml");
		
		return true;
	}
	
}
?>
