<?
global $MESS;
include(dirname(__FILE__) . '/version.php');
__IncludeLang(realpath(dirname(__FILE__).'/../lang/'.LANGUAGE_ID.'.php'));

class ws_projectsettings extends CModule {
    const MODULE_ID = "ws.projectsettings";
    var $MODULE_ID = "ws.projectsettings";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_GROUP_RIGHTS = 'N';
    public $NEED_MAIN_VERSION = '11.5';

    public function __construct() {
        $arModuleVersion = array();
        include(dirname(__FILE__). '/version.php');
        $this->MODULE_ID = self::MODULE_ID;
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->PARTNER_NAME = GetMessage("ws_partner_name");
        $this->PARTNER_URI  = 'http://www.worksolutions.ru/';
        $this->MODULE_NAME        = GetMessage('ws_module_name');
        $this->MODULE_DESCRIPTION = GetMessage('ws_module_description');
    }

    public function DoInstall() {
        RegisterModule(self::MODULE_ID);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".self::MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".self::MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
    }

    public function DoUninstall() {
        UnRegisterModule(self::MODULE_ID);
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".self::MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".self::MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/");
    }

    public function GetModuleRightList() {
        return array(
            'reference_id' => array('D', 'W'),
            'reference' => array(GetMessage('ws_access_deny'), GetMessage('ws_access_full'))
        );
    }
}
