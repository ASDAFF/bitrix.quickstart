<?
global $MESS;

$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));

if (class_exists("tcsbank_kupivkredit")) return; 
Class tcsbank_kupivkredit extends CModule
{
	var $MODULE_ID = "tcsbank.kupivkredit";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function tcsbank_kupivkredit()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->PARTNER_NAME = GetMessage("TCS_MODULE_NAME");
		$this->PARTNER_URI="http://kupivkredit.ru";
		
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = ST_VERSION;
			$this->MODULE_VERSION_DATE = ST_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("TCS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("TCS_MODULE_DESCRIPTION");
	}


	
	function DoInstall()
	{
		global $APPLICATION, $step;
		$step = IntVal($step);
		//print_r($step);	die();
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule("tcsbank.kupivkredit");
		RegisterModuleDependences("sale", "OnSaleCancelOrder", "tcsbank.kupivkredit", "CTCSBank", "OnCancelOrder");
		RegisterModuleDependences("sale", "OnBeforeOrderDelete", "tcsbank.kupivkredit", "CTCSBank", "OnDeleteOrder");
		$APPLICATION->IncludeAdminFile(GetMessage("ST_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/step2.php");
		$GLOBALS["errors"] = $this->errors;
		
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;
		$step = IntVal($step);
		
		if($step<2)
		{
			COption::RemoveOption($this->MODULE_ID);		
			$APPLICATION->IncludeAdminFile(GetMessage("ST_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
		}
		elseif($step==2)
		{
			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
			));
			$this->UnInstallFiles();
			$GLOBALS["errors"] = $this->errors;
			UnRegisterModuleDependences("sale", "OnBeforeOrderDelete", "tcsbank.kupivkredit", "CTCSBank", "OnDeleteOrder");
			UnRegisterModuleDependences("sale", "OnSaleCancelOrder", $this->MODULE_ID, "CTCSBank", "OnCancelOrder");
			UnRegisterModule($this->MODULE_ID);
			$APPLICATION->IncludeAdminFile(GetMessage("ST_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep2.php");
		}
		$GLOBALS["errors"] = $this->errors;
	}
	function GetModuleRightList()
	{
		$arr = array(
			"reference_id" => array("D","F","S","T","W"),
			"reference" => array(
				"[D] ".GetMessage("TCS_RIGHTS_STATE_D"),
				"[F] ".GetMessage("TCS_RIGHTS_STATE_F"),
				"[S] ".GetMessage("TCS_RIGHTS_STATE_S"),
				"[T] ".GetMessage("TCS_RIGHTS_STATE_T"),
				"[W] ".GetMessage("TCS_RIGHTS_STATE_W")
			)   
		);  
		return $arr;	
	
	}	
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/db/".$DBType."/install.sql");
		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		}
		

		return true;
	}
	
	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		if(array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y")
		{
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/db/".$DBType."/uninstall.sql");
			if($this->errors !== false)
			{
				$APPLICATION->ThrowException(implode("", $this->errors));
				return false;
			}
		}

		return true;
	}


	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/tcsbank.kupivkredit/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/payment/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/tcsbank.kupivkredit/", true, true);
	}

	
	
	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js");
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/tcsbank.kupivkredit/");
		DeleteDirFilesEx("/bitrix/themes/.default/icons/tcsbank.kupivkredit/");
		//DeleteDirFilesEx("/bitrix/images/tcsbank.kupivkredit/");
		DeleteDirFilesEx("/bitrix/js/tcsbank.kupivkredit");
	}

}
?>