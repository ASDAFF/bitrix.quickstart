<?
/**
 * Модуль shopolia.emailfields (Добавление полей заказа в стандартные почтовые события 1С-Битрикс)
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

	// Описание модуля
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

	// Выполнение установочных SQL-запросов, привязка к другим модулям
	function InstallDB($arParams = array()) {
		RegisterModule("shopolia.emailfields");
        RegisterModuleDependences("main", "OnBeforeEventAdd", "shopolia.emailfields", "CShopoliaEmailFieldsHandlers", "OnBeforeEventAdd", 9999);
		return true;
	}

	// Удаление базы данных и зависимостей от других модулей
	function UnInstallDB($arParams = array()) {       
        UnRegisterModuleDependences("main", "OnBeforeEventAdd", "shopolia.emailfields", "CShopoliaEmailFieldsHandlers", "OnBeforeEventAdd");
		COption::RemoveOption("shopolia.emailfields"); // удаляем все настройки
		UnRegisterModule("shopolia.emailfields");
		return true;
	}
	// Процедуры, выполняемые сразу после запуска установки модуля
	function DoInstall() {
		global $DOCUMENT_ROOT, $APPLICATION, $MESS;
		$step = intval($_POST['step']);
        $this->InstallDB(); // ставим базу
        $GLOBALS["errors"] = $this->errors;
        $APPLICATION->IncludeAdminFile(GetMessage("SHOPOLIA_EMAILFIELDS_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/shopolia.emailfields/install/install_ready.php");
	}

	// Процедуры, выполняемые сразу после запуска деинсталляции модуля
	function DoUninstall() {
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage("SHOPOLIA_EMAILFIELDS_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/shopolia.emailfields/install/uninstall_ready.php");
	}
}
?>
