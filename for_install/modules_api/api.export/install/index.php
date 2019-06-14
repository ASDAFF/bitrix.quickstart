<?
use Bitrix\Main\ModuleManager,
	 Bitrix\Main\EventManager,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Class api_export extends CModule
{
	const MYSQL_TABLE = 'api_export_profile';

	var $MODULE_ID           = 'api.export';
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
		$this->MODULE_NAME         = GetMessage("api.export_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.export_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.export_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.export_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = null;
		if(!$DB->Query("SELECT 'x' FROM `" . self::MYSQL_TABLE . "`", true))
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');

		if(!empty($errors)) {
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = null;
		if(array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y") {
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
			//$DB->Query("DELETE FROM `b_option` WHERE `MODULE_ID` = '".$this->MODULE_ID."'", true);
			//$DB->Query("DELETE FROM `b_event_log` WHERE `MODULE_ID` = '".$this->MODULE_ID."'", true);

			if(!empty($errors)) {
				$APPLICATION->ThrowException(implode("", $errors));
				return false;
			}
		}

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');
		//DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID . '/');

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->InstallFiles();
		$this->InstallDB();

		ModuleManager::registerModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		ModuleManager::unRegisterModule($this->MODULE_ID);

		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}

?>