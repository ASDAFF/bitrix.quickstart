<?
IncludeModuleLangFile(__FILE__);
Class abudagov_aisort extends CModule
{
	var $MODULE_ID = 'abudagov.aisort';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $strError = '';

	function __construct()
	{
		global $USER;
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("abudagov.aisort_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("abudagov.aisort_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("abudagov.aisort_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("abudagov.aisort_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		if(!IsModuleInstalled($this->MODULE_ID)) {
			RegisterModule($this->MODULE_ID);
		}

		RegisterModuleDependences("main", "OnAdminTabControlBegin", $this->MODULE_ID, "CAbudagovAISort", "AddTab");
		RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID, "CAbudagovAISort", "OnPageStart");
		RegisterModuleDependences("main", "OnAdminTabControlBegin", $this->MODULE_ID, "CAbudagovAISort", "ChangeSortInForm");
		RegisterModuleDependences("iblock", "OnBeforeIBlockElementAdd", $this->MODULE_ID, "CAbudagovAISort", "OnBeforeElementAdd");
		RegisterModuleDependences("iblock", "OnBeforeIBlockSectionAdd", $this->MODULE_ID, "CAbudagovAISort", "OnBeforeSectionAdd");

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		UnRegisterModule($this->MODULE_ID);
		UnRegisterModuleDependences("main", "OnAdminTabControlBegin", $this->MODULE_ID, "CAbudagovAISort", "AddTab");
		UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID, "CAbudagovAISort", "OnPageStart");
		UnRegisterModuleDependences("main", "OnAdminTabControlBegin", $this->MODULE_ID, "CAbudagovAISort", "ChangeSortInForm");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementAdd", $this->MODULE_ID, "CAbudagovAISort", "OnBeforeElementAdd");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockSectionAdd", $this->MODULE_ID, "CAbudagovAISort", "OnBeforeSectionAdd");

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
		return true;
	}

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		$this->InstallFiles();
		$this->InstallDB();

		if(!IsModuleInstalled($this->MODULE_ID)) {
			RegisterModule($this->MODULE_ID);
		}

	}

	function DoUninstall()
	{
		global $APPLICATION;

		UnRegisterModule($this->MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
