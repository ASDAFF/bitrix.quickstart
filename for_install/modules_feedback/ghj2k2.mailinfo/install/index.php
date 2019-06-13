<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));

Class ghj2k2_mailinfo extends CModule
{
  var $MODULE_ID = "ghj2k2.mailinfo";
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
      $this->MODULE_VERSION = $arModuleVersion["VERSION"];
      $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }
    else
    {
      $this->MODULE_VERSION = CURRENCY_VERSION;
      $this->MODULE_VERSION_DATE = CURRENCY_VERSION_DATE;
    }

    $this->PARTNER_NAME = "ghj2k2";
    $this->PARTNER_URI = "mailto:bitrixmodules@gmail.com";
    $this->MODULE_NAME = GetMessage("MAILINFO_INSTALL_NAME");
    $this->MODULE_DESCRIPTION = GetMessage("MAILINFO_INSTALL_DESCRIPTION");
  }

  function DoInstall()
  {
    global $APPLICATION;
    $this->InstallFiles();
    $this->InstallDB();
    $GLOBALS["errors"] = $this->errors;

    $APPLICATION->IncludeAdminFile(GetMessage("MAILINFO_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
  }

  function DoUninstall()
  {
    global $APPLICATION, $step;
    $step = IntVal($step);
    if($step<2)
    {
      $APPLICATION->IncludeAdminFile(GetMessage("MAILINFO_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
    }
    elseif($step==2)
    {
      $this->UnInstallDB(array(
        "savedata" => $_REQUEST["savedata"],
      ));
      $this->UnInstallFiles();
      
      $GLOBALS["errors"] = $this->errors;
      $APPLICATION->IncludeAdminFile(GetMessage("MAILINFO_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep2.php");
    }
  }
  
  function InstallDB()
  {
    
    global $DB, $DBType, $APPLICATION;
    $this->errors = false;
    RegisterModule($this->MODULE_ID);

    return true;
  }
  
  function UnInstallDB($arParams = array())
  {
    global $DB, $DBType, $APPLICATION;
    $this->errors = false;
    UnRegisterModule($this->MODULE_ID);

    return true;
  }


  function InstallFiles()
  {
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID."/", true, true);
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
 
    return true;
  }

  function UnInstallFiles()
  {
    DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
    DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
    DeleteDirFilesEx("/bitrix/themes/.default/icons/".$this->MODULE_ID."/");
    DeleteDirFilesEx("/bitrix/images/".$this->MODULE_ID."/");
    DeleteDirFilesEx("/bitrix/themes/.default/icons/".$this->MODULE_ID."/");

    return true;
  }
}
?>