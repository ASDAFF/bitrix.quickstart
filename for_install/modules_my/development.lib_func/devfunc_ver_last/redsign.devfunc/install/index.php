<?
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Localization\Loc;

global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class redsign_devfunc extends CModule
{
	var $MODULE_ID = "redsign.devfunc";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function redsign_devfunc()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("REDSIGN.DEVFUNC.INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("REDSIGN.DEVFUNC.INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("REDSIGN.DEVFUNC.SPER_PARTNER");
		$this->PARTNER_URI = GetMessage("REDSIGN.DEVFUNC.PARTNER_URI");
	}

	function InstallDB($install_wizard = true)
	{
		global $DB, $DBType, $APPLICATION;

		ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;
		ModuleManager::unregisterModule($this->MODULE_ID);

		return true;
	}

	function InstallEvents()
	{
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', 'redsign.devfunc', 'RSDevFuncOffersExtension', 'OnAfterIBlockElementAddHandler',10000);
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'redsign.devfunc', 'RSDevFuncOffersExtension', 'OnAfterIBlockElementUpdateHandler',10000);
		if (isModuleInstalled('catalog') && isModuleInstalled('sale'))
		{
			RegisterModuleDependences('catalog', 'OnPriceAdd', 'redsign.devfunc', 'RSDevFuncOffersExtension', 'OnPriceUpdateAddHandler',10000);
			RegisterModuleDependences('catalog', 'OnPriceUpdate', 'redsign.devfunc', 'RSDevFuncOffersExtension', 'OnPriceUpdateAddHandler',10000);
		}

		RegisterModuleDependences('main', 'OnEpilog', 'redsign.devfunc', 'RSSeo', 'addMetaOG', 10000);

		if (ModuleManager::isModuleInstalled('iblock'))
		{
			EventManager::getInstance()->registerEventHandler(
				'iblock',
				'OnIBlockPropertyBuildList',
				$this->MODULE_ID,
				'\Redsign\DevFunc\Iblock\Property',
				'OnIBlockPropertyBuildListStores'
			);

			EventManager::getInstance()->registerEventHandler(
				'iblock',
				'OnIBlockPropertyBuildList',
				$this->MODULE_ID,
				'\Redsign\DevFunc\Iblock\Property',
				'OnIBlockPropertyBuildListPrices'
			);

			EventManager::getInstance()->registerEventHandler(
				'iblock',
				'OnIBlockPropertyBuildList',
				$this->MODULE_ID,
				'\Redsign\DevFunc\Iblock\Property',
				'OnIBlockPropertyBuildListLocations'
			);
		}
        
        EventManager::getInstance()->registerEventHandler(
            'main',
            'onMainGeoIpHandlersBuildList',
            $this->MODULE_ID,
            '\Redsign\DevFunc\Sale\Location\Location',
            'onGeoIpHandlersBuildList'
        );

		return true;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', 'redsign.devfunc', 'RSDevFuncOffersExtension', 'OnAfterIBlockElementAddHandler');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'redsign.devfunc', 'RSDevFuncOffersExtension', 'OnAfterIBlockElementUpdateHandler');
		if (isModuleInstalled('catalog') && isModuleInstalled('sale'))
		{
			UnRegisterModuleDependences('catalog', 'OnPriceAdd', 'redsign.devfunc', 'RSDevFuncOffersExtension', 'OnPriceUpdateAddHandler');
			UnRegisterModuleDependences('catalog', 'OnPriceUpdate', 'redsign.devfunc', 'RSDevFuncOffersExtension', 'OnPriceUpdateAddHandler');
		}

		UnRegisterModuleDependences('main', 'OnEpilog', 'redsign.devfunc', 'RSSeo', 'addMetaOG');

		if (ModuleManager::isModuleInstalled('iblock'))
		{
			EventManager::getInstance()->unRegisterEventHandler(
				'iblock',
				'OnIBlockPropertyBuildList',
				$this->MODULE_ID,
				'\Redsign\DevFunc\Iblock\Property',
				'OnIBlockPropertyBuildListStores'
			);

			EventManager::getInstance()->unRegisterEventHandler(
				'iblock',
				'OnIBlockPropertyBuildList',
				$this->MODULE_ID,
				'\Redsign\DevFunc\Iblock\Property',
				'OnIBlockPropertyBuildListPrices'
			);

			EventManager::getInstance()->unRegisterEventHandler(
				'iblock',
				'OnIBlockPropertyBuildList',
				$this->MODULE_ID,
				'\Redsign\DevFunc\Iblock\Property',
				'OnIBlockPropertyBuildListLocations'
			);
		}
        
        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'onMainGeoIpHandlersBuildList',
            $this->MODULE_ID,
            '\Redsign\DevFunc\Sale\Location\Location',
            'onGeoIpHandlersBuildList'
        );

		return true;
	}

	function InstallFiles()
	{
		COption::SetOptionString('redsign.devfunc', 'no_photo_path', '/bitrix/modules/redsign.devfunc/img/no-photo.png');
		$arFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.devfunc/img/no-photo.png');
		$fid = CFile::SaveFile($arFile, 'redsign_devfunc_nophoto');
		COption::SetOptionInt('redsign.devfunc', 'no_photo_fileid', $fid);

		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.devfunc/install/js', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js', true, true);

		 /* Panel */
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/panel", $_SERVER["DOCUMENT_ROOT"]."/bitrix/panel", true, true);
		
		/* Admin */
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx('/bitrix/js/redsign.devfunc');

		DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/admin",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin"
		);
		DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/panel/redsign.devfunc",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/panel/redsign.devfunc"
		);

		DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/themes/.default",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default"
		);
		
		DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/components/redsign",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/components/redsign"
		);

		return true;
	}

	function InstallPublic()
	{
		return true;
	}

	function UnInstallPublic()
	{
		return true;
	}

	function InstallOptions()
	{
		COption::SetOptionString('redsign.devfunc', 'fakeprice_active', "Y" );
		COption::SetOptionString('redsign.devfunc', 'propcode_cml2link', "CML2_LINK" );
		COption::SetOptionString('redsign.devfunc', 'propcode_fakeprice', "PROD_PRICE_FALSE" );
		return true;
	}

	function UnInstallOptions()
	{
		COption::RemoveOption('redsign.devfunc');
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallOptions();
		$this->InstallEvents();
		$this->InstallPublic();

		$APPLICATION->IncludeAdminFile(GetMessage('REDSIGN.DEVFUNC.INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.devfunc/install/install.php');

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallOptions();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$this->UnInstallPublic();

		$APPLICATION->IncludeAdminFile(GetMessage('REDSIGN.DEVFUNC.UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.devfunc/install/uninstall.php');

		return true;
	}
}