<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Class api_message extends CModule
{
	var $MODULE_ID = 'api.message';
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
		$this->MODULE_NAME         = GetMessage("ASM_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("ASM_MODULE_DESC");
		$this->PARTNER_NAME        = GetMessage("ASM_PARTNER_NAME");
		$this->PARTNER_URI         = GetMessage("ASM_PARTNER_URI");
	}

	function checkDependency()
	{
		$bMainValid = (defined('SM_VERSION') && version_compare(SM_VERSION, '15.00.00', '>='));

		return (bool)($bMainValid);
	}

	function installConfigs()
	{
		if(Loader::includeModule($this->MODULE_ID))
		{
			//SELECT COUNT(*) FROM `api_message_config`
			if(!\Api\Message\ConfigTable::getCount())
			{
				$arSites = SiteTable::getList(array(
					'select' => array('LID'),
					'filter' => array('ACTIVE' => 'Y'),
				))->fetchAll();

				foreach($arSites as $site)
				{
					$siteId = $site['LID'];

					$arConfig = array(
						'USE_JQUERY'     => 'jquery2',
						'CACHE_TTL'      => 3600,
						'COOKIE_NAME'    => ToUpper(Option::get("main", "cookie_name", "BITRIX_SM") . '_' . $siteId),
					);

					foreach($arConfig as $key => $val)
					{
						$arData = array(
							'NAME'    => $key,
							'VALUE'   => $val,
							'SITE_ID' => $siteId,
						);
						\Api\Message\ConfigTable::addEx($arData);
					}
				}
			}
		}
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$connection = Application::getConnection();
		//$helper = $connection->getSqlHelper();

		if($connection->isTableExists('api_systemmessage')){
			$connection->renameTable('api_systemmessage','api_message');

			if($connection->isTableExists('api_systemmessage_config')){
				$connection->renameTable('api_systemmessage_config','api_message_config');
			}
		}
		else{
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');
			if(!empty($errors))
			{
				$APPLICATION->ThrowException(implode("", $errors));
				return false;
			}
		}

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = null;
		if(array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y")
		{
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
			if(!empty($errors))
			{
				$APPLICATION->ThrowException(implode("", $errors));
				return false;
			}
		}

		return true;
	}

	function InstallEvents()
	{
		$eventManager = EventManager::getInstance();

		//$eventManager->registerEventHandler('main', 'OnPageStart', $this->MODULE_ID, 'CApiMessage', 'OnPageStart');
		$eventManager->registerEventHandler('main', 'OnProlog', $this->MODULE_ID, 'CApiMessage', 'onProlog');

		return true;
	}

	function UnInstallEvents()
	{
		$eventManager = EventManager::getInstance();

		//$eventManager->unRegisterEventHandler('main', 'OnPageStart', $this->MODULE_ID, 'CApiMessage', 'OnPageStart');
		$eventManager->unRegisterEventHandler('main', 'OnProlog', $this->MODULE_ID, 'CApiMessage', 'onProlog');

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/css/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/'. $this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/images/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/'. $this->MODULE_ID, true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');

		DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID . '/');
		DeleteDirFilesEx('/bitrix/images/' . $this->MODULE_ID . '/');

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if(!$this->checkDependency())
		{
			$APPLICATION->IncludeAdminFile('', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/error_dependency.php');
			return false;
		}

		$this->InstallEvents();
		$this->InstallFiles();
		$this->InstallDB();

		ModuleManager::registerModule($this->MODULE_ID);
		$this->installConfigs();

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$step = intval($step);
		if($step < 2)
			$APPLICATION->IncludeAdminFile('', $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep1.php");
		else
		{
			$arParams = array(
				"savedata" => $_REQUEST["savedata"],
			);

			ModuleManager::unRegisterModule($this->MODULE_ID);
			$this->UnInstallDB($arParams);
			$this->UnInstallEvents();
			$this->UnInstallFiles();
		}

		return true;
	}
}

?>