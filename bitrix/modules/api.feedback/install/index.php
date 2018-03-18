<?
IncludeModuleLangFile(__FILE__);
Class api_feedback extends CModule
{
	const MODULE_ID = 'api.feedback';
	var $MODULE_ID = 'api.feedback'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $SITE_ID;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("api.feedback_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("api.feedback_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.feedback_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("api.feedback_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		return true;
	}

	function InstallEvents()
	{
		//RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CApiFeedback', 'OnBuildGlobalMenu');
		//RegisterModuleDependences(self::MODULE_ID, 'OnBeforeEmailSend', self::MODULE_ID);
		//RegisterModuleDependences(self::MODULE_ID, 'OnAfterEmailSend', self::MODULE_ID);

		include_once($_SERVER["DOCUMENT_ROOT"] ."/bitrix/modules/". $this->MODULE_ID ."/install/events.php");

		return true;
	}

	function UnInstallEvents()
	{
		//UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CApiFeedback', 'OnBuildGlobalMenu');
		//UnRegisterModuleDependences(self::MODULE_ID, 'OnBeforeEmailSend', self::MODULE_ID);
		//UnRegisterModuleDependences(self::MODULE_ID, 'OnAfterEmailSend', self::MODULE_ID);

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
		global $APPLICATION, $step, $arSites;

		$step = intval($step);
		$ob_sites = CSite::GetList($by="id",$order="asc",array("ACTIVE"=>"Y"));
		while($rs_site = $ob_sites->Fetch())
			$arSites[] = $rs_site;

		if(empty($arSites))
		{
			CAdminMessage::ShowMessage(GetMessage('SITES_NOT_FOUND'));

			return false;
		}

		if($step < 2 && count($arSites) > 1)
			$APPLICATION->IncludeAdminFile(GetMessage("IBLOCK_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/". $this->MODULE_ID ."/install/step1.php");
		else
		{
			if(!empty($_REQUEST["API_SITE_ID"]) && is_array($_REQUEST["API_SITE_ID"]))
				$this->SITE_ID = $_REQUEST["API_SITE_ID"];
			else
				$this->SITE_ID[] = $arSites[0]['ID'];
		}

		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvents();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}