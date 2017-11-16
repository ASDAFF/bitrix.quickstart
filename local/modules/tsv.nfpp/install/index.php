<?
IncludeModuleLangFile(__FILE__);
Class tsv_nfpp extends CModule
{
	const MODULE_ID = 'tsv.nfpp';
	var $MODULE_ID = 'tsv.nfpp'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $MODULE_GROUP_RIGHTS = "Y";

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("TSV_NFPP_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("TSV_NFPP_MODULE_DESCRIPTION");

		$this->PARTNER_NAME = GetMessage("TSV_NFPP_PARTNER_NAME");
		$this->PARTNER_URI = 'http://tsv.rivne.me';
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType;
		//$DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/install/db/'.strtolower($DBType).'/install.sql');
				
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType;
		if ($_REQUEST['savedata'] != 'Y') {
			//$DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/install/db/'.strtolower($DBType).'/uninstall.sql');
		}
		
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
					if ($item == '..' || $item == '.') continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		return true;
	}
	
	function DoInstall()
	{
		global $APPLICATION;
		
		if ($GLOBALS['APPLICATION']->GetGroupRight('main') < 'W') {
			$APPLICATION->ThrowException(GetMessage("TSV_NFPP_NO_RIGHTS"));
			return;
		}
		
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		RegisterModuleDependences("main", "OnBeforeProlog", self::MODULE_ID, "pp");
		
	}

	function DoUninstall()
	{
		global $APPLICATION;
		
		if ($GLOBALS['APPLICATION']->GetGroupRight('main') < 'W') {
			$APPLICATION->ThrowException(GetMessage("TSV_NFPP_NO_RIGHTS_UNINSTALL"));
			return;
		}
		
		$this->UnInstallFiles();
		$this->UnInstallDB();
		UnRegisterModuleDependences("main", "OnBeforeProlog", self::MODULE_ID, "pp");
		UnRegisterModule($this->MODULE_ID);
				
	}	
}
?>
