<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

// NOTE this file must compatible with php 5.3

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class rodzeta_redirect extends CModule {
	var $MODULE_ID = "rodzeta.redirect"; // NOTE using "var" for bitrix rules

	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_GROUP_RIGHTS;
	public $PARTNER_NAME;
	public $PARTNER_URI;

	//public $MODULE_GROUP_RIGHTS = 'N';
	//public $NEED_MAIN_VERSION = '';
	//public $NEED_MODULES = array();

	function __construct() {
		$this->MODULE_ID = "rodzeta.redirect"; // NOTE for showing module in /bitrix/admin/partner_modules.php?lang=ru

		$arModuleVersion = array();
		include __DIR__ . "/version.php";
		if (!empty($arModuleVersion["VERSION"])) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = Loc::getMessage("RODZETA_REDIRECT_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("RODZETA_REDIRECT_MODULE_DESCRIPTION");
		$this->MODULE_GROUP_RIGHTS = "N";

		$this->PARTNER_NAME = "Rodzeta";
		$this->PARTNER_URI = "http://rodzeta.ru/";
	}

	function InstallFiles() {
		return true;
	}

	function UninstallFiles() {
		//...
		return true;
	}

	function DoInstall() {
		global $APPLICATION;
		if (version_compare(PHP_VERSION, "7", "<")) {
 			$APPLICATION->ThrowException(Loc::getMessage("RODZETA_REQUIREMENTS_PHP_VERSION"));
 			return false;
 		}
 		if (!defined("BX_UTF")) {
 			$APPLICATION->ThrowException(Loc::getMessage("RODZETA_REQUIREMENTS_BITRIX_UTF8"));
 			return false;
 		}
		RegisterModule($this->MODULE_ID);
		RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
		$this->InstallFiles();
	}

	function DoUninstall() {
		$this->UninstallFiles();
		UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
		UnRegisterModule($this->MODULE_ID);
	}
}
