<?
IncludeModuleLangFile(__FILE__);
if (class_exists("cosmos_urlrewrite")){
    return;
}

Class cosmos_urlrewrite extends CModule {

    var $MODULE_ID = "cosmos.urlrewrite";
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "N";


    public function cosmos_urlrewrite(){
        
        include(dirname( __FILE__ ) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage( strtoupper( $this->MODULE_ID ) . "_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage( strtoupper( $this->MODULE_ID ) . "_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = "Cosmos-Web";
        $this->PARTNER_URI = "http://www.cosmos-web.ru";

    }


    public function DoInstall() {
        global $DB, $USER, $step, $APPLICATION;

        if ($USER->IsAdmin()){
            
            RegisterModuleDependences( "main", "OnAfterEpilog", $this->MODULE_ID, 'Cosmos\Urlrewrite\Urlrewrite', "OnAfterEpilog" );
            RegisterModule( $this->MODULE_ID );
            
//            CModule::IncludeModule( $this->MODULE_ID );
            
            $APPLICATION->IncludeAdminFile( GetMessage( strtoupper( $this->MODULE_ID ) . "_INSTALL_TITLE" ), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/step1.php" );
        }
        
    }



    public function DoUninstall() {
        global $DB, $APPLICATION, $step;
        
        UnRegisterModuleDependences( "main", "OnAfterEpilog", $this->MODULE_ID, 'Cosmos\Urlrewrite\Urlrewrite', "OnAfterEpilog" );
        UnRegisterModule( $this->MODULE_ID );

        $APPLICATION->IncludeAdminFile( GetMessage( strtoupper( $this->MODULE_ID ) . "_UNINSTALL_TITLE" ), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep1.php" );
        UnRegisterModule("dv_module");
    }

}
