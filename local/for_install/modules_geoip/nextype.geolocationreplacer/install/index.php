<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

class nextype_geolocationreplacer extends CModule
{
    var $MODULE_ID = 'nextype.geolocationreplacer';
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;

    function nextype_geolocationreplacer()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = GetMessage('NT_GEOLOCATION_REPLACER_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('NT_GEOLOCATION_REPLACER_INSTALL_DESCRIPTION');
        $this->PARTNER_NAME = GetMessage("NT_GEOLOCATION_REPLACER_PARTNER");
        $this->PARTNER_URI = GetMessage("NT_GEOLOCATION_REPLACER_PARTNER_URI");
    }

    public function DoInstall(){

        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        RegisterModule($this->MODULE_ID);

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".$this->MODULE_ID."/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);

        LocalRedirect('/bitrix/admin/partner_modules.php?lang=ru&result=OK');

    }

    public function DoUninstall(){

        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        
        DeleteDirFilesEx("/bitrix/modules/".$this->MODULE_ID."/");
        DeleteDirFilesEx("/bitrix/components/nextype/geolocation.replacer/");
        
        UnRegisterModule($this->MODULE_ID);

    }
}
?>