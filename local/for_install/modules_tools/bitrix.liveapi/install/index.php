<?
IncludeModuleLangFile(__FILE__);
Class bitrix_liveapi extends CModule
{
	const MODULE_ID = 'bitrix.liveapi';
	var $MODULE_ID = 'bitrix.liveapi'; 
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
		$this->MODULE_NAME = GetMessage("bitrix.liveapi_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("bitrix.liveapi_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("bitrix.liveapi_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("bitrix.liveapi_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		global $DB;

		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CBitrixLiveapi', 'OnBuildGlobalMenu');
		RegisterModuleDependences('main', 'OnEpilog', self::MODULE_ID, 'CBitrixLiveapi', 'OnAdminPageLoad');


		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/updates/main'))
		{
			if (file_exists($f = dirname(__FILE__).'/db/install.sql'))
			{
				foreach($DB->ParseSQLBatch(file_get_contents($f)) as $sql)
					$DB->Query($sql);
			}
		}
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB;

		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CBitrixLiveapi', 'OnBuildGlobalMenu');
		UnRegisterModuleDependences('main', 'OnEpilog', self::MODULE_ID, 'CBitrixLiveapi', 'OnAdminPageLoad');

		if (file_exists($f = dirname(__FILE__).'/db/uninstall.sql'))
		{
			foreach($DB->ParseSQLBatch(file_get_contents($f)) as $sql)
				$DB->Query($sql);
		}
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
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/js', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/liveapi', $ReWrite = True, $Recursive = True);
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
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					if (is_dir($p0 = $p.'/'.$item))
					{
						$dir0 = opendir($p0);
						while (false !== $item0 = readdir($dir0))
						{
							if ($item0 == '..' || $item0 == '.')
								continue;
							DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
						}
						closedir($dir0);
					}
					else
						unlink($p);
				}
				closedir($dir);
			}
		}
		DeleteDirFilesEx('/bitrix/js/liveapi');
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
