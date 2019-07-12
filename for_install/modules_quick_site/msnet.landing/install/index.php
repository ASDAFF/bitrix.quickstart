<?
global $MESS;
IncludeModuleLangFile(str_replace("\\", "/", __FILE__));

if(class_exists('msnet_landing'))
	return;

class msnet_landing extends CModule
{
	var $MODULE_ID = 'msnet.landing';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	
	function __construct()
	{
		$arModuleVersion = array();
		
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - 10);
        include($path.'/version.php');
        
        $this->MODULE_NAME = GetMessage('MODULE_NAME'); 
        $this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage('PARTNER_NAME');
		$this->PARTNER_URI = GetMessage('PARTNER_URI'); 

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
	}

	function DoInstall()
	{
		$this->InstallFiles();
		RegisterModule($this->MODULE_ID);
	}
	
	function InstallEvents()
	{
		return true;
	}
	
	function InstallFiles()
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/wizards/msnet/landing', $_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards/msnet/landing', true, true);

		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
		$wizard = new CWizard("msnet:landing");
		$wizard->Install();

		return true;
	}
	
	function UnInstallEvents()
	{
		return true;
	}
	
 	function InstallDB()
    {
        return true;
    }
    
    function InstallPublic()
	{
		return true;
	}
	
	function UnInstallDB()
	{
	}
	
	function UnInstallFiles()
	{
		return true;
	}

	function DoUninstall()
	{
		DeleteDirFilesEx('/bitrix/wizards/msnet/landing');
		UnRegisterModule($this->MODULE_ID);
	}
}
?>