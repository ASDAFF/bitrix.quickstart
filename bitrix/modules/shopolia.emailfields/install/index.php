<?
/**
 * ������ shopolia.emailfields (���������� ����� ������ � ����������� �������� ������� 1�-�������)
 */


global $MESS, $DB;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class shopolia_emailfields extends CModule {
	var $MODULE_ID = "shopolia.emailfields";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	// �������� ������
	function shopolia_emailfields () {
        global $APPLICATION;
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		} else {
			$this->MODULE_VERSION = SHOPOLIA_EMAILFIELDS_VERSION;
			$this->MODULE_VERSION_DATE = SHOPOLIA_EMAILFIELDS_VERSION_DATE;
		}

        $this->MODULE_NAME = GetMessage("SHOPOLIA_EMAILFIELDS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("SHOPOLIA_EMAILFIELDS_SHOP_MODULE_DESC");
		$this->PARTNER_NAME = "Shopolia.com"; 
		$this->PARTNER_URI = "http://shopolia.com"; 
	}

	// ���������� ������������ SQL-��������, �������� � ������ �������
	function InstallDB($arParams = array()) {
		RegisterModule("shopolia.emailfields");
        RegisterModuleDependences("main", "OnBeforeEventAdd", "shopolia.emailfields", "CShopoliaEmailFieldsHandlers", "OnBeforeEventAdd", 9999);
		return true;
	}

	// �������� ���� ������ � ������������ �� ������ �������
	function UnInstallDB($arParams = array()) {       
        UnRegisterModuleDependences("main", "OnBeforeEventAdd", "shopolia.emailfields", "CShopoliaEmailFieldsHandlers", "OnBeforeEventAdd");
		COption::RemoveOption("shopolia.emailfields"); // ������� ��� ���������
		UnRegisterModule("shopolia.emailfields");
		return true;
	}
	// ���������, ����������� ����� ����� ������� ��������� ������
	function DoInstall() {
		global $DOCUMENT_ROOT, $APPLICATION, $MESS;
		$step = intval($_POST['step']);
        $this->InstallDB(); // ������ ����
        $GLOBALS["errors"] = $this->errors;
        $APPLICATION->IncludeAdminFile(GetMessage("SHOPOLIA_EMAILFIELDS_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/shopolia.emailfields/install/install_ready.php");
	}

	// ���������, ����������� ����� ����� ������� ������������� ������
	function DoUninstall() {
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage("SHOPOLIA_EMAILFIELDS_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/shopolia.emailfields/install/uninstall_ready.php");
	}
}
?>
