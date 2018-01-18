<?php
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall) - strlen('/index.php'));
IncludeModuleLangFile($PathInstall . '/install.php');
include($PathInstall . '/version.php');

if (class_exists('asd_usersonline'))
	return;

class asd_usersonline extends CModule {

	var $MODULE_ID = "asd.usersonline";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';

	function __construct() {
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path . '/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage('usersonline_partner_name');
		$this->PARTNER_URI = 'http://www.d-it.ru/solutions/components/usersonline/';

		$this->MODULE_NAME = GetMessage('usersonline_module_name');
		$this->MODULE_DESCRIPTION = GetMessage('usersonline_module_description');
	}

	function DoInstall() {
		if ($GLOBALS['APPLICATION']->GetGroupRight('main') >= 'W') {
			$GLOBALS['APPLICATION']->IncludeAdminFile(GetMessage('usersonline_install_title'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/asd.usersonline/install/step1.php');
		}
	}

	function DoUninstall() {
		if ($GLOBALS['APPLICATION']->GetGroupRight('main') >= 'W') {
			$GLOBALS['APPLICATION']->IncludeAdminFile(GetMessage('usersonline_uninstall_title'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/asd.usersonline/install/unstep1.php');
		}
	}

}