<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));


Class epir_oldbrowser extends CModule
{

    var $MODULE_ID = "epir.oldbrowser";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    function epir_oldbrowser()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = GetMessage("OBM_INSTALL_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("OBM_INSTALL_DESCRIPTION");

        $this->PARTNER_NAME = GetMessage("OBM_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("OBM_PARTNER_URI");
    }

    function InstallDB($install_wizard = true)
    {
        RegisterModule($this->MODULE_ID);
        return true;
    }

    function UnInstallDB($arParams = Array())
    {
        UnRegisterModule($this->MODULE_ID);
        UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "oldbrowser_class", "oldbrowser_addScript");
        COption::RemoveOption($this->MODULE_ID);
        return true;

    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epir.oldbrowser/install/scripts/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/oldbrowser/", true, true);
        return true;
    }


    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/tools/oldbrowser/");
        return true;
    }


    function DoInstall()
    {
        $this->InstallDB(false);
        $this->InstallFiles();

        // для выбора браузеров
        $ie_option = array('ie6','ie7','ie8');
        $ie_val = array('Y','Y','Y');
        $ie_option_descr = array(GetMessage("OBM_TEXT_MSIE6"),GetMessage("OBM_TEXT_MSIE7"),GetMessage("OBM_TEXT_MSIE8"));

        $option_code = array("active_oldbrowser","string_1_oldbrowser","string_2_oldbrowser","string_3_oldbrowser","include_jquery");
        $option_val = array("Y",GetMessage("OBM_TEXT_VAL_1"),GetMessage("OBM_TEXT_VAL_2"),GetMessage("OBM_TEXT_VAL_3"),"N");
        $option_descr = array(GetMessage("OBM_ACTIVE"),GetMessage("OBM_TEXT_1"),GetMessage("OBM_TEXT_2"),GetMessage("OBM_TEXT_3"),GetMessage("OBM_JQUERY"));

        COption::SetOptionString($this->MODULE_ID, $option_code[0], $option_val[0], $option_descr[0]);

        COption::SetOptionString($this->MODULE_ID, $option_code[1], $option_val[1], $option_descr[1]);
        COption::SetOptionString($this->MODULE_ID, $option_code[2], $option_val[2], $option_descr[2]);
        COption::SetOptionString($this->MODULE_ID, $option_code[3], $option_val[3], $option_descr[3]);
        COption::SetOptionString($this->MODULE_ID, $option_code[4], $option_val[4], $option_descr[4]);
        // для браузеров
        COption::SetOptionString($this->MODULE_ID, $ie_option[0], $ie_val[0],$ie_option_descr[0]);
        COption::SetOptionString($this->MODULE_ID, $ie_option[1], $ie_val[1],$ie_option_descr[1]);
        COption::SetOptionString($this->MODULE_ID, $ie_option[2], $ie_val[2],$ie_option_descr[2]);

        RegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "oldbrowser_class", "oldbrowser_addScript");
        $GLOBALS["APPLICATION"]->IncludeAdminFile(GetMessage("OBM_INSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epir.oldbrowser/install/step.php");

    }

    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallFiles();
        UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "oldbrowser_class", "oldbrowser_addScript");
        COption::RemoveOption($this->MODULE_ID);
    }
}
?>
