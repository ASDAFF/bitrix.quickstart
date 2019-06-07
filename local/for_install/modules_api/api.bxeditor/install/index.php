<?

use Bitrix\Main\ModuleManager,
	 Bitrix\Main\EventManager,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class api_bxeditor extends CModule
{
	var $MODULE_ID           = 'api.bxeditor';
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
		$this->MODULE_NAME         = GetMessage("api.bxeditor_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.bxeditor_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.bxeditor_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.bxeditor_PARTNER_URI");
	}

	function InstallEvents()
	{
		$eventManager = EventManager::getInstance();
		$eventManager->registerEventHandler('fileman', 'OnBeforeHTMLEditorScriptRuns', $this->MODULE_ID, '\Api\BXEditor\Button', 'init');

		return true;
	}

	function UnInstallEvents()
	{
		$eventManager = EventManager::getInstance();
		$eventManager->unRegisterEventHandler('fileman', 'OnBeforeHTMLEditorScriptRuns', $this->MODULE_ID, '\Api\BXEditor\Button', 'init');

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/" . $this->MODULE_ID, true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/');

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->InstallFiles();
		$this->InstallEvents();

		ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->UnInstallFiles();
		$this->UnInstallEvents();

		ModuleManager::unRegisterModule($this->MODULE_ID);

		return true;
	}
}

?>