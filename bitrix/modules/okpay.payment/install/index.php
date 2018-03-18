<?
IncludeModuleLangFile(__FILE__);
Class okpay_payment extends CModule
{
	const MODULE_ID = 'okpay.payment';
	var $MODULE_ID = 'okpay.payment'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct() {
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("okpay.payment_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("okpay.payment_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("okpay.payment_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("okpay.payment_PARTNER_URI");
	}

	function InstallDB($arParams = array()) {
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'COKPayPayment', 'OnBuildGlobalMenu');
		return true;
	}

	function UnInstallDB($arParams = array()) {
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'COKPayPayment', 'OnBuildGlobalMenu');
		return true;
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
		# /bitrix/modules/sale/payment/ okpay
		# /bitrix/components/ okpay/sale.payment.ipn
		//if ( !is_dir($payment = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/payment/okpay') ) {
		//	mkdir($payment, 0755);
		//}
		//if ( !is_dir($components = $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/okpay') ) {
		//	mkdir($components, 0755);
		//}
		if (is_dir($source = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install')) {
			$this->copyDir( $source."/components", $_SERVER['DOCUMENT_ROOT'].'/bitrix/components');
			$this->copyDir( $source."/payment", $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/payment');
		}
		return true;
	}

	function UnInstallFiles() {
		$this->rmFolder($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/okpay');
		$this->rmFolder($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/payment/okpay');
		return true;
	}

	function DoInstall() {
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall() {
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
