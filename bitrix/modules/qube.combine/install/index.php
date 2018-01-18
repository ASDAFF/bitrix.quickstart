<?
IncludeModuleLangFile(__FILE__);
Class qube_combine extends CModule
{
	const MODULE_ID = 'qube.combine';
	var $MODULE_ID = 'qube.combine'; 
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
		$this->MODULE_NAME = GetMessage("QUBE_COMBINE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("QUBE_COMBINE_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("QUBE_COMBINE_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("QUBE_COMBINE_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		global $DB, $APPLICATION;
		
		RegisterModule(self::MODULE_ID);
		RegisterModuleDependences('main', 'OnAdminContextMenuShow', self::MODULE_ID, 'CQubeCombine', 'OnAdminContextMenuShowHandler');
		RegisterModuleDependences('main', 'OnAdminTabControlBegin', self::MODULE_ID, 'CQubeCombine', 'OnAdminTabControlBeginHandler');
		RegisterModuleDependences('sale', 'OnBeforeOrderPropsUpdate', self::MODULE_ID, 'CQubeCombine', 'OnBeforeOrderPropsUpdateHandler');
		RegisterModuleDependences('sale', 'OnOrderPropsAdd', self::MODULE_ID, 'CQubeCombine', 'OnOrderPropsEdit');
		RegisterModuleDependences('sale', 'OnOrderPropsUpdate', self::MODULE_ID, 'CQubeCombine', 'OnOrderPropsEdit');
		RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepProcess', self::MODULE_ID, 'CQubeCombine', 'OnSaleComponentOrderOneStepProcessHandler');
		COption::SetOptionString(self::MODULE_ID, 'COMBINE_FIELDS', serialize(array('CODE')));
		COption::SetOptionString(self::MODULE_ID, 'ADMIN_ACTIVE', 'Y');
		COption::SetOptionString(self::MODULE_ID, 'COMPONENT_ACTIVE', 'Y');

		return true;
	}

	function UnInstallDB($arParams = array())
	{
	
		UnRegisterModule(self::MODULE_ID);
		UnRegisterModuleDependences('main', 'OnAdminContextMenuShow', self::MODULE_ID, 'CQubeCombine', 'OnAdminContextMenuShowHandler');
		UnRegisterModuleDependences('main', 'OnAdminTabControlBegin', self::MODULE_ID, 'CQubeCombine', 'OnAdminTabControlBeginHandler');
		UnRegisterModuleDependences('sale', 'OnBeforeOrderPropsUpdate', self::MODULE_ID, 'CQubeCombine', 'OnBeforeOrderPropsUpdateHandler');
		UnRegisterModuleDependences('sale', 'OnOrderPropsAdd', self::MODULE_ID, 'CQubeCombine', 'OnOrderPropsEdit');
		UnRegisterModuleDependences('sale', 'OnOrderPropsUpdate', self::MODULE_ID, 'CQubeCombine', 'OnOrderPropsEdit');
		UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepProcess', self::MODULE_ID, 'CQubeCombine', 'OnSaleComponentOrderOneStepProcessHandler');
		
		return true;
	}
	
	function InstallEvents($arParams = array())
	{
		return true;
	}

	function UnInstallEvents($arParams = array())
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".self::MODULE_ID, true, true);
		return true;
	}

	function UnInstallFiles()
	{
		//DeleteDirFilesEx("/bitrix/images/".self::MODULE_ID."/");
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallEvents();
		$this->InstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage("QUBE_COMBINE_INST_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/step.php");
	}

	function DoUninstall()
	{
		global $APPLICATION;

		$APPLICATION->ResetException();
		if (!check_bitrix_sessid())
			return false;
		
		$step = IntVal($_REQUEST['step']);
		if($step < 2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("QUBE_COMBINE_UNINST_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/unstep1.php");
		}
		elseif($step == 2)
		{
			$this->UnInstallDB();
			$this->UnInstallFiles();
			$this->UnInstallEvents();
			$APPLICATION->IncludeAdminFile(GetMessage("QUBE_COMBINE_UNINST_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/unstep2.php");
		}	
	}
}
?>