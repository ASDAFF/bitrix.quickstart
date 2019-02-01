<?
/**
 * Module Install/Uninstall script
 */
global $MESS;
IncludeModuleLangFile(__FILE__);
if (class_exists('bagmet_menu'))
    return;
class bagmet_menu extends CModule
{

    var $MODULE_ID = 'bagmet.menu';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = 'N';
    var $PARTNER_NAME;
    var $PARTNER_URI;
    
    function bagmet_menu()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage('MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('MODULE_DESCRIPTION');
		$this->PARTNER_NAME = GetMessage('MODULE_PARTNER_NAME');
		$this->PARTNER_URI = GetMessage('MODULE_PARTNER_URI');
    }

	function InstallComponent() {
		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/bagmet.menu/install/components',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/components',
			true, true
		);
	}

    function DoInstall() {
		global $APPLICATION, $step;
		$this->InstallComponent();
		RegisterModule($this->MODULE_ID);
		$APPLICATION->IncludeAdminFile(
			GetMessage('MODULE_INSTALL_TITLE'), 
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/bagmet.menu/install/step.php'
		);
    }

    function DoUninstall() {
		global $APPLICATION, $step;
        $this->UnInstallComponent();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(
			GetMessage('MODULE_UNINSTALL_TITLE'), 
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/bagmet.menu/install/unstep.php'
		);
    }
    

	
	function UnInstallComponent() {
		return true;
	}

}
?>
