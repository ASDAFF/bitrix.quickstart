<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
    
class kriteris_win2utf extends CModule{
	var $MODULE_ID = "kriteris.win2utf";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

  function kriteris_win2utf(){
    $arModuleVersion = array();
		include(__DIR__."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = "Kriteris";
		$this->PARTNER_URI = "http://www.kriteris.ru/";
		$this->MODULE_NAME = GetMessage("KUTF8_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("KUTF8_INSTALL_DESCRIPTION");
		return true;
  }

	function InstallDB($install_wizard = true){
		global $DB, $DBType, $APPLICATION;
		RegisterModule($this->MODULE_ID);
		return true;
	}

	function UnInstallDB($arParams = Array()){
		global $DB, $DBType, $APPLICATION;
		UnRegisterModule($this->MODULE_ID);
		return true;
	}

	function InstallEvents(){
		return true;
	}

	function UnInstallEvents(){
		return true;
	}

	function InstallFiles(){
		return true;
	}

	function InstallPublic(){
    return true;
	}

	function UnInstallFiles(){
		return true;
	}

	function DoInstall(){
		global $APPLICATION, $step;
		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kriteris.win2utf/install/step.php");
	}

	function DoUninstall(){
		global $APPLICATION, $step;
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kriteris.win2utf/install/unstep.php");
	}
}
?>
