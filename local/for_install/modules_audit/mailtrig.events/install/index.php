<?
IncludeModuleLangFile(__FILE__);

if (class_exists("mailtrig_events"))
	return;

class mailtrig_events extends CModule
{
	var $MODULE_ID = "mailtrig.events";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function __construct()
	{
		$arModuleVersion = array();

		include(dirname(__FILE__)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("MAILTRIG_EVENTS_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MAILTRIG_EVENTS_INSTALL_DESCRIPTION");

		$this->PARTNER_NAME = GetMessage("MAILTRIG_EVENTS_PARTNER");
		$this->PARTNER_URI = "http://mailtrig.ru";
	}


	function InstallDB()
	{
		global $DB;

		//$DB->RunSQLBatch(dirname(__FILE__)."/sql/install.sql");

		RegisterModule($this->MODULE_ID);

		COption::SetOptionString($this->MODULE_ID, "site_name", COption::GetOptionString("main", "site_name"));
		COption::SetOptionString($this->MODULE_ID, "server_name", COption::GetOptionString("main", "server_name"));
		COption::SetOptionString($this->MODULE_ID, "site_email", COption::GetOptionString("main", "email_from"));

		// register events
		RegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, 'CMailTrigEventsHandler', "onEpilogHandler");
		RegisterModuleDependences("main", "OnAfterUserAuthorize", $this->MODULE_ID, 'CMailTrigEventsHandler', "onAfterUserAuthorizeHandler");
		RegisterModuleDependences("main", "OnAfterUserRegister", $this->MODULE_ID, 'CMailTrigEventsHandler', "onAfterUserRegisterHandler");
		RegisterModuleDependences("main", "OnBeforeUserUpdate", $this->MODULE_ID, 'CMailTrigEventsHandler', "onBeforeUserUpdateHandler");

		RegisterModuleDependences("sale", "OnBasketAdd", $this->MODULE_ID, 'CMailTrigEventsHandler', "onBasketAddHandler");
		RegisterModuleDependences("sale", "OnBeforeBasketDelete", $this->MODULE_ID, 'CMailTrigEventsHandler', "onBeforeBasketDeleteHandler");
		RegisterModuleDependences("sale", "OnBasketUpdate", $this->MODULE_ID, 'CMailTrigEventsHandler', "onBasketUpdateHandler");

		RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, 'CMailTrigEventsHandler', "orderStartHandler");
		RegisterModuleDependences("sale", "OnSalePayOrder", $this->MODULE_ID, 'CMailTrigEventsHandler', "orderFinishHandler");

		return true;
	}

	function UnInstallDB()
	{
		global $DB;

		//$DB->RunSQLBatch(dirname(__FILE__)."/sql/uninstall.sql");

		COption::RemoveOption($this->MODULE_ID, "login");
		COption::RemoveOption($this->MODULE_ID, "password");
		COption::RemoveOption($this->MODULE_ID, "appId");
		COption::RemoveOption($this->MODULE_ID, "partner");

		COption::RemoveOption($this->MODULE_ID, "site_name");
		COption::RemoveOption($this->MODULE_ID, "server_name");
		COption::RemoveOption($this->MODULE_ID, "site_email");
		COption::RemoveOption($this->MODULE_ID, "support_phone");
		COption::RemoveOption($this->MODULE_ID, "profile_url");
		COption::RemoveOption($this->MODULE_ID, "basket_url");
		COption::RemoveOption($this->MODULE_ID, "order_url");
		COption::RemoveOption($this->MODULE_ID, "debug_mode");

		// unregister events
		UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, 'CMailTrigEventsHandler', "onEpilogHandler");
		UnRegisterModuleDependences("main", "OnAfterUserAuthorize", $this->MODULE_ID, 'CMailTrigEventsHandler', "onAfterUserAuthorizeHandler");
		UnRegisterModuleDependences("main", "OnAfterUserRegister", $this->MODULE_ID, 'CMailTrigEventsHandler', "onAfterUserRegisterHandler");
		UnRegisterModuleDependences("main", "OnBeforeUserUpdate", $this->MODULE_ID, 'CMailTrigEventsHandler', "onBeforeUserUpdateHandler");

		UnRegisterModuleDependences("sale", "OnBasketAdd", $this->MODULE_ID, 'CMailTrigEventsHandler', "onBasketAddHandler");
		UnRegisterModuleDependences("sale", "OnBeforeBasketDelete", $this->MODULE_ID, 'CMailTrigEventsHandler', "onBeforeBasketDeleteHandler");
		UnRegisterModuleDependences("sale", "OnBasketUpdate", $this->MODULE_ID, 'CMailTrigEventsHandler', "onBasketUpdateHandler");

		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, 'CMailTrigEventsHandler', "orderStartHandler");
		UnRegisterModuleDependences("sale", "OnSalePayOrder", $this->MODULE_ID, 'CMailTrigEventsHandler', "orderFinishHandler");

		UnRegisterModule($this->MODULE_ID);

		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		CopyDirFiles(dirname(__FILE__)."/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
		CopyDirFiles(dirname(__FILE__)."/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", true, true);
		CopyDirFiles(dirname(__FILE__)."/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles(dirname(__FILE__)."/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools");
		DeleteDirFiles(dirname(__FILE__)."/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images");
		DeleteDirFiles(dirname(__FILE__)."/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes");

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $USER, $step;

		// steps
		$step = intval($step);
		if($step < 2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("MAILTRIG_EVENTS_INSTALL_PROCESS") . GetMessage("MAILTRIG_EVENTS_INSTALL_NAME"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/". $this->MODULE_ID ."/install/step.php");
		}
		elseif($step == 2)
		{
			session_start();

			$this->InstallFiles();
			$this->InstallDB();

			if(strlen($_REQUEST["ContinueRegister"]) > 0)
			{
				// register
				$email = COption::GetOptionString("main", "email_from");
				if(strlen($email) == 0)
				{
					// find admin
					$email = $USER->GetEmail();
				}

				CModule::IncludeModule("mailtrig.events");

				$obClient = new CMailTrigClient;

				$sMtApiUrl = "http://" . COption::GetOptionString("mailtrig.events", "server_name") . (($_SERVER["SERVER_PORT"] != 80)?':'.$_SERVER["SERVER_PORT"]:'') . "/bitrix/tools/mailtrig_events_api.php";
				//$sPartner = COption::GetOptionString("mailtrig.events", "partner");

				$pass = randString(8);
				$arRegisterResult = $obClient->regUser($email, $pass, $sMtApiUrl);

				if($arRegisterResult["status"] == "200")
				{
					COption::SetOptionString("mailtrig.events", "login", $email);
					COption::SetOptionString("mailtrig.events", "password", $pass);
					COption::SetOptionString("mailtrig.events", "appId", $arRegisterResult["data"]["appId"]);
				}
				else
				{
					$_SESSION["MAILTRIG_OPTIONS"]["ERROR"] = array(
						GetMessage("MAILTRIG_EVENTS_INSTALL_REGISTER_ERROR") . $arRegisterResult["error_message"]
					);
				}

				$_SESSION["MAILTRIG_OPTIONS"]["NOTE"] = array(
					GetMessage("MAILTRIG_EVENTS_INSTALL_SUCCESS")
				);
				LocalRedirect("/bitrix/admin/settings.php?lang=".LANG_ID."&mid=".$this->MODULE_ID);
			}
			else
			{
				$_SESSION["MAILTRIG_OPTIONS"]["NOTE"] = array(
					GetMessage("MAILTRIG_EVENTS_INSTALL_SUCCESS")
				);
				LocalRedirect("/bitrix/admin/settings.php?lang=".LANG_ID."&mid=".$this->MODULE_ID);
			}
		}

		return true;
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallFiles();

		return true;
	}
}