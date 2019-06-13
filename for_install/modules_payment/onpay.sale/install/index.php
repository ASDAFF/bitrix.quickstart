<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));

Class onpay_sale extends CModule
{
	var $MODULE_ID = "onpay.sale";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function onpay_sale()
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
		else
		{
			$this->MODULE_VERSION = CURRENCY_VERSION;
			$this->MODULE_VERSION_DATE = CURRENCY_VERSION_DATE;
		}

		$this->PARTNER_URI  = "http://www.onpay.ru";
		$this->PARTNER_NAME = GetMessage("ONPAY.SALE_PARTNER_NAME");
		$this->MODULE_NAME = GetMessage("ONPAY.SALE_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ONPAY.SALE_INSTALL_DESCRIPTION");
	}

	function DoInstall()
	{
		global $APPLICATION, $step;
		$GLOBALS["errors"] = false;
		$step = IntVal($step);
		if($step<2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("ONPAY.SALE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/onpay.sale/install/step1.php");
		}
		elseif($step==2 && empty($_REQUEST['login']))
		{
			$this->errors[] = GetMessage("ONPAY.SALE_INSTALL_LOGIN_EMPTY");
			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("ONPAY.SALE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/onpay.sale/install/step1.php");
		}
		elseif($step==2)
		{
			$this->InstallFiles();
			$this->InstallDB();
			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("ONPAY.SALE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/onpay.sale/install/step2.php");
		}
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;
		$step = IntVal($step);
		if($step<2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("ONPAY.SALE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/onpay.sale/install/unstep1.php");
		}
		elseif($step==2)
		{
			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
			));
			$this->UnInstallFiles();
			
			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("ONPAY.SALE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/onpay.sale/install/unstep2.php");
		}
	}
	
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		RegisterModule("onpay.sale");
		$arOptions = array("login", "api_in_key", "success_url", "fail_url", "iframe_form", "convert");
		if(CModule::IncludeModule("currency")) {
			$lcur = CCurrency::GetList(($b="name"), ($order1="asc"), LANGUAGE_ID);
			while($lcur_res = $lcur->Fetch()) {
				$arOptions[] = "currency_".$lcur_res['CURRENCY'];
			}
		}
		foreach($arOptions as $name) {
			COption::SetOptionString("onpay.sale", $name, $_REQUEST[$name], "");
		}
		return true;
	}
	
	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		UnRegisterModule("onpay.sale");
		return true;
	}


	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/onpay.sale/install/sale_payment/onpay.sale/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/onpay.sale/");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/onpay.sale/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/");
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/onpay.sale");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/onpay.sale/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/");
		return true;
	}
	
}
?>










