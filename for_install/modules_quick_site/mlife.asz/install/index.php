<?
IncludeModuleLangFile(__FILE__);

class mlife_asz extends CModule
{
        var $MODULE_ID = "mlife.asz";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;

        function mlife_asz() {
				$path = str_replace("\\", "/", __FILE__);
				$path = substr($path, 0, strlen($path) - strlen("/index.php"));
				include($path."/version.php");
				
				$this->MODULE_VERSION = $arModuleVersion["VERSION"];
				$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
				$this->PARTNER_NAME = GetMessage("MLIFE_ASZ_PARTNER_NAME");
				$this->PARTNER_URI = GetMessage("MLIFE_ASZ_PARTNER_URI");
				$this->MODULE_NAME = GetMessage("MLIFE_ASZ_MODULE_NAME");
				$this->MODULE_DESCRIPTION = GetMessage("MLIFE_ASZ_MODULE_DESC");
				
			return true;
        }

        function DoInstall() {
			
			global $USER, $APPLICATION;
			
			if ($USER->IsAdmin())
			{
				if ($this->InstallDB())
				{
					RegisterModule("mlife.asz");
					
					$eventManager = \Bitrix\Main\EventManager::getInstance();
					$eventManager->registerEventHandler("mlife.asz", "BasketOnBeforeAdd", "mlife.asz", '\Mlife\Asz\DiscountHandlers', "BasketOnBeforeAdd");
					$eventManager->registerEventHandler("mlife.asz", "BasketOnBeforeUpdate", "mlife.asz", '\Mlife\Asz\DiscountHandlers', "BasketOnBeforeUpdate");
					$eventManager->registerEventHandler("mlife.asz", "OrderOnAfterAdd", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterAdd");
					$eventManager->registerEventHandler("mlife.asz", "OrderOnBeforeUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnBeforeUpdate");
					$eventManager->registerEventHandler("mlife.asz", "OrderOnAfterUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterUpdate");
					$eventManager->registerEventHandler("mlife.asz", "OrderOnAfterDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterDelete");
					$eventManager->registerEventHandler("mlife.asz", "OrderOnBeforeDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnBeforeDelete");
					
					$eventManager->registerEventHandlerCompatible("iblock", "OnIBlockPropertyBuildList", "mlife.asz", '\Mlife\Asz\Properties\AszMagazine', "GetUserTypeDescription");
					$eventManager->registerEventHandlerCompatible("main", "OnBuildGlobalMenu", "mlife.asz", '\Mlife\Asz\Handlers', "OnBuildGlobalMenu");
					$eventManager->registerEventHandlerCompatible("main", "OnAdminTabControlBegin", "mlife.asz", '\Mlife\Asz\Handlers', "OnAdminTabControlBegin");
					$eventManager->registerEventHandlerCompatible("iblock", "OnAfterIBlockElementAdd", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementAdd");
					$eventManager->registerEventHandlerCompatible("iblock", "OnAfterIBlockElementUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementAdd");
					$eventManager->registerEventHandlerCompatible("iblock", "OnAfterIBlockElementDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementDelete");
					
					$this->InstallFiles();
				}
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile(GetMessage("MLIFE_ASZ_MODULE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/step.php");
			}
			
        }

        function DoUninstall() {
			global $DB, $USER, $DOCUMENT_ROOT, $APPLICATION, $step;
			if ($USER->IsAdmin())
			{
				$step = IntVal($step);
				if ($step < 2)
				{
					$APPLICATION->IncludeAdminFile(GetMessage("MLIFE_ASZ_MODULE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/unstep1.php");
				}
				elseif ($step == 2)
				{
					$this->UnInstallDB(array(
						"save_tables" => $_REQUEST["save_tables"],
					));
					UnRegisterModule("mlife.asz");
					UnRegisterModuleDependences("mlife.asz", "OrderOnAfterAdd", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterAdd");
					UnRegisterModuleDependences("mlife.asz", "OrderOnBeforeUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnBeforeUpdate");
					UnRegisterModuleDependences("mlife.asz", "OrderOnAfterUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterUpdate");
					UnRegisterModuleDependences("mlife.asz", "OrderOnAfterDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterDelete");
					UnRegisterModuleDependences("mlife.asz", "OrderOnBeforeDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnBeforeDelete");
					UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", "mlife.asz", '\Mlife\Asz\Properties\AszMagazine', "GetUserTypeDescription");
					UnRegisterModuleDependences("main", "OnBuildGlobalMenu", "mlife.asz", '\Mlife\Asz\Handlers', "OnBuildGlobalMenu");
					UnRegisterModuleDependences("main", "OnAdminTabControlBegin", "mlife.asz", '\Mlife\Asz\Handlers', "OnAdminTabControlBegin");
					UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementAdd");
					UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementAdd");
					UnRegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementDelete");
					
					$this->UnInstallFiles();
					$GLOBALS["errors"] = $this->errors;
					$APPLICATION->IncludeAdminFile(GetMessage("MLIFE_ASZ_MODULE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/unstep2.php");
				}
			}
        }
	
	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/db/".strtolower($DB->type)."/install.sql");

		if ($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		return true;
	}
	
	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		if (!array_key_exists("save_tables", $arParams) || $arParams["save_tables"] != "Y")
		{
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/db/".strtolower($DB->type)."/uninstall.sql");
		}

		if ($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		return true;
	}
	
	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/wizards/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
		return true;
	}
	
	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/wizards/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools");
		return true;
	}
}

?>

