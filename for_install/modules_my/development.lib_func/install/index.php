<?
global $MESS;
IncludeModuleLangFile(__FILE__);

Class development_lib_func extends CModule
{
    var $MODULE_ID = 'development.lib_func';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function development_lib_func()
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

		$this->MODULE_NAME = GetMessage('RSDF.MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('RSDF.MODULE_DESCRIPTION');
		$this->PARTNER_NAME = GetMessage('RSDF.DEVELOPER_NAME');
        $this->PARTNER_URI  = 'https://asdaff.github.io/';
	}

	// Install functions
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		RegisterModule('development.lib_func');
		return TRUE;
	}

	function InstallEvents()
	{
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', 'development.lib_func', 'RSLibFuncOffersExtension', 'OnAfterIBlockElementAddHandler',10000);
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'development.lib_func', 'RSLibFuncOffersExtension', 'OnAfterIBlockElementUpdateHandler',10000);
		RegisterModuleDependences('catalog', 'OnPriceAdd', 'development.lib_func', 'RSLibFuncOffersExtension', 'OnPriceUpdateAddHandler',10000);
		RegisterModuleDependences('catalog', 'OnPriceUpdate', 'development.lib_func', 'RSLibFuncOffersExtension', 'OnPriceUpdateAddHandler',10000);
		return TRUE;
	}

	function InstallOptions()
	{
		COption::SetOptionString('development.lib_func', 'fakeprice_active', "Y" );
		COption::SetOptionString('development.lib_func', 'propcode_cml2link', "CML2_LINK" );
		COption::SetOptionString('development.lib_func', 'propcode_fakeprice', "PROD_PRICE_FALSE" );
		return TRUE;
	}

	function InstallFiles()
	{
		COption::SetOptionString('development.lib_func', 'no_photo_path', '/bitrix/modules/development.lib_func/img/no-photo.png');
		$arFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/development.lib_func/img/no-photo.png');
		$fid = CFile::SaveFile($arFile, 'development_lib_func_nophoto');
		COption::SetOptionInt('development.lib_func', 'no_photo_fileid', $fid);
		
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/development.lib_func/install/js', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js', true, true);
		return TRUE;
	}

	function InstallPublic()
	{
		return TRUE;
	}

	// UnInstal functions
	function UnInstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		UnRegisterModule('development.lib_func');
		return TRUE;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', 'development.lib_func', 'RSLibFuncOffersExtension', 'OnAfterIBlockElementAddHandler');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'development.lib_func', 'RSLibFuncOffersExtension', 'OnAfterIBlockElementUpdateHandler');
		UnRegisterModuleDependences('catalog', 'OnPriceAdd', 'development.lib_func', 'RSLibFuncOffersExtension', 'OnPriceUpdateAddHandler');
		UnRegisterModuleDependences('catalog', 'OnPriceUpdate', 'development.lib_func', 'RSLibFuncOffersExtension', 'OnPriceUpdateAddHandler');
		return TRUE;
	}

	function UnInstallOptions()
	{
		COption::RemoveOption('development.lib_func');
		return TRUE;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx('/bitrix/js/development.lib_func');
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
		$APPLICATION->IncludeAdminFile(GetMessage('SPER_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/development.lib_func/install/install.php');
    }

    function DoUninstall()
    {
		global $APPLICATION, $step;
		$keyGoodFiles = $this->UnInstallFiles();
		$keyGoodEvents = $this->UnInstallEvents();
		$keyGoodOptions = $this->UnInstallOptions();
		$keyGoodDB = $this->UnInstallDB();
		$keyGoodPublic = $this->UnInstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage('SPER_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/development.lib_func/install/uninstall.php');
    }
}