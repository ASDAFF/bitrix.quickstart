<?
IncludeModuleLangFile(__FILE__);

if(class_exists("d2mg_ufhtml")) return;

class d2mg_ufhtml extends CModule
{
	var $MODULE_ID = "d2mg.ufhtml";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	var $errors;

	function d2mg_ufhtml()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = GetMessage("inst_module_name");
		$this->MODULE_DESCRIPTION = GetMessage("inst_module_desc");
		$this->PARTNER_NAME = GetMessage("inst_module_partner"); 
		$this->PARTNER_URI = "http://www.d2mg.ru";
	}


	function InstallDB()
	{
		RegisterModule("d2mg.ufhtml");
		RegisterModuleDependences("main", "OnUserTypeBuildList", "d2mg.ufhtml", "CCustomTypeHtml", "GetUserTypeDescription");

		return true;
	}

	function UnInstallDB()
	{
		UnRegisterModuleDependences("main", "OnUserTypeBuildList", "d2mg.ufhtml", "CCustomTypeHtml", "GetUserTypeDescription");
		UnRegisterModule("d2mg.ufhtml");

		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage("inst_inst_title"), $DOCUMENT_ROOT."/bitrix/modules/d2mg.ufhtml/install/step.php");
	}

	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage("inst_uninst_title"), $DOCUMENT_ROOT."/bitrix/modules/d2mg.ufhtml/install/unstep.php");
	}

}
?>