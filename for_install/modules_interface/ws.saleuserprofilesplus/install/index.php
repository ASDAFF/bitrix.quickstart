<?php
use WS\SaleUserProfilesPlus\handlers\InsertToGlobalMenu;
use WS\SaleUserProfilesPlus\Module;

IncludeModuleLangFile(__FILE__);

class ws_saleuserprofilesplus extends CModule{
    const MODULE_ID = 'ws.saleuserprofilesplus';
    var $MODULE_ID = 'ws.saleuserprofilesplus';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;
    public $NEED_MAIN_VERSION = '12.0';
    public $NEED_MODULES = array('sale');
    public $MODULE_GROUP_RIGHTS = "Y";

    function __construct(){
        $arModuleVersion = array();

        require __DIR__ . "/../vendor/autoload.php";
        require __DIR__ . "/version.php";
        Module::get()->includeLangFile();

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Module::get()->getMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = Module::get()->getMessage("MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("ws.saleuserprofilesplus_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("ws.saleuserprofilesplus_PARTNER_URI");
    }

    function InstallDB($arParams = array()){
        RegisterModuleDependences('main', 'OnBuildGlobalMenu', Module::MODULE_ID, InsertToGlobalMenu::className(), 'process');
        return true;
    }

    function UnInstallDB($arParams = array()){
        UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', Module::MODULE_ID, InsertToGlobalMenu::className(), 'process');
        return true;
    }

    function InstallFiles($arParams = array()){
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.Module::MODULE_ID.'/admin')){
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || $item == 'menu.php') {
                        continue;
                    }
                    file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.Module::MODULE_ID.'_'.$item,
                        '<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.Module::MODULE_ID.'/admin/'.$item.'");?'.'>');
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.Module::MODULE_ID.'/install/components')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.') {
                        continue;
                    }
                    CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
                }
                closedir($dir);
            }
        }
        return true;
    }

    function UnInstallFiles(){
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.Module::MODULE_ID.'/admin'))
        {
            if ($dir = opendir($p))
            {
                while (false !== $item = readdir($dir))
                {
                    if ($item == '..' || $item == '.')
                        continue;
                    unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.Module::MODULE_ID.'_'.$item);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.Module::MODULE_ID.'/install/components'))
        {
            if ($dir = opendir($p))
            {
                while (false !== $item = readdir($dir))
                {
                    if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
                        continue;

                    $dir0 = opendir($p0);
                    while (false !== $item0 = readdir($dir0))
                    {
                        if ($item0 == '..' || $item0 == '.')
                            continue;
                        DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
                    }
                    closedir($dir0);
                }
                closedir($dir);
            }
        }
        return true;
    }

    function DoInstall(){
        if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES)){
            foreach ($this->NEED_MODULES as $module){
                if (!IsModuleInstalled($module)){
                    return false;
                }
            }
        }

        if (strlen($this->NEED_MAIN_VERSION)<=0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION)>=0){
            $this->InstallFiles();
            $this->InstallDB();
            RegisterModule(Module::MODULE_ID);
            return true;
        }

        return false;
    }

    function DoUninstall(){
        UnRegisterModule(Module::MODULE_ID);
        $this->UnInstallDB();
        $this->UnInstallFiles();
        return true;
    }

    function GetModuleRightList() {
        $arr = array(
            "reference_id" => array(
                "D",
                "R",
                "W"
            ),
            "reference" => array(
                Module::get()->getMessage('access_D'),
                Module::get()->getMessage('access_R'),
                Module::get()->getMessage('access_W'),
            )
        );
        return $arr;
    }
}
