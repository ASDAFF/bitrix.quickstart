<?
IncludeModuleLangFile(__FILE__);
Class itconstruct_uncachedarea extends CModule
{
    const MODULE_ID = 'itconstruct.uncachedarea';
    var $MODULE_ID = 'itconstruct.uncachedarea'; 
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__)."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("itconstruct.uncachedarea_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("itconstruct.uncachedarea_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("itconstruct.uncachedarea_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("itconstruct.uncachedarea_PARTNER_URI");
    }

    function InstallDB($arParams = array())
    {
        RegisterModuleDependences('main', 'OnBeforeProlog', self::MODULE_ID, 'itc\CUncachedArea', 'onBeforeProlog');
        RegisterModuleDependences('main', 'OnEndBufferContent', self::MODULE_ID, 'itc\CUncachedArea', 'processAreas');
        return true;
    }

    function UnInstallDB($arParams = array())
    {
        UnRegisterModuleDependences('main', 'OnBeforeProlog', self::MODULE_ID, 'itc\CUncachedArea', 'onBeforeProlog');
        UnRegisterModuleDependences('main', 'OnEndBufferContent', self::MODULE_ID, 'itc\CUncachedArea', 'processAreas');
        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallFiles($arParams = array())
    {
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/js/' . self::MODULE_ID, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . self::MODULE_ID, $ReWrite = true, $Recursive = true);
        return true;
    }

    function UnInstallFiles()
    {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/js')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item)) {
                        continue;
                    }

                    $dir0 = opendir($p0);

                    while (false !== $item0 = readdir($dir0)) {
                        if ($item0 == '..' || $item0 == '.') {
                            continue;
                        }

                        DeleteDirFilesEx('/bitrix/js/'.$item.'/'.$item0);
                    }

                    closedir($dir0);
                }

                closedir($dir);
            }
        }

        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;
        $this->InstallFiles();
        $this->InstallDB();
        RegisterModule(self::MODULE_ID);
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION;
        UnRegisterModule(self::MODULE_ID);
        $this->UnInstallDB();
        $this->UnInstallFiles();
        return true;
    }
}
?>
