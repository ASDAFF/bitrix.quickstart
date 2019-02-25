<?
global $MESS;
IncludeModuleLangFile(str_replace("\\", "/", __FILE__));

if(class_exists('nsandrey_sitemap'))
	return;

class nsandrey_sitemap extends CModule
{
	var $MODULE_ID = 'nsandrey.sitemap';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	
	function nsandrey_sitemap()
	{
		$arModuleVersion = array();
		
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - 10);
        include($path.'/version.php');
        
        $this->MODULE_NAME = GetMessage('NAS_SITEMAP_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = GetMessage("NAS_SITEMAP_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage('NAS');
		$this->PARTNER_URI = GetMessage('NAS_URI'); 

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
	}

	function DoInstall()
	{
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/nsandrey'))
			mkdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/nsandrey');
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components/nsandrey', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/nsandrey', true, true);
		RegisterModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
		DeleteDirFilesEx('/bitrix/components/nsandrey/sitemap');
		UnRegisterModule($this->MODULE_ID);
	}
}
?>