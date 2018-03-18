<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");
IncludeModuleLangFile(__FILE__);

Class yakus_yml extends CModule
{
    const MODULE_ID = 'yakus.yml';
    var $MODULE_ID = 'yakus.yml';
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
        $this->MODULE_NAME = GetMessage("yakus.yml_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("yakus.yml_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("yakus.yml_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("yakus.yml_PARTNER_URI");
    }

    function InstallDB($arParams = array())
    {
        global $DB;
        RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CYakusYml', 'OnBuildGlobalMenu');

        //$DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/yakus.yml/install/sql/sql_comments.sql");
        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB;
        UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CYakusYml', 'OnBuildGlobalMenu');

        //$DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/yakus.comments/install/sql/delete_sql_comments.sql");
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
        //CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/yakus.yml/js/', true, true);

        return true;
    }

    function UnInstallFiles()
    {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
        {
            if ($dir = opendir($p))
            {
                while (false !== $item = readdir($dir))
                {
                    if ($item == '..' || $item == '.')
                        continue;
                    unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
                }
                closedir($dir);
            }
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
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

        DeleteDirFilesEx('/bitrix/tools/'.self::MODULE_ID.'/');

        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;
        $this->InstallFiles();
        $this->InstallDB();
        RegisterModule(self::MODULE_ID);
    }

    function DoUninstall()
    {
        global $APPLICATION;
        UnRegisterModule(self::MODULE_ID);
        $this->UnInstallDB();
        $this->UnInstallFiles();
    }
}
?>
