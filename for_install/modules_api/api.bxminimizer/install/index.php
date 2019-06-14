<?

use Bitrix\Main\ModuleManager,
	 Bitrix\Main\EventManager,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class api_bxminimizer extends CModule
{
	var $MODULE_ID           = 'api.bxminimizer';
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
		$this->MODULE_NAME         = GetMessage("api.bxminimizer_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.bxminimizer_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.bxminimizer_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.bxminimizer_PARTNER_URI");
	}


	function DoInstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$eventManager = EventManager::getInstance();
		$eventManager->registerEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, 'CApiBXMinimizer', 'onBuildGlobalMenu');

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/css", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/css/" . $this->MODULE_ID, true, true);

		ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$eventManager = EventManager::getInstance();
		$eventManager->unRegisterEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, 'CApiBXMinimizer', 'onBuildGlobalMenu');

		DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID . '/');

		ModuleManager::unRegisterModule($this->MODULE_ID);

		return true;
	}
}
?>