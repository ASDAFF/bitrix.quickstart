<?
IncludeModuleLangFile(__FILE__);

if(class_exists("rarus_sms4b")) return;

class rarus_sms4b extends CModule
{
	var $MODULE_ID = "rarus.sms4b";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	var $errors;

	function rarus_sms4b()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->PARTNER_NAME = GetMessage("COMPANY_NAME");
			$this->PARTNER_URI = "http://rarus.ru";
		}
		else
		{
			$this->MODULE_VERSION = SMS4B_VERSION;
			$this->MODULE_VERSION_DATE = SMS4B_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("SMS4B_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SMS4B_INSTALL_DESCRIPTION");
	}

	function DoInstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;

		$POST_RIGHT = $APPLICATION->GetGroupRight("rarus.sms4b");

		if($POST_RIGHT == "W")
		{
			$step = intval($step);
			if($step<2)
			{
				$APPLICATION->IncludeAdminFile(GetMessage('inst_inst_title'),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/step1.php");
			}
			elseif($step==2)
			{
				$this->InstallDB();
				$this->InstallEvents();
				$this->InstallFiles();

				$APPLICATION->IncludeAdminFile(GetMessage('inst_inst_title'),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/step2.php");
			}
		}
	}

	function DoUninstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;

		$POST_RIGHT = $APPLICATION->GetGroupRight("rarus.sms4b");
		if($POST_RIGHT == "W")
		{
			$step = IntVal($step);
			if($step<2)
			{
				$APPLICATION->IncludeAdminFile(GetMessage('inst_uninst_title'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/unstep1.php");
			}
			elseif($step==2)
			{
				$this->UnInstallDB(array(
					"save_tables" => $_REQUEST["save_tables"],
				));
				//message types and templates
				if($_REQUEST["save_templates"] != "Y")
				{
					$this->UnInstallEvents();
				}
				$this->UnInstallFiles();

				$GLOBALS["errors"] = $this->errors;

				$APPLICATION->IncludeAdminFile(GetMessage('inst_uninst_title'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/unstep2.php");
			}
		}
	}

	function InstallDB($arParams = array())
	{

		global $DB, $DBType, $APPLICATION;

		$this->errors = false;

		$this->errors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/db/".strtolower($DB->type)."/install.sql");

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		else
		{
			RegisterModule("rarus.sms4b");
			CModule::IncludeModule("rarus.sms4b");
			return true;
		}
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if(!array_key_exists("save_tables", $arParams) || ($arParams["save_tables"] != "Y"))
		{
			//kick current user options
			COption::RemoveOption("rarus.sms4b", "");
			//drop tables
			$this->errors = $DB->RunSqlBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/db/".strtolower($DB->type)."/uninstall.sql");
			//drop files
			$strSql = "SELECT ID FROM b_file WHERE MODULE_ID='rarus.sms4b'";
			$rsFile = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			while($arFile = $rsFile->Fetch())
				CFile::Delete($arFile["ID"]);
		}

		UnRegisterModule("rarus.sms4b");
		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		return true;
	}

	function InstallEvents()
	{
		global $DB;
		RegisterModuleDependences("main", "OnBeforeEventAdd", "rarus.sms4b", "Csms4b", "Events");
		RegisterModuleDependences("subscribe", "BeforePostingSendMail", "rarus.sms4b", "Csms4b", "EventsPosting");

		//обработка событий для КП (only)
		/* Ждем когда в Битрикс добавят обработчика календарных событий*/
		if (COption::GetOptionString("main", "vendor") == '1c_bitrix_portal')
		{
			RegisterModuleDependences('tasks', 'OnTaskAdd', 'rarus.sms4b', 'Csms4b', 'TaskAdded',10001);
			RegisterModuleDependences('tasks', 'OnTaskUpdate', 'rarus.sms4b', 'Csms4b', 'TaskUpdated',10001);
			RegisterModuleDependences('tasks', 'OnBeforeTaskDelete', 'rarus.sms4b', 'Csms4b', 'BeforeTaskDeleted',10001);
		}

		//install templates for events
		global $DB;
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/events.php");
		return true;
	}

	function UnInstallEvents()
	{
		global $DB;
		UnRegisterModuleDependences("main", "OnBeforeEventAdd", "rarus.sms4b", "Csms4b", "Events");
		UnRegisterModuleDependences("subscribe", "BeforePostingSendMail", "rarus.sms4b", "Csms4b", "EventsPosting");

		//uninstall events for

		if (COption::GetOptionString("main", "vendor") == '1c_bitrix_portal')
		{
			UnRegisterModuleDependences('tasks', 'OnTaskAdd', 'rarus.sms4b', 'Csms4b', 'TaskAdded',10001);
			UnRegisterModuleDependences('tasks', 'OnTaskUpdate', 'rarus.sms4b', 'Csms4b', 'TaskUpdated',10001);
			UnRegisterModuleDependences('tasks', 'OnBeforeTaskDelete', 'rarus.sms4b', 'Csms4b', 'BeforeTaskDeleted',10001);
		}

		$statusMes = array();
		$dbStatus = $DB->Query("SELECT * FROM b_sale_status", true);

		if ($dbStatus)
		{
			while($arStatus = $dbStatus->Fetch())
			{
				$eventType = new CEventType;
				$eventType->Delete("SMS4B_SALE_STATUS_CHANGED_".$arStatus["ID"]);
				$eventType->Delete("SMS4B_ADMIN_SALE_STATUS_CHANGED_".$arStatus["ID"]);
				$statusMes[] = "SMS4B_SALE_STATUS_CHANGED_".$arStatus["ID"];
				$statusMes[] = "SMS4B_ADMIN_SALE_STATUS_CHANGED_".$arStatus["ID"];
			}
		}
		//@todo Переписать более универсально
		$eventType = new CEventType;
		$eventType->Delete("SMS4B_ADMIN_SEND");
		$eventType->Delete("SMS4B_TASK_ADD");
		$eventType->Delete("SMS4B_TASK_UPDATE");
		$eventType->Delete("SMS4B_TASK_DELETE");
		$eventType->Delete("SMS4B_SALE_NEW_ORDER");
		$eventType->Delete("SMS4B_ADMIN_SALE_NEW_ORDER");
		$eventType->Delete("SMS4B_SALE_ORDER_CANCEL");
		$eventType->Delete("SMS4B_ADMIN_SALE_ORDER_CANCEL");
		$eventType->Delete("SMS4B_SALE_ORDER_PAID");
		$eventType->Delete("SMS4B_ADMIN_SALE_ORDER_PAID");
		$eventType->Delete("SMS4B_SALE_ORDER_DELIVERY");
		$eventType->Delete("SMS4B_ADMIN_SALE_ORDER_DELIVERY");
		$eventType->Delete("SMS4B_SALE_RECURRING_CANCEL");
		$eventType->Delete("SMS4B_ADMIN_SALE_RECURRING_CANCEL");
		$eventType->Delete("SMS4B_SALE_STATUS_CHANGED");
		$eventType->Delete("SMS4B_ADMIN_SALE_STATUS_CHANGED");
		$eventType->Delete("SMS4B_SUBSCRIBE_CONFIRM");
		$eventType->Delete("SMS4B_ADMIN_SUBSCRIBE_CONFIRM");
		$eventType->Delete("SMS4B_TICKET_NEW_FOR_TECHSUPPORT");
		$eventType->Delete("SMS4B_ADMIN_TICKET_NEW_FOR_TECHSUPPORT");
		$eventType->Delete("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT");
		$eventType->Delete("SMS4B_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT");

		$statusMes[] = "SMS4B_ADMIN_SEND";
		$statusMes[] = "SMS4B_TASK_ADD";
		$statusMes[] = "SMS4B_TASK_UPDATE";
		$statusMes[] = "SMS4B_TASK_DELETE";
		$statusMes[] = "SMS4B_SALE_NEW_ORDER";
		$statusMes[] = "SMS4B_ADMIN_SALE_NEW_ORDER";
		$statusMes[] = "SMS4B_SALE_ORDER_CANCEL";
		$statusMes[] = "SMS4B_ADMIN_SALE_ORDER_CANCEL";
		$statusMes[] = "SMS4B_SALE_ORDER_PAID";
		$statusMes[] = "SMS4B_ADMIN_SALE_ORDER_PAID";
		$statusMes[] = "SMS4B_SALE_ORDER_DELIVERY";
		$statusMes[] = "SMS4B_ADMIN_SALE_ORDER_DELIVERY";
		$statusMes[] = "SMS4B_SALE_RECURRING_CANCEL";
		$statusMes[] = "SMS4B_ADMIN_SALE_RECURRING_CANCEL";
		$statusMes[] = "SMS4B_SALE_STATUS_CHANGED";
		$statusMes[] = "SMS4B_ADMIN_SALE_STATUS_CHANGED";
		$statusMes[] = "SMS4B_SUBSCRIBE_CONFIRM";
		$statusMes[] = "SMS4B_ADMIN_SUBSCRIBE_CONFIRM";
		$statusMes[] = "SMS4B_TICKET_NEW_FOR_TECHSUPPORT";
		$statusMes[] = "SMS4B_ADMIN_TICKET_NEW_FOR_TECHSUPPORT";
		$statusMes[] = "SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT";
		$statusMes[] = "SMS4B_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT";

		foreach($statusMes as $v)
		{
			$eventM = new CEventMessage;
			$dbEvent = CEventMessage::GetList($b="ID", $order="ASC", Array("EVENT_NAME" => $v));
			while($arEvent = $dbEvent->Fetch())
			{
				$eventM->Delete($arEvent["ID"]);
			}
		}

		return true;
	}

	function InstallFiles($arParams = array())
	{
		global $APPLICATION;
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", false, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", false, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/wizard", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/rarus.sms4b/", false, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/", false, true);

		if($_REQUEST["INSTALL_COMPONENTS"] == "Y")
		{
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", false, true);
		}
		if($_REQUEST["INSTALL_DEMO"] == "Y")
		{
			if (!function_exists("file_get_contents"))
			{
				function file_get_contents($filename)
				{
					$fd = fopen("$filename", "rb");
					$content = fread($fd, filesize($filename));
					fclose($fd);
					return $content;
				}
			}

			function FileArray($path, $exclude = ".|..", $recursive = true)
			{
				$path = rtrim($path, "/")."/";
				$folder_handle = opendir($path);
				$exclude_array = explode("|", $exclude);
				$result = array();
				while(false !== ($filename = readdir($folder_handle))) {
					if(!in_array(strtolower($filename), $exclude_array))
					{
						if(is_dir($path . $filename . "/"))
						{
							if($recursive) $result[] = FileArray($path . $filename . "/", $exclude, true);
						}
						else
						{
							$result[] = $filename;
						}
					}
				}
				return $result;
			}

			$baseDir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/";
			$files[] = array("files" => FileArray($baseDir .  "admin"), "dir" => "admin");
			$files[] = array("files" => FileArray($baseDir .  "classes/general"), "dir" => "classes/general");

		foreach ($files as $file)
		{
			foreach($file['files'] as $arIndex)
			{
				if (defined('BX_UTF') && BX_UTF)
				{
					$fileContents = $APPLICATION->ConvertCharset(file_get_contents($baseDir . $file["dir"] . "/" . $arIndex), "WINDOWS-1251", "UTF-8");
				}
				else
				{
					$fileContents = file_get_contents($baseDir . $file["dir"] . "/" . $arIndex);
				}

				if ($fileContents)
				{
					if ($f = fopen($baseDir . $file["dir"] . "/" . $arIndex, "w+"))
					{
						@fwrite($f, $fileContents);
						@fclose($f);
					}
				}
			}
		}

			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/public", $_SERVER["DOCUMENT_ROOT"]."/sms4b_demo", false, true);
		}
		if($_REQUEST["INSTALL_HELP"] == "Y")
		{
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/help", $_SERVER["DOCUMENT_ROOT"]."/bitrix/help/ru/source/service/rarus.sms4b", false, true);
		}
		return true;
	}

	function UnInstallFiles()
	{
		//admin files
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		//css
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/install/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		//icons
		DeleteDirFilesEx("/bitrix/themes/.default/icons/rarus.sms4b");
		//images
		DeleteDirFilesEx("/bitrix/images/rarus.sms4b");
		//wizard
		DeleteDirFilesEx("/bitrix/wizards/rarus.sms4b");
		//delete js
		DeleteDirFilesEx("/bitrix/js/rarus.sms4b");
		//COMPONENTS
		if($_REQUEST["SAVE_COMPONENTS"] != "Y")
		{
			DeleteDirFilesEx("/bitrix/components/rarus.sms4b");
		}
		//delete help
		if($_REQUEST["SAVE_HELP"] != "Y")
		{
			DeleteDirFilesEx("/bitrix/help/ru/source/service/rarus.sms4b");
		}
		//delete demo public part
		if($_REQUEST["SAVE_DEMO"] != "Y")
		{
			DeleteDirFilesEx("/sms4b_demo");
		}
		return true;
	}
}
?>