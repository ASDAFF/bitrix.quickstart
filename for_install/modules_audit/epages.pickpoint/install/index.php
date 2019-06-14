<?
global $MESS;

IncludeModuleLangFile(__FILE__);

if (class_exists("epages_pickpoint")) return;

Class epages_pickpoint extends CModule
{
	var $MODULE_ID = "epages.pickpoint";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function epages_pickpoint()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->PARTNER_NAME="E-Pages";
		$this->PARTNER_URI="http://epages.su/";

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

		$this->MODULE_NAME = GetMessage("PP_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("PP_MODULE_DESCRIPTION");
	}

	function DoInstall()
	{
		$this->InstallFiles();
		$this->InstallDB();

		RegisterModuleDependences('sale', 'OnOrderAdd', $this->MODULE_ID, 'CPickpoint', 'OnOrderAdd');
		RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepComplete', $this->MODULE_ID, 'CPickpoint', 'OnOrderAddV15');
		\Bitrix\Main\EventManager::getInstance()->registerEventHandler(
			'sale',
			'OnSaleOrderSaved',
			$this->MODULE_ID,
			'CPickpoint',
			'OnSaleOrderSaved'
		);
		RegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'CPickpoint', 'CheckRequest');
		RegisterModuleDependences(
			'sale',
			'OnSaleComponentOrderOneStepDelivery',
			$this->MODULE_ID,
			'CPickpoint',
			'OnSCOrderOneStepDeliveryHandler'
		);
		RegisterModuleDependences(
			'sale',
			'OnSaleComponentOrderOneStepPersonType',
			$this->MODULE_ID,
			'CPickpoint',
			'addPickpointJs'
		);

		RegisterModule("epages.pickpoint");

		$GLOBALS["errors"] = $this->errors;
	}

	function DoUninstall()
	{
		
		global $APPLICATION, $step;
		$step = IntVal($step);

		if($step<2)
		{
			COption::RemoveOption($this->MODULE_ID);
			$APPLICATION->IncludeAdminFile(
				GetMessage("ST_INSTALL_TITLE"),
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/unstep1.php"
			);
		}
		elseif($step==2)
		{
			$this->UnInstallDB(array("savedata" => $_REQUEST["savedata"],));
			$this->UnInstallFiles();
			$GLOBALS["errors"] = $this->errors;

		    UnRegisterModuleDependences('sale', 'OnOrderAdd', $this->MODULE_ID, 'CPickpoint', 'OnOrderAdd');
		    UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepComplete', $this->MODULE_ID, 'CPickpoint', 'OnOrderAddV15');
			\Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler(
				'sale',
				'OnSaleOrderSaved',
				$this->MODULE_ID,
				'CPickpoint',
				'OnSaleOrderSaved'
			);
			UnRegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'CPickpoint', 'CheckRequest');
			UnRegisterModuleDependences(
				'sale',
				'OnSaleComponentOrderOneStepDelivery',
				$this->MODULE_ID,
				'CPickpoint',
				'OnSCOrderOneStepDeliveryHandler'
			);
			UnRegisterModuleDependences(
				'sale',
				'OnSaleComponentOrderOneStepPersonType',
				$this->MODULE_ID,
				'CPickpoint',
				'addPickpointJs'
			);

			UnRegisterModule("epages.pickpoint");

			$APPLICATION->IncludeAdminFile(
				GetMessage("ST_INSTALL_TITLE"),
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/unstep2.php"
			);
		}

		$GLOBALS["errors"] = $this->errors;
	}

	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/db/".$DBType."/install.sql");
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
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/db/".$DBType."/uninstall.sql");
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
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/admin",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin",
			true
		);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/images/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/images/epages.pickpoint/",
			true,
			true
		);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/themes/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/",
			true,
			true
		);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/js/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/",
			true,
			true
		);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/delivery/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_delivery/",
			true,
			true
		);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/payment/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/epages.pickpoint/",
			true,
			true
		);
	}

	function UnInstallFiles()
	{
		DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/admin",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin"
		);
		DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/themes/.default/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default"
		);
		DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/install/delivery/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_delivery"
		);
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/epages.pickpoint/");
		DeleteDirFilesEx("/bitrix/themes/.default/icons/epages.pickpoint/");
		DeleteDirFilesEx("/bitrix/images/epages.pickpoint/");
		DeleteDirFilesEx("/bitrix/js/epages.pickpoint");
	}
}
