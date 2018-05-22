<?
global $MESS;
IncludeModuleLangFile(__FILE__);

Class redsign_quickbuy extends CModule
{
    var $MODULE_ID = 'redsign.quickbuy';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function redsign_quickbuy()
	{
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');
	
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        } else {
            $this->MODULE_VERSION = '1.0.0';
            $this->MODULE_VERSION_DATE = '2014.01.01';
        }

		$this->MODULE_NAME = GetMessage('RSQB.MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('RSQB.MODULE_DESCRIPTION');
		$this->PARTNER_NAME = GetMessage('RSQB.MODULE_DEVELOPER_NAME');
        $this->PARTNER_URI  = 'http://redsign.ru/';
	}

	// Install functions
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		RegisterModule('redsign.quickbuy');
		$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.quickbuy/install/db/'.$DBType.'/install.sql');
		return TRUE;
	}

	function InstallEvents()
	{
		RegisterModuleDependences('main', 'OnAdminTabControlBegin', 'redsign.quickbuy', 'CRSQUICKBUYTab', 'MyOnAdminTabControlBegin');
		RegisterModuleDependences('main', 'OnBeforeProlog', 'redsign.quickbuy', 'CRSQUICKBUYMain', 'OnBeforePrologElementUpdate');
		RegisterModuleDependences('sale', 'OnOrderUpdate', 'redsign.quickbuy', 'CRSQUICKBUYMain', 'OnOrderUpdate');
		return TRUE;
	}

	function InstallOptions()
	{
		return TRUE;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.quickbuy/install/copyfiles/components', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);
		return TRUE;
	}

	function InstallPublic()
	{
		return TRUE;
	}

	// UnInstal functions
	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;
		$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.quickbuy/install/db/'.$DBType.'/uninstall.sql');
		UnRegisterModule('redsign.quickbuy');
		return TRUE;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences('main', 'OnAdminTabControlBegin', 'redsign.quickbuy', 'CRSQUICKBUYTab', 'MyOnAdminTabControlBegin');
		UnRegisterModuleDependences('main', 'OnBeforeProlog', 'redsign.quickbuy', 'CRSQUICKBUYMain', 'OnBeforePrologElementUpdate');
		UnRegisterModuleDependences('sale', 'OnOrderUpdate', 'redsign.quickbuy', 'CRSQUICKBUYMain', 'OnOrderUpdate');
		return TRUE;
	}

	function UnInstallOptions()
	{
		COption::RemoveOption('redsign.quickbuy');
		return TRUE;
	}

	function UnInstallFiles()
	{
		return TRUE;
	}

	function UnInstallPublic()
	{
		return TRUE;
	}

    function DoInstall()
    {
		global $APPLICATION, $step;
		$keyGoodDB = $this->InstallDB();
		$keyGoodEvents = $this->InstallEvents();
		$keyGoodOptions = $this->InstallOptions();
		$keyGoodFiles = $this->InstallFiles();
		$keyGoodPublic = $this->InstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage('SPER_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.quickbuy/install/install.php');
    }

    function DoUninstall()
    {
		global $APPLICATION, $step;
		$keyGoodFiles = $this->UnInstallFiles();
		$keyGoodEvents = $this->UnInstallEvents();
		$keyGoodOptions = $this->UnInstallOptions();
		$keyGoodDB = $this->UnInstallDB();
		$keyGoodPublic = $this->UnInstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage('SPER_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/redsign.quickbuy/install/uninstall.php');
    }
}