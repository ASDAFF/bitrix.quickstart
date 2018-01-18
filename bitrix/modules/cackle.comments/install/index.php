<?
IncludeModuleLangFile(__FILE__);

Class cackle_comments extends CModule
{
    var $MODULE_ID = "cackle.comments";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION =  $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
		$this->PARTNER_NAME = "Cackle"; 
		$this->PARTNER_URI = "http://cackle.me";
        $this->MODULE_NAME = GetMessage('APP_PLATFORM_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('APP_PLATFORM_MODULE_DESCRIPTION');
    }
//
    function InstallDB()
    {
        RegisterModule($this->MODULE_ID);
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID . "/install/db/".strtolower($DB->type)."/install.sql");
        return true;
    }

    function UnInstallDB($arParams = array())
    {
        UnRegisterModule($this->MODULE_ID);
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID . "/install/db/".strtolower($DB->type)."/uninstall.sql");
        return true;
    }

    function InstallFiles()
    {

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/",
            true, true
        );

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/",
            true, true
        );

        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/components/cackle.comments/");
        return true;
    }

    function DoInstall()
    {
        global $USER, $APPLICATION;
        if(!$USER->IsAdmin())
            return;

        $this->InstallDB();
        $this->InstallFiles();

    }

    function DoUninstall(){
        global $USER, $DB, $APPLICATION, $step;
        if($USER->IsAdmin()){
        $this->UnInstallDB();
        $this->UnInstallFiles();
        $GLOBALS["errors"] = $this->errors;
        //UnRegisterModuleDependences("main", "OnBeforeProlog", "main", "", "","/modules/cackle/generate.php");
        //UnRegisterModuleDependences("main", "OnAfterEpilog", "main", "", "","/modules/cackle/flush.php");
        UnRegisterModule("$this->MODULE_ID");
         //       $APPLICATION->IncludeAdminFile(GetMessage("APP_PLATFORM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/cackle/install/unstep.php");

        }
    }
}
?>