<?
IncludeModuleLangFile(__FILE__);
Class akazakov_reindex extends CModule
{
	const MODULE_ID = 'akazakov.reindex';
	var $MODULE_ID = 'akazakov.reindex'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_INSTDIR;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("akazakov.reindex_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("akazakov.reindex_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("akazakov.reindex_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("akazakov.reindex_PARTNER_URI");
		//if (SM_VERSION >= '12.0') {
		//	$this->MODULE_INSTDIR = 'panel';
		//} else {
			$this->MODULE_INSTDIR = 'themes';
		//}
	}

	function InstallDB($arParams = array())
	{
		RegisterModuleDependences("main", "OnPageStart", self::MODULE_ID, "CAkazakovReindex", "ReindexOnPageStartHandler");
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CAkazakovReindex', 'OnBuildGlobalMenu',"500");
		
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences("main", "OnPageStart", self::MODULE_ID, "CAkazakovReindex", "ReindexOnPageStartHandler");
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CAkazakovReindex', 'OnBuildGlobalMenu');
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

	function InstallFiles()
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
		
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/'.$this->MODULE_INSTDIR))
		{
			CopyDirFiles($p,$_SERVER['DOCUMENT_ROOT'].'/bitrix/'.$this->MODULE_INSTDIR,true,true);
		}
		
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

		//DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"].'/bitrix/panel/akazakov.reindex');
		DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"].'/bitrix/themes/.default/akazakov.reindex.css');
		DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"].'/bitrix/themes/.default/icons/reindex');

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		
		
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		CAgent::AddAgent(
            "CSearch::ReIndexAll(true, 2);", // имя функции
            "search",                          // идентификатор модуля
            "N",                                  // агент не критичен к кол-ву запусков
            '',                                // интервал запуска - 1 сутки
            date("d.m.Y H:i:s"),                // дата первой проверки на запуск
            "N",                                  // агент активен
            date("d.m.Y H:i:s"),                // дата первого запуска
            30);
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
