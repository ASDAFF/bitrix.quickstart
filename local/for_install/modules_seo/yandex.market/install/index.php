<?php

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class yandex_market extends CModule
{
    var $MODULE_ID = 'yandex.market';
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $PARTNER_NAME;
	var $PARTNER_URI;

    function __construct()
    {
        $arModuleVersion = null;

        include __DIR__ . '/version.php';

        if (isset($arModuleVersion) && is_array($arModuleVersion))
        {
	        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
	        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

	    $this->MODULE_NAME = GetMessage('YANDEX_MARKET_MODULE_NAME');
	    $this->MODULE_DESCRIPTION = GetMessage('YANDEX_MARKET_MODULE_DESCRIPTION');

        $this->PARTNER_NAME = GetMessage('YANDEX_MARKET_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('YANDEX_MARKET_PARTNER_URI');
    }

    function DoInstall()
    {
        global $APPLICATION;

        $result = true;

        try
        {
	        $this->checkRequirements();

	        Main\ModuleManager::registerModule($this->MODULE_ID);

	        if (Main\Loader::includeModule($this->MODULE_ID))
	        {
		        $this->InstallDB();
		        $this->InstallEvents();
		        $this->InstallAgents();
		        $this->InstallFiles();
		    }
		    else
		    {
		        throw new Main\SystemException(GetMessage('YANDEX_MARKET_MODULE_NOT_REGISTERED'));
		    }
	    }
	    catch (\Exception $exception)
	    {
	        $result = false;
	        $APPLICATION->ThrowException($exception->getMessage());
	    }

	    return $result;
    }

    function DoUninstall()
    {
        if (Main\Loader::includeModule($this->MODULE_ID))
        {
	        $this->UnInstallDB();
	        $this->UnInstallEvents();
	        $this->UnInstallAgents();
	        $this->UnInstallFiles();
        }

        Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    function InstallDB()
    {
		Market\Reference\Storage\Controller::createTable();
    }

    function UnInstallDB()
    {
        Market\Reference\Storage\Controller::dropTable();
    }

    function InstallEvents()
    {
		Market\Reference\Event\Controller::updateRegular();
    }

    function UnInstallEvents()
    {
        Market\Reference\Event\Controller::deleteAll();
    }

    function InstallAgents()
    {
        Market\Reference\Agent\Controller::updateRegular();
    }

    function UnInstallAgents()
    {
		Market\Reference\Agent\Controller::deleteAll();
    }

    function InstallFiles()
    {
        CopyDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/', true, true);
        CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/yandex.market/', true, true);
        CopyDirFiles(__DIR__ . '/css', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/yandex.market/', true, true);
        CopyDirFiles(__DIR__ . '/js', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/yandex.market/', true, true);
        CopyDirFiles(__DIR__ . '/images', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/yandex.market/', true, true);
        CopyDirFiles(__DIR__ . '/templates', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/.default/components/', true, true);
    }

    function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');
        DeleteDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/yandex.market/');
        DeleteDirFiles(__DIR__ . '/css', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/yandex.market/');
        DeleteDirFiles(__DIR__ . '/js', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/yandex.market/');
        DeleteDirFiles(__DIR__ . '/images', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/yandex.market/');
        DeleteDirFiles(__DIR__ . '/templates', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/.default/components/');
    }

    function checkRequirements()
    {
        // require php version

		$requirePhp = '5.4.0';

        if (CheckVersion(PHP_VERSION, $requirePhp) === false)
        {
			throw new \Exception(GetMessage('YANDEX_MARKET_INSTALL_REQUIRE_PHP', [ '#VERSION#' => $requirePhp ]));
        }

        // require simplexml extension

		if (!class_exists('\\SimpleXMLElement'))
		{
			throw new \Exception(GetMessage('YANDEX_MARKET_INSTALL_REQUIRE_SIMPLEXML'));
		}

        // required modules

        $requireModules = [
			'main' => '15.5.0',
			'iblock' => '15.0.0'
		];

        if (class_exists('\\Bitrix\\Main\\ModuleManager'))
        {
			foreach ($requireModules as $moduleName => $moduleVersion)
			{
				$currentVersion = Main\ModuleManager::getVersion($moduleName);

				if ($currentVersion !== false && CheckVersion($currentVersion, $moduleVersion))
				{
					unset($requireModules[$moduleName]);
				}
			}
        }

        if (!empty($requireModules))
        {
            foreach ($requireModules as $moduleName => $moduleVersion)
            {
                throw new \Exception(GetMessage('YANDEX_MARKET_INSTALL_REQUIRE_MODULE', [
                    '#MODULE#' => $moduleName,
                    '#VERSION#' => $moduleVersion
                ]));
            }
        }
    }
}