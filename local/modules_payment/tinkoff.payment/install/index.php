<?php

IncludeModuleLangFile(__FILE__);

Class tinkoff_payment extends CModule
{
	const MODULE_ID = 'tinkoff.payment';
	var $MODULE_ID = 'tinkoff.payment';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	var $strError = '';

	function __construct() {
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("tinkoff_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("tinkoff_MODULE_DESC");   
    $this->PARTNER_NAME = "Тинькофф Банк";
    $this->PARTNER_URI = "https://www.tinkoff.ru";
	}

	function InstallEvents() {
		return true;
	}

	function UnInstallEvents() {
		return true;
	}

	function rmFolder($dir) {
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file)){
				$this->rmFolder($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dir);

		return true;
	}

	function copyDir( $source, $destination ) {
		if ( is_dir( $source ) ) {
			@mkdir( $destination, 0755 );
			$directory = dir( $source );
			while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
				if ( $readdirectory == '.' || $readdirectory == '..' ) continue;
				$PathDir = $source . '/' . $readdirectory; 
				if ( is_dir( $PathDir ) ) {
					$this->copyDir( $PathDir, $destination . '/' . $readdirectory );
					continue;
				}
			copy( $PathDir, $destination . '/' . $readdirectory );
			}
			$directory->close();
		} else {
			copy( $source, $destination );
		}
	}

	function InstallFiles($arParams = array()) {
		if ( !is_dir($ipn_dir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment/') ) {
			mkdir($ipn_dir, 0755);
		}
		if (is_dir($source = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install')) {
			$this->copyDir( $source."/sale_payment", $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/payment');
			$this->copyDir( $source."/sale_payment", $ipn_dir);
			copy( $source."/notifications/notification.php", $_SERVER['DOCUMENT_ROOT'].'/personal/order/notification.php');
            copy( $source."/notifications/success.php", $_SERVER['DOCUMENT_ROOT'].'/personal/order/success.php');
            copy( $source."/notifications/failed.php", $_SERVER['DOCUMENT_ROOT'].'/personal/order/failed.php');
		}
		return true;
	}

	function UnInstallFiles() {
		$this->rmFolder($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/payment/tinkoff');
		$this->rmFolder($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment/tinkoff');
		return true;
	}

	function DoInstall() {
		global $APPLICATION;

		$this->InstallFiles();

		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall() {
		global $APPLICATION;

		UnRegisterModule(self::MODULE_ID);

		$this->UnInstallFiles();
	}
}
?>
