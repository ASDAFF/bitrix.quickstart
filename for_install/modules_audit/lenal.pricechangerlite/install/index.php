<?

IncludeModuleLangFile(__FILE__);

Class lenal_pricechangerlite extends CModule {

    var $MODULE_ID = "lenal.pricechangerlite";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function lenal_pricechangerlite() {

        $this->MODULE_ID = "lenal.pricechangerlite";
        $this->MODULE_NAME = GetMessage("LL_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("LL_MODULE_DESCR");
        $this->PARTNER_NAME = 'LENAL';
        $this->PARTNER_URI = "http://lenal.biz";
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
    }

    function DoInstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        // Install events
        //CopyDirFiles($DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $DOCUMENT_ROOT . "/bitrix/components/lenal", true, true);
        CopyDirFiles($DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/ajax/", $DOCUMENT_ROOT . "/bitrix/js/" . $this->MODULE_ID .'/', true, true);

        RegisterModule($this->MODULE_ID);

        copy($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/lenal.pricechangerlite/admin/lenal_pricechanger_lite.php', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin/lenal_pricechanger_lite.php');
        
        $APPLICATION->IncludeAdminFile(GetMessage("LL_INSTALL") . ' ' . $this->MODULE_ID, $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/step.php");
        return true;
    }

    function DoUninstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage("LL_REMOVE") . ' ' . $this->MODULE_ID, $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep.php");
        unlink($_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin/lenal_pricechanger.php');
        return true;
    }

}

?>
