<?
IncludeModuleLangFile(__FILE__);
Class bitrix_mpbuilder extends CModule
{
	const MODULE_ID = 'bitrix.mpbuilder';
	var $MODULE_ID = 'bitrix.mpbuilder';
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
		$this->MODULE_NAME = GetMessage("BITRIX.MPBUILDER_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("BITRIX.MPBUILDER_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("BITRIX.MPBUILDER_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("BITRIX.MPBUILDER_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CBitrixMpBuilder', 'OnBuildGlobalMenu');

		// set exclude mask
		if (CModule::IncludeModule('security'))
		{
			$rs = CSecurityFilterMask::GetList();
			$myMask = '/bitrix/admin/bitrix.mpbuilder_*';

			$bMaskSet = false;
			$arFilterMask = array();
			while($f = $rs->Fetch())
			{
				if ($f['FILTER_MASK'] == $myMask)
					$bMaskSet = true;

				$arFilterMask[] = array('MASK' => $f['FILTER_MASK'], 'SITE_ID' => $f['SITE_ID']);
			}

			if (!$bMaskSet)
			{
				$arFilterMask[] = array('MASK' => $myMask, 'SITE_ID' => '');
				CSecurityFilterMask::Update($arFilterMask);
			}
		}
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CBitrixMpBuilder', 'OnBuildGlobalMenu');

		// unset exclude mask
		if (CModule::IncludeModule('security'))
		{
			$rs = CSecurityFilterMask::GetList();
			$myMask = '/bitrix/admin/bitrix.mpbuilder_*';

			$bMaskSet = false;
			$arFilterMask = array();
			while($f = $rs->Fetch())
			{
				if ($f['FILTER_MASK'] == $myMask)
					$bMaskSet = true;
				else
					$arFilterMask[] = array('MASK' => $f['FILTER_MASK'], 'SITE_ID' => $f['SITE_ID']);
			}

			if ($bMaskSet)
			{
				CSecurityFilterMask::Update($arFilterMask);
			}
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
		if (is_dir($admin = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($admin))
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
		if (is_dir($admin = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($admin))
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
