<?

use Bitrix\Main\ModuleManager,
	 Bitrix\Main\EventManager,
	 Bitrix\Main\SiteTable,
	 Bitrix\Main\Application,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class api_mail extends CModule
{
	var $MODULE_ID           = 'api.mail';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION      = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME         = GetMessage("api.mail_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.mail_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.mail_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.mail_PARTNER_URI");
	}

	function DoInstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		if($this->InstallDB()){
			RegisterModule($this->MODULE_ID);

			$this->InstallFiles();
			$this->InstallEvents();
		}

		return true;
	}

	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;

		$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');
		if(!empty($errors)) {
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}

		return true;
	}

	function InstallEvents()
	{
		$eventManager = EventManager::getInstance();
		$eventManager->registerEventHandler('main', 'OnBeforeEventAdd', $this->MODULE_ID, '\Api\Mail\Event', 'OnBeforeEventAdd');
		$eventManager->registerEventHandler('main', 'OnBeforeEventSend', $this->MODULE_ID, '\Api\Mail\Event', 'OnBeforeEventSend');
		$eventManager->registerEventHandler('main', 'OnBeforeMailSend', $this->MODULE_ID, '\Api\Mail\Event', 'OnBeforeMailSend');

		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);

		return true;
	}



	function DoUninstall()
	{
		global $APPLICATION, $step;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;


		$step = intval($step);
		if($step < 2)
			$APPLICATION->IncludeAdminFile('', $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep1.php");
		else
		{
			$arParams = array(
				 "savedata" => $_REQUEST["savedata"],
			);

			$this->UnInstallDB($arParams);
			$this->UnInstallEvents();
			$this->UnInstallFiles();

			UnRegisterModule($this->MODULE_ID);
		}


		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = null;
		if(array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y") {
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
			if(!empty($errors)) {
				$APPLICATION->ThrowException(implode("", $errors));
				return false;
			}
		}

		return true;
	}

	function UnInstallEvents()
	{
		$eventManager = EventManager::getInstance();
		$eventManager->unRegisterEventHandler('main', 'OnBeforeEventAdd', $this->MODULE_ID, '\Api\Mail\Event', 'OnBeforeEventAdd');
		$eventManager->unRegisterEventHandler('main', 'OnBeforeEventSend', $this->MODULE_ID, '\Api\Mail\Event', 'OnBeforeEventSend');
		$eventManager->unRegisterEventHandler('main', 'OnBeforeMailSend', $this->MODULE_ID, '\Api\Mail\Event', 'OnBeforeMailSend');

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');

		return true;
	}
}

?>