<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

if(class_exists("prmedia_vkontaktecomments")) return;

Class prmedia_vkontaktecomments extends CModule
{
	var $MODULE_ID = "prmedia.vkontaktecomments";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	
	function prmedia_vkontaktecomments()
	{
        $this->MODULE_NAME = GetMessage("PRMEDIA_INSTALL_NAME"); 
        $this->MODULE_DESCRIPTION = GetMessage("PRMEDIA_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = "Progressive Media";
		$this->PARTNER_URI = "http://www.progressivemedia.ru";

		$arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
	}

	function DoInstall()
	{
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/prmedia')) mkdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/prmedia');
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/components/prmedia', $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/prmedia', true, true);
		RegisterModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
		DeleteDirFilesEx('/bitrix/components/prmedia/vkontakte.comments');
		UnRegisterModule($this->MODULE_ID);
	}
}
?>