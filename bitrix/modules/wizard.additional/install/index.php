<?php
/**
 * Created by PhpStorm.
 * User: Сергей
 * Date: 05.05.2015
 * Time: 15:55
 */

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

global $server, $context;
$context = \Bitrix\Main\Application::getInstance()->getContext();
$server = $context->getServer();

if (class_exists('wizard_additional'))
	return;

Class wizard_additional extends CModule
{
	var $MODULE_ID = 'wizard.additional';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $PARTNER_CODE = 'wizard';
	var $WIZARD_NAME = 'additional';
	var $strError = '';

	/**
	 *
	 */
	function wizard_additional()
	{
		$this->MODULE_NAME = Loc::getMessage('MODULE_NAME_WIZARD_AFEX');
		$this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_DESC_WIZARD_AFEX');

		$this->PARTNER_NAME = GetMessage('PARTNER_NAME_WIZARD_AFEX');
		$this->PARTNER_URI = GetMessage('PARTNER_URI_WIZARD_AFEX');

		$arModuleVersion = array();
		include(dirname(__FILE__) . '/version.php');

		if (isset($arModuleVersion) && is_array($arModuleVersion) && !empty($arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
	}

	/**
	 * @param array $arParams
	 *
	 * @return bool
	 */
	function InstallDB($arParams = array())
	{
		\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

		/*RegisterModuleDependences(
			"main",
			"OnBeforeProlog",
			$this->MODULE_ID,
			"\\Bitrix\\wizardUShop\\Events",
			"ShowPanel"
		);*/

		return true;
	}

	/**
	 * @param array $arParams
	 *
	 * @return bool
	 */
	function UnInstallDB($arParams = array())
	{
		/*UnRegisterModuleDependences(
			"main",
			"OnBeforeProlog",
			$this->MODULE_ID,
			"\\Bitrix\\wizardUShop\\Events",
			"ShowPanel"
		);*/

		\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

		return true;
	}

	/**
	 * @return bool
	 */
	function InstallEvents()
	{
		return true;
	}

	/**
	 * @return bool
	 */
	function UnInstallEvents()
	{
		return true;
	}

	/**
	 * @param array $arParams
	 *
	 * @return bool
	 */
	function InstallFiles($arParams = array())
	{
/*		global $server;
		if(!isset($server) || empty($server)) {
			$context = \Bitrix\Main\Application::getInstance()->getContext();
			$server = $context->getServer();
		}

		if($_ENV['COMPUTERNAME']!='BX')
		{
			if (is_dir($p = $server->getDocumentRoot().BX_ROOT.'/modules/'.$this->MODULE_ID.'/admin'))
			{
				if ($dir = opendir($p))
				{
					while (false !== $item = readdir($dir))
					{
						if ($item == '..' || $item == '.' || $item == 'menu.php')
							continue;
						file_put_contents($file = $server->getDocumentRoot().BX_ROOT
							.'/admin/'.$this->MODULE_ID.'_'.$item,
							'<'.'? require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/'
							.$this->MODULE_ID.'/admin/'.$item.'");?'.'>'
						);
					}
					closedir($dir);
				}
			}
		}
		CopyDirFiles($server->getDocumentRoot().BX_ROOT.'/modules/'.$this->MODULE_ID.'/install/components',
			$server->getDocumentRoot().BX_ROOT.'/components/'.$this->PARTNER_CODE, true, true);
		CopyDirFiles($server->getDocumentRoot().BX_ROOT.'/modules/'.$this->MODULE_ID
			.'/install/wizards/',
			$server->getDocumentRoot().BX_ROOT.'/wizards/', true, true);*/

		return true;
	}

	/**
	 *
	 */
	function UnInstallFiles()
	{
/*		global $server;
		if(!isset($server) || empty($server)) {
			$context = \Bitrix\Main\Application::getInstance()->getContext();
			$server = $context->getServer();
		}

		if (is_dir($p = $server->getDocumentRoot().BX_ROOT.'/modules/'.$this->MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($server->getDocumentRoot().BX_ROOT.'/admin/'.$this->MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		DeleteDirFiles($server->getDocumentRoot().BX_ROOT.'/modules/'
			.$this->MODULE_ID.'/install/components/',
			$server->getDocumentRoot().BX_ROOT.'/components/'.$this->PARTNER_CODE);
		DeleteDirFiles($server->getDocumentRoot().BX_ROOT.'/modules/'
			.$this->MODULE_ID.'/install/wizards/',
			$server->getDocumentRoot().BX_ROOT.'/wizards/');*/

		return true;
	}

	/**
	 *
	 */
	function DoInstall()
	{
		global $USER, $APPLICATION;

		if ($APPLICATION->GetGroupRight('main') < 'W' || !$USER->IsAdmin())
			return;

		if (!IsModuleInstalled($this->MODULE_ID))
		{
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
		}
	}

	/**
	 *
	 */
	function DoUninstall()
	{
		global $APPLICATION, $USER;

		if ($APPLICATION->GetGroupRight('main') < 'W' || !$USER->IsAdmin())
			return;

		if (IsModuleInstalled($this->MODULE_ID))
		{
			$this->UnInstallEvents();
			$this->UnInstallDB();
			$this->UnInstallFiles();
		}
	}
}
?>