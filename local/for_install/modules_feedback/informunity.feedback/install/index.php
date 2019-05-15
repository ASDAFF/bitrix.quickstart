<?
global $DOCUMENT_ROOT, $MESS;

IncludeModuleLangFile(__FILE__);

if (class_exists("informunity_feedback")) return;

Class informunity_feedback extends CModule
{
	var $MODULE_ID = "informunity.feedback";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function informunity_feedback()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = "informUnity";
		$this->PARTNER_URI = "http://informunity.ru";

		$this->MODULE_NAME = GetMessage("IU_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("IU_MODULE_DESCRIPTION");
	}

	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("informunity.feedback"))
		{
			RegisterModule("informunity.feedback");
			CopyDirFiles(
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/informunity.feedback/install/components/",
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/components",
				true, true
			);
		}
	}

	function DoUninstall()
	{
		UnRegisterModule("informunity.feedback");
	}
}
?>