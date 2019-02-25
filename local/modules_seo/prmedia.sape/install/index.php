<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

if(class_exists("prmedia_sape")) return;

Class prmedia_sape extends CModule
{
	var $MODULE_ID = "prmedia.sape";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	function prmedia_sape()
	{
        $this->MODULE_NAME = GetMessage("PRMEDIA_INSTALL_NAME"); 
        $this->MODULE_DESCRIPTION = GetMessage("PRMEDIA_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = "Progressive Media";
		$this->PARTNER_URI = "http://www.progressivemedia.ru";

		$arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
	}

	function DoInstall()
	{
		global $DB, $APPLICATION, $step;
		$step = IntVal($step);
		if($step<2) $APPLICATION->IncludeAdminFile(GetMessage('PRMEDIA_INSTALL_TITLE'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/prmedia.sape/install/step1.php");
		elseif($step==2) $APPLICATION->IncludeAdminFile(GetMessage('PRMEDIA_INSTALL_TITLE'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/prmedia.sape/install/step2.php");
	}

	function DoUninstall()
	{
		global $DB, $APPLICATION, $step;

		$APPLICATION->IncludeAdminFile(GetMessage('PRMEDIA_UNINSTALL_TITLE'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/prmedia.sape/install/unstep1.php");

	}

	function GetModuleRightList()
	{

	}

}
?>