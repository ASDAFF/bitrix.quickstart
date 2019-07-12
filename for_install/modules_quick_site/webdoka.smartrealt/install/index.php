<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

global $MESS;

IncludeModuleLangFile ( __FILE__ );

if (class_exists ( "webdoka_smartrealt" ))
    return;

class webdoka_smartrealt extends CModule {
    var $MODULE_ID = "webdoka.smartrealt";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";
    //public $AUTO_INSTALL_UNINSTALL = true;
    

    public function __construct() {
        $PathInstall = str_replace ( "\\", "/", __FILE__ );
        $PathInstall = substr ( $PathInstall, 0, strlen ( $PathInstall ) - strlen ( "/index.php" ) );
        include ($PathInstall . "/version.php");
        
        $this->MODULE_VERSION = SMARTREALT_VERSION;
        $this->MODULE_VERSION_DATE = SMARTREALT_VERSION_DATE;
        $this->MODULE_NAME = GetMessage ( 'SMARTREALT_MODULE_NAME' );
        $this->MODULE_DESCRIPTION = GetMessage ( 'SMARTREALT_MODULE_DESCRIPTION' );
        $this->PARTNER_NAME = "Webdoka";
        $this->PARTNER_URI = "http://www.webdoka.ru/";

    }
    
    /**
     * @desc установка файлов модуля
     */
    function InstallFiles() {
        // страницы админки
        CopyDirFiles ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/admin", true );
        // изображения
        CopyDirFiles ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/images", $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/images/" . $this->MODULE_ID, true, true );
        // темы (стили и иконки)
        CopyDirFiles ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/themes", $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/themes", true, true );
        // компоненты
        CopyDirFiles ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/components", true, true );
        // публичная часть
        //CopyDirFiles ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/public", $_SERVER ["DOCUMENT_ROOT"] . "/catalog", false, true );
        
        return true;
    }
    
    /**
     * @desc удаление файлов модуля
     */
    function UnInstallFiles() {
        DeleteDirFiles ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/admin" );
        DeleteDirFiles ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/themes/.default/", $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/themes/.default" ); //css
        DeleteDirFilesEx ( "/bitrix/themes/.default/icons/" . $this->MODULE_ID . "/" ); //icons
        DeleteDirFilesEx ( "/bitrix/images/" . $this->MODULE_ID . "/" ); //images    
        
        return true;
    }
    
    /**
     * @desc установка базы модуля
     */
    function InstallDB($install_wizard = true) {
        global $DB, $APPLICATION;
        
        
        $errors = $DB->RunSQLBatch ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/db/" . strtolower ( $DB->type ) . "/install.sql" );

        if (empty($errors))
        {          
            $sSql = 'SELECT * FROM smartrealt_rubric_group';
            $rs = $DB->Query ( $sSql );
            if ($rs->SelectedRowsCount () <= 0) {
                
                $errors = $this->RunSQLBatch ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/db/" . strtolower ( $DB->type ) . "/data.sql" );
            } 
        }
         
        if (! empty ( $errors )) {
            $APPLICATION->ThrowException ( implode ( "", $errors ) );
            return false;
        }
        
        return true;
    }
    
    /**
     * @desc удаление базы модуля
     */
    function UnInstallDB($sSaveData = 'N') {
        global $DB, $APPLICATION;
        
        if ($sSaveData != "Y") {
            $errors = $DB->RunSQLBatch ( $_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/db/" . strtolower ( $DB->type ) . "/uninstall.sql" );
            
            if (! empty ( $errors )) {
                $APPLICATION->ThrowException ( implode ( "", $errors ) );
                return false;
            }
        } else {
            return true;
        }
    }
    
    /**
     * @desc осуществляет инсталляцию модуля
     */
    function DoInstall() {
        global $DB, $APPLICATION, $step;
        
        $R_RIGHT = $APPLICATION->GetGroupRight ( $this->MODULE_ID );
        
        if ($R_RIGHT == "W") {
            $step = intval ( $step );
            
            /*if ($step < 2) {
                $APPLICATION->IncludeAdminFile ( GetMessage ( 'SMARTREALT_INSTALL_STEP1_TITLE' ), $_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/' . $this->MODULE_ID . '/install/step1.php' );
            } else*/ {
                // здесь обрабатываются дополнительные параметры которые могли быть установлены на первом шаге
                
                $this->InstallDB();
                RegisterModule ( $this->MODULE_ID );
                
                RegisterModuleDependences('search', 'OnReindex', $this->MODULE_ID, 'SmartRealt_Common', 'OnReindex');  
                
                if (!defined('BX_UTF') || !BX_UTF)  
                    $DB->Query("SET NAMES 'utf8'");
                
                if (CModule::IncludeModule ( $this->MODULE_ID )) {
                    //почтовые события
                    include ($_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/events/set_events.php");
                }
                
                if (!defined('BX_UTF') || !BX_UTF)  
                    $DB->Query("SET NAMES 'cp1251'");
                
                $this->InstallFiles();
                CAgent::AddAgent("SmartRealt_CatalogElement::LoadFromWebserviceAgent('', 0, 20);", $this->MODULE_ID, "Y", 3600, ConvertTimeStamp(time()+3600, "FULL"));
                CAgent::AddAgent("SmartRealt_CatalogElementPhoto::LoadFromWebserviceAgent('', 0, 2);", $this->MODULE_ID, "Y", 3600, ConvertTimeStamp(time()+3700, "FULL"));
                CAgent::AddAgent("SmartRealt_CatalogElementType::LoadFromWebserviceAgent('','','');", $this->MODULE_ID, "N", 86400, ConvertTimeStamp(time()+3800, "FULL"));
                COption::SetOptionString($this->MODULE_ID, "TOKEN", $_GET['Token']);
                
                $APPLICATION->IncludeAdminFile ( GetMessage ( 'SMARTREALT_INSTALL_STEP2_TITLE' ), $_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/' . $this->MODULE_ID . '/install/step2.php' );
            }
        }
    }
    
    /**
     * @desc осуществляет деинсталляцию модуля
     */
    function DoUninstall() {
        global $DB, $APPLICATION, $step;
        
        $R_RIGHT = $APPLICATION->GetGroupRight ( $this->MODULE_ID );
        
        if ($R_RIGHT == "W") {
            $step = intval ( $step );
            if ($step < 2) {
                $APPLICATION->IncludeAdminFile ( GetMessage ( "SMARTREALT_UNINSTALL_STEP1_TITLE" ), $_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/' . $this->MODULE_ID . '/install/unstep1.php' );
            } elseif ($step == 2) {
                $this->UnInstallDB ( $_REQUEST ['savedata'] );
                if (CModule::IncludeModule ( $this->MODULE_ID )) {
                    // Почтовые события
                    include ($_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/events/unset_events.php");
                }
                CAgent::RemoveModuleAgents( $this->MODULE_ID );
                UnRegisterModule ( $this->MODULE_ID );
                $this->UnInstallFiles ();
                
                $APPLICATION->IncludeAdminFile ( GetMessage ( "SMARTREALT_UNINSTALL_STEP2_TITLE" ), $_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/' . $this->MODULE_ID . '/install/unstep2.php' );
            }
        }
    }
    
    /**
     * @desc права пользователей модуля
     */
    function GetModuleRightList() {
        $arr = array ('reference_id' => array ('D', 'R', 'U', 'W' ), 'reference' => array ('[D] ' . GetMessage ( 'SMARTREALT_PERM_DENIED' ), '[R] ' . GetMessage ( 'SMARTREALT_PERM_READ' ), '[U] ' . GetMessage ( 'SMARTREALT_PERM_UPDATE' ), '[W] ' . GetMessage ( 'SMARTREALT_PERM_WRITE' ) ) );
        return $arr;
    }

    function DoInstallAuto() {
        global $DB, $APPLICATION;

        $R_RIGHT = $APPLICATION->GetGroupRight ( $this->MODULE_ID );

        if ($R_RIGHT == "W") {
            $this->InstallDB();
            RegisterModule ( $this->MODULE_ID );

            RegisterModuleDependences('search', 'OnReindex', $this->MODULE_ID, 'SmartRealt_Common', 'OnReindex');

            if (CModule::IncludeModule ( $this->MODULE_ID )) {
                //почтовые события
                include ($_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/events/set_events.php");
            }
            $this->InstallFiles();
            CAgent::RemoveModuleAgents( $this->MODULE_ID );
            CAgent::AddAgent("SmartRealt_CatalogElement::LoadFromWebserviceAgent('', 0, 20);", $this->MODULE_ID, "Y", 3600, ConvertTimeStamp(time()+3600, "FULL"));
            CAgent::AddAgent("SmartRealt_CatalogElementPhoto::LoadFromWebserviceAgent('', 0, 2);", $this->MODULE_ID, "Y", 3600, ConvertTimeStamp(time()+3700, "FULL"));
            CAgent::AddAgent("SmartRealt_CatalogElementType::LoadFromWebserviceAgent('','','');", $this->MODULE_ID, "N", 86400, ConvertTimeStamp(time()+3800, "FULL"));
            COption::SetOptionString($this->MODULE_ID, "TOKEN", '');
        }
    }  
    
    function RunSQLBatch($filepath, $bIncremental = False)
    {
        global $DB;
        
        if(!file_exists($filepath) || !is_file($filepath))
            return Array("File $filepath is not found.");

        $arErr = Array();
        $f = @fopen($filepath, "rb");
        if($f)
        {
            $contents = fread($f, filesize ($filepath));
            
            if (defined('BX_UTF')) 
            {
                $contents = iconv('WINDOWS-1251', 'UTF-8', $contents);
            }
            
            fclose($f);

            $arSql = $DB->ParseSqlBatch($contents, $bIncremental);
            //echo "<pre>"; print_r($arSql); echo "</pre>"; die();
            for($i=0; $i<count($arSql); $i++)
            {
                if ($bIncremental)
                {
                    $arErr[] = $arSql[$i];
                }
                else
                {
                    $strSql = str_replace("\r\n", "\n", $arSql[$i]);
                    if(!$DB->Query($strSql, true))
                        $arErr[] = "<hr><pre>Query:\n".$strSql."\n\nError:\n<font color=red>".$this->GetErrorMessage()."</font></pre>";
                }
            }
        }
        if(count($arErr)>0)
            return $arErr;

        return false;
    }  
}

?>
