<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
IncludeModuleLangFile(__FILE__);
class rksoft_selectcity extends CModule
{
	var $MODULE_ID = "rksoft.selectcity";
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAMESPACE = "rksoft";
	var $MODULE_COMPONENT_NAME = "select.city";

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("rksoft.select.city_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("rksoft.select.city_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("rksoft.select.city_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("rksoft.select.city_PARTNER_URI");
	}
	
	function installFiles()
	{
		if(is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components") and is_writable($_SERVER["DOCUMENT_ROOT"]."/bitrix/components"))
		{
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		}
		return true;
	}
	
	function unInstallFiles()
	{
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$this->MODULE_NAMESPACE."/".$this->MODULE_COMPONENT_NAME))
		{
			DeleteDirFilesEx("/bitrix/components/".$this->MODULE_NAMESPACE."/".$this->MODULE_COMPONENT_NAME);
		}
	}
	
	function DoInstall()
	{
		global $APPLICATION;
		$this->installFiles();
		RegisterModule($this->MODULE_ID);
	}
	
	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule($this->MODULE_ID);
		$this->unInstallFiles();
	}
}
?>