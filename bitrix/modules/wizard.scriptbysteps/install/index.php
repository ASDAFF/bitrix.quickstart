<?php
/**
 * Created by PhpStorm.
 * User: Сергей
 * Date: 05.05.2015
 * Time: 15:55
 */

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if (class_exists('wizard_scriptbysteps'))
	return;

Class wizard_scriptbysteps extends CModule
{
	var $MODULE_ID = 'wizard.scriptbysteps';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $PARTNER_CODE = 'wizard';
	var $WIZARD_NAME = 'scriptbysteps';
	var $strError = '';

	/**
	 *
	 */
	function wizard_scriptbysteps()
	{
		$this->MODULE_NAME = Loc::getMessage('MODULE_NAME_WIZARD_SCRIPT_BY_STEPS');
		$this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_DESC_WIZARD_SCRIPT_BY_STEPS');

		$this->PARTNER_NAME = GetMessage('PARTNER_NAME_WIZARD_SCRIPT_BY_STEPS');
		$this->PARTNER_URI = GetMessage('PARTNER_URI_WIZARD_SCRIPT_BY_STEPS');

		$arModuleVersion = [];
		include(dirname(__FILE__) . '/version.php');

		if (isset($arModuleVersion) && is_array($arModuleVersion) && !empty($arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
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
			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
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
			\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
		}
	}
}