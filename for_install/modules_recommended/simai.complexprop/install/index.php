<?php
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - 18);
@include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
IncludeModuleLangFile($strPath2Lang."/install/index.php");

class simai_complexprop extends CModule
{
    const MODULE_ID = 'simai.complexprop';
    var $MODULE_ID = 'simai.complexprop';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'N';
    var $PARTNER_NAME;
    var $PARTNER_URI;
    
    function simai_complexprop()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SMCP_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SMCP_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = "SIMAI"; 
        $this->PARTNER_URI = "http://www.simai.ru";
	}
    
    public function DoInstall()
    {
        if(!IsModuleInstalled("simai.complexprop"))
        {
            RegisterModule("simai.complexprop");
			
			RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", "simai.complexprop", "CCustomTypeSimaiComplex", "GetUserTypeDescription");			
			RegisterModuleDependences("main", "OnBeforeProlog", "simai.complexprop", "CIBEditSimaiComplexProp", "OnBeforePrologHandler");
			RegisterModuleDependences("iblock", "OnStartIBlockElementAdd", "simai.complexprop", "CIBEditSimaiComplexProp", "OnStartIBlockElementUpdateHandler");
			RegisterModuleDependences("iblock", "OnStartIBlockElementUpdate", "simai.complexprop", "CIBEditSimaiComplexProp", "OnStartIBlockElementUpdateHandler");
			RegisterModuleDependences("iblock", "OnBeforeIBlockElementAdd", "simai.complexprop", "CIBEditSimaiComplexProp", "OnBeforeIBlockElementUpdateHandler");
			RegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", "simai.complexprop", "CIBEditSimaiComplexProp", "OnBeforeIBlockElementUpdateHandler");
			RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "simai.complexprop", "CIBEditSimaiComplexProp", "OnAfterIBlockElementUpdateHandler");
			RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "simai.complexprop", "CIBEditSimaiComplexProp", "OnAfterIBlockElementUpdateHandler");
        }
        return true;
    }    
    
	function DoUninstall()
	{
		global $APPLICATION,$DB;
		
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", "simai.complexprop", "CCustomTypeSimaiComplex", "GetUserTypeDescription");		
		UnRegisterModuleDependences("main", "OnBeforeProlog", "simai.complexprop", "CIBEditSimaiComplexProp", "OnBeforePrologHandler");
		UnRegisterModuleDependences("iblock", "OnStartIBlockElementAdd", "simai.complexprop", "CIBEditSimaiComplexProp", "OnStartIBlockElementUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnStartIBlockElementUpdate", "simai.complexprop", "CIBEditSimaiComplexProp", "OnStartIBlockElementUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementAdd", "simai.complexprop", "CIBEditSimaiComplexProp", "OnBeforeIBlockElementUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", "simai.complexprop", "CIBEditSimaiComplexProp", "OnBeforeIBlockElementUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "simai.complexprop", "CIBEditSimaiComplexProp", "OnAfterIBlockElementUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "simai.complexprop", "CIBEditSimaiComplexProp", "OnAfterIBlockElementUpdateHandler");
		
		UnRegisterModule("simai.complexprop");
		
		return true;			
	}
    
    public function InstallDB()
	{
		return true;
	}
    
    public function UninstallDB()
	{
		return true;
	}
    
    public function InstallFiles()
	{
		return true;
	}
    
    public function UninstallFiles()
	{
		return true;
	}
}
?>
