<?
global $MESS;

$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall) - strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");
include($PathInstall."/version.php");

Class %UNDER% extends CModule
{
	var $MODULE_ID = "%DOT%";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MLANG = "%UNDER_CAPS%_";

	function %UNDER%()
	{
		$this->MODULE_VERSION      = %UNDER_CAPS%_VERSION;
		$this->MODULE_VERSION_DATE = %UNDER_CAPS%_VERSION_DATE;
		$this->MODULE_NAME         = GetMessage($this->MLANG."INSTALL_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage($this->MLANG."INSTALL_DESCRIPTION");
		$this->PARTNER_NAME  		= GetMessage($this->MLANG."PARTNER_NAME");
	}

	function DoInstall()
	{
		global $DB, $APPLICATION;
		$APPLICATION->IncludeAdminFile(
			GetMessage($this->MLANG."INSTALL_TITLE"),
			$_SERVER["DOCUMENT_ROOT"].getLocalPath("modules/".$this->MODULE_ID."/install/step.php")
		);
	}

	function InstallFiles($arParams = array()){
		return true;
	}

	function UnInstallFiles(){
		return true;
	}

	function DoUninstall(){
		global $DB, $APPLICATION, $step;
		$step = IntVal($step);
		if($step < 2){
			$APPLICATION->IncludeAdminFile(GetMessage($this->MLANG."INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"].getLocalPath("modules/".$this->MODULE_ID."/install/unstep1.php"));
		}
		elseif($step == 2){
			$APPLICATION->IncludeAdminFile(GetMessage($this->MLANG."INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"].getLocalPath("modules/".$this->MODULE_ID."/install/unstep2.php"));
		}
	}
}

?>