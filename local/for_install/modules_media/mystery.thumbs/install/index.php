<?
global $DOCUMENT_ROOT, $MESS;
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/mystery.thumbs/prolog.php');
IncludeModuleLangFile ( __FILE__ );
if (class_exists ( 'mystery_thumbs' )) {
    return;
}

Class mystery_thumbs extends CModule {

    var $MODULE_ID = "mystery.thumbs";

    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function mystery_thumbs () {
        $arModuleVersion = array ();

        $path = str_replace ( "\\",
                              "/",
                              __FILE__
        );
        $path = substr ( $path,
                         0,
                         strlen ( $path ) - strlen ( "/index.php" )
        );
        include($path."/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->PARTNER_NAME = GetMessage('MYSTERY_THUMBS_PARTNER_NAME');
        $this->PARTNER_URI = "http://www.1c-bitrix.ru/partners/144409.php";

        $this->MODULE_NAME = GetMessage ( "MYSTERY_THUMBS_FORM_MODULE_NAME" );
        $this->MODULE_DESCRIPTION = GetMessage ( "MYSTERY_THUMBS_FORM_MODULE_DESCRIPTION" );
    }

    function InstallDB () {
        CUrlRewriter::Add ( array (
                                  "SITE_ID"   => NS_SITE_ID_ISNALL,
                                  "CONDITION" => MYSTERY_THUMBS_URLREWRITER_CONDITION,
                                  "ID"        => '',
                                  "PATH"      => MYSTERY_THUMBS_URLREWRITER_FILE_PATH,
                                  "RULE"      => ''
                            )
        );
        RegisterModule ( $this->MODULE_ID );
        return true;
    }

    function UnInstallDB () {
        CUrlRewriter::Delete ( array (
                                     "SITE_ID"   => NS_SITE_ID_ISNALL,
                                     "CONDITION" => MYSTERY_THUMBS_URLREWRITER_CONDITION
                               )
        );

        COption::RemoveOption ( $this->MODULE_ID );

        UnRegisterModule ( $this->MODULE_ID );
        return true;
    }

    function InstallEvents () {
        return true;
    }

    function UnInstallEvents () {
        return true;
    }

    function InstallFiles () {

        $rez = CopyDirFiles ( $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/images',
                              $_SERVER["DOCUMENT_ROOT"].'/bitrix/images/'.$this->MODULE_ID,
                              true
        );

        $rez = CopyDirFiles ( $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/root',
                              $_SERVER["DOCUMENT_ROOT"],
                              true
        );

        return true;
    }

    function UnInstallFiles () {
        DeleteDirFiles ( $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/images/',
                         $_SERVER["DOCUMENT_ROOT"].'/bitrix/images/'.$this->MODULE_ID
        );

        DeleteDirFiles ( $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/root/',
                         $_SERVER["DOCUMENT_ROOT"]
        );

        DeleteDirFilesEx ( '/upload/resize_cache/mystery.thumbs/' ); // old thumb images
        DeleteDirFilesEx ( '/thumb/' ); // thumb images (from v1.0.1)
        return true;
    }

    function DoInstall () {
        global $APPLICATION, $errors;
        $errors = false;
        if (!IsModuleInstalled ( $this->MODULE_ID )) {
            $errors = false;
            $step = IntVal ( $_REQUEST["step"] );
            if ($step != 2) {
                $APPLICATION->IncludeAdminFile ( GetMessage ( "MYSTERY_THUMBS_INSTALL_TITLE" ),
                                                 $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/step1.php'
                );
            } else {
                define('NS_SITE_ID_ISNALL', $_REQUEST["SITE_ID"]);

                $this->InstallDB ();
                $this->InstallFiles ();
                $this->InstallEvents ();
                $APPLICATION->IncludeAdminFile ( GetMessage ( "MYSTERY_THUMBS_INSTALL_TITLE" ),
                                                 $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/step2.php'
                );
            }
        }
    }

    function DoUninstall () {
        global $APPLICATION, $errors;
        $errors = false;
        $this->UnInstallDB ();
        $this->UnInstallEvents ();
        $this->UnInstallFiles ();
        $APPLICATION->IncludeAdminFile ( GetMessage ( "MYSTERY_THUMBS_FORM_UNINSTALL_TITLE" ),
                                         $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/unstep2.php'
        );
    }
}

?>