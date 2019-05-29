<?
IncludeModuleLangFile(__FILE__);
Class webprostor_underconstruct extends CModule
{
	const MODULE_ID = 'webprostor.underconstruct';
	var $MODULE_ID = 'webprostor.underconstruct'; 
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
		$this->MODULE_NAME = GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("WEBPROSTOR_UNDERCONSTRUCT_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("WEBPROSTOR_UNDERCONSTRUCT_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("WEBPROSTOR_UNDERCONSTRUCT_PARTNER_URI");
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
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/wizards/webprostor/underconstruct', $_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards/webprostor/underconstruct', true, true);
		mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/site_closed/');
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx('/bitrix/wizards/webprostor/underconstruct');
		rmdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards/webprostor/');
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		RegisterModule(self::MODULE_ID);
		$APPLICATION->IncludeAdminFile(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webprostor.underconstruct/install/install_1.php");
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;
		$step = IntVal($step);
		
		if($step<2)
		{
			$GLOBALS["APPLICATION"]->IncludeAdminFile(GetMessage("WEBPROSTOR_UNDERCONSTRUCT_DELETE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webprostor.underconstruct/install/uninstall_1.php");
		}
		elseif($step==2)
		{
			global $APPLICATION;
			UnRegisterModule(self::MODULE_ID);
			if($_REQUEST["deletelogodir"] != "Y")
			{
				DeleteDirFilesEx('/upload/site_closed');
				rmdir($_SERVER['DOCUMENT_ROOT'].'/upload/site_closed/');
			}
			
			$rsSites = CSite::GetList();
			$placeHolders = Array();
			while ($arSite = $rsSites->Fetch())
			{
				$placeHolders[] = $arSite["LID"];
			}
			foreach($placeHolders as $site):
				if($_REQUEST["delete_placeholder_".$site] != "Y")
				{
					DeleteDirFilesEx('/bitrix/php_interface/'.$site.'/site_closed.php');
					rmdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/'.$site.'/');
				}
			endforeach;
			
			$this->UnInstallFiles();
		}
	}
}
?>
