<?
IncludeModuleLangFile(__FILE__);

if(!IsModuleInstalled("scrollup.bxd"))
{
    if(function_exists('__') || function_exists('__log'))
    {
        return false;
    }
}

Class scrollup_bxd extends CModule
{
	const MODULE_ID = 'scrollup.bxd';
	var $MODULE_ID = 'scrollup.bxd';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("scrollup.bxd_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("scrollup.bxd_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("scrollup.bxd_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("scrollup.bxd_PARTNER_URI");
	}

	function InstallEvents($arParams = array())
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'Cbxd', 'OnBuildGlobalMenu');
        RegisterModuleDependences('main', 'OnBeforeProlog', self::MODULE_ID, 'Cbxd', 'OnBeforeProlog');
        RegisterModuleDependences('main', 'OnProlog', self::MODULE_ID, 'Cbxd', 'OnProlog');
		return true;
	}

	function UnInstallEvents($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'Cbxd', 'OnBuildGlobalMenu');
		UnRegisterModuleDependences('main', 'OnBeforeProlog', self::MODULE_ID, 'Cbxd', 'OnBeforeProlog');
        UnRegisterModuleDependences('main', 'OnProlog', self::MODULE_ID, 'Cbxd', 'OnProlog');
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID."/", true, true
        );

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID."/", true, true
        );

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true
        );

		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}

        DeleteDirFilesEx("/bitrix/images/".$this->MODULE_ID."/");
        DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID."/");
        DeleteDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default"
        );

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallEvents();
		RegisterModule(self::MODULE_ID);

        COption::SetOptionString("scrollup.bxd", "SBXD_JQUERY", "true");
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallEvents();

        COption::RemoveOption($this->MODULE_ID, "SBXD_JQUERY");
        COption::RemoveOption($this->MODULE_ID, "SBXD_GROUPS");
	}
}
?>
