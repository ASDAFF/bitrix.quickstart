<?
IncludeModuleLangFile(__FILE__);
Class eoplatonov_changepass extends CModule{
    var $MODULE_ID = "eoplatonov.changepass";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function eoplatonov_changepass(){
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = GetMessage("CHPASS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("CHPASS_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage("CHPASS_MODULE_PARTNER_NAME");
        $this->PARTNER_URI="http://dev.1c-bitrix.ru/community/forums/forum14/topic85387/";
    }
    function DoInstall(){
        global $DOCUMENT_ROOT, $APPLICATION;
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/eoplatonov.changepass/install/components",
                     $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        RegisterModule("eoplatonov.changepass");
    }
    function DoUninstall(){
        DeleteDirFilesEx("/bitrix/components/eoplatonov");
        UnRegisterModule("eoplatonov.changepass");
    }
}
?>