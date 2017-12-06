<?
IncludeModuleLangFile(__FILE__);
if (class_exists("fgsoft_propmedia")) return;

Class fgsoft_propmedia extends CModule
{
	const MODULE_ID = 'fgsoft.propmedia';
	var $MODULE_ID = 'fgsoft.propmedia'; 
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

		$this->MODULE_NAME = GetMessage("fgsoft.propmedia_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("fgsoft.propmedia_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("fgsoft.propmedia_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("fgsoft.propmedia_PARTNER_URI");
	}

	function DoInstall()
	{
		RegisterModule(self::MODULE_ID);

		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", self::MODULE_ID, "FGSoftPropMediaLibIblockProperty", "GetUserTypeDescription");
		RegisterModuleDependences("main", "OnUserTypeBuildList", self::MODULE_ID, "FGSoftPropMediaLibUserType", "GetUserTypeDescription");
	}

	function DoUninstall()
	{
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", self::MODULE_ID, "FGSoftPropMediaLibIblockProperty", "GetUserTypeDescription");
		UnRegisterModuleDependences("main", "OnUserTypeBuildList", self::MODULE_ID, "FGSoftPropMediaLibUserType", "GetUserTypeDescription");

		UnRegisterModule(self::MODULE_ID);
	}
}
?>
