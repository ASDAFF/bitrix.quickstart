<?
#################################################
#   Company developer: Zebrus Ltd.              #
#   Developer: Sergey                           #
#   Site: http://www.zebrus.ru                  #
#   E-mail: support@zebrus.ru                   #
#   Copyright (c) 2005-2012 Zebrus Ltd.         #
#################################################
?>
<?
global $MESS;
IncludeModuleLangFile(__FILE__);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/zebrus.resize/prolog.php');

Class zebrus_resize extends CModule
{
	const MODULE_ID = 'zebrus.resize';
	var $MODULE_ID = 'zebrus.resize'; 
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
		$this->MODULE_NAME = GetMessage("ZEBRUS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ZEBRUS_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("ZEBRUS_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("ZEBRUS_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		COption::SetOptionString($this->MODULE_ID, 'NS_SITE_ID', NS_SITE_ID_ISNALL);
		CUrlRewriter::Add(
			array(
				"SITE_ID" => NS_SITE_ID_ISNALL,
				"CONDITION" => URLREWRITER_CONDITION,
				"ID" => '',
				"PATH" => URLREWRITER_FILE_PATH,
				"RULE" => 'w=$1&wr=$2&h=$3&hr=$4&q=$5&file=$6'
			)
		);
		//RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CZebrusResize', 'OnBuildGlobalMenu');
		
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		define('NS_SITE_ID', COption::GetOptionString($this->MODULE_ID, 'NS_SITE_ID', 's1'));
		$CONDITION = '#^/resize/([0-9]+)x([0-9]+)x([0-9]+)x([0-9]+)x([0-9]+)/(.*)#';
		CUrlRewriter::Delete(array("SITE_ID" => NS_SITE_ID, "CONDITION" => URLREWRITER_CONDITION));
		COption::RemoveOption($this->MODULE_ID);
		//UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CZebrusResize', 'OnBuildGlobalMenu');
		
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
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/tools'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/zebrus_resize.php',
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/tools/resize.php");?'.'>'); 
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache/zebrus_resize/info.txt',
					'This directory to store files cache module');

				/*	file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/resize.php',
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/tools/index.php");?'.'>'); */
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
 			
	
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/tools'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/zebrus_resize.php');
					//unlink($_SERVER['DOCUMENT_ROOT'].'/resize.php');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
$APPLICATION->IncludeAdminFile(GetMessage("ZEBRUS_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zebrus.resize/install/step1.php");
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
$APPLICATION->IncludeAdminFile(GetMessage("ZEBRUS_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zebrus.resize/install/unstep1.php");
	}
}
?>
