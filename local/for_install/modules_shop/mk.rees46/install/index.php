<?php

IncludeModuleLangFile(__FILE__);

class mk_rees46 extends CModule
{
	const MODULE_ID = 'mk.rees46';

	const IMAGE_WIDTH_DEFAULT       = 150;
	const IMAGE_HEIGHT_DEFAULT      = 150;
	const RECOMMEND_COUNT_DEFAULT   = 10;

	var $MODULE_ID = "mk.rees46";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME = 'REES46';
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	public function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__ . '/version.php');
		$this->MODULE_VERSION       = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE  = $arModuleVersion['VERSION_DATE'];
		$this->PARTNER_NAME         = "REES46";
		$this->PARTNER_URI          = "http://rees46.com/";
		$this->MODULE_NAME          = GetMessage('REES_INSTALL_MODULE_NAME');
		$this->MODULE_DESCRIPTION   = GetMessage('REES_INSTALL_MODULE_DESC');
	}

	public function DoInstall()
	{
		global $APPLICATION;
		RegisterModule($this->MODULE_ID);
		$this->InstallFiles();
		$this->InstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage('REES_INSTALL_TITLE'), __DIR__ . '/step.php');
	}

	public function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule($this->MODULE_ID);
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage('REES_INSTALL_TITLE'), __DIR__ . '/unstep.php');
	}

	public function InstallFiles($arParams = array())
	{
		$result = true;
		$result = $result && CopyDirFiles(__DIR__ .'/components/rees46', $_SERVER['DOCUMENT_ROOT'] .'/bitrix/components/rees46', true, true);
		$result = $result && CopyDirFiles(__DIR__ .'/include', $_SERVER['DOCUMENT_ROOT'] .'/include', true, true);
		return $result;
	}

	public function UnInstallFiles()
	{
		$result = true;
		$result = $result && DeleteDirFilesEx('/bitrix/components/rees46');
		$result = $result && DeleteDirFilesEx('/include/rees46-handler.php');
		return $result;
	}

	public function InstallEvents()
	{
		// Track adding items to the cart
		RegisterModuleDependences('sale', 'OnBasketAdd',            self::MODULE_ID, 'Rees46\\Events', 'cart');
		// Track removing items from the cart
		// OnBeforeBasketDelete because we can't get product_id in OnBasketDelete
		RegisterModuleDependences('sale', 'OnBeforeBasketDelete',   self::MODULE_ID, 'Rees46\\Events', 'removeFromCart');
		// Track ordering
		// WARNING!!! NON-DOCUMENTED BITRIX EVENT!!!
		// We can't get items in OnOrderAdd
		RegisterModuleDependences('sale', 'OnBasketOrder',          self::MODULE_ID, 'Rees46\\Events', 'purchase');
	}

	public function UnInstallEvents()
	{
		UnRegisterModuleDependences('sale', 'OnBasketAdd',          self::MODULE_ID, 'Rees46\\Events', 'cart');
		UnRegisterModuleDependences('sale', 'OnBeforeBasketDelete', self::MODULE_ID, 'Rees46\\Events', 'removeFromCart');
		UnRegisterModuleDependences('sale', 'OnBasketOrder',        self::MODULE_ID, 'Rees46\\Events', 'purchase');
	}
}
