<?php

global $MESS;
$strPath2Lang = str_replace( "\\", "/", __FILE__ );
$strPath2Lang = substr( $strPath2Lang, 0, strlen($strPath2Lang)-18 );
@include( GetLangFileName( $strPath2Lang."/lang/", "/install/index.php" ) );
IncludeModuleLangFile( $strPath2Lang."/install/index.php" );

class simai_maps2gis extends CModule
{
    const MODULE_ID = 'simai.maps2gis';
    var $MODULE_ID = 'simai.maps2gis';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'N';
    var $PARTNER_NAME;
    var $PARTNER_URI;
    // component name to install
    var $COMPONENT_NAME = 'maps.2gis.simple';
    
    
    function simai_maps2gis()
	{
		$arModuleVersion = array();

		$path = str_replace( "\\", "/", __FILE__ );
		$path = substr( $path, 0, strlen($path) - strlen("/index.php") );
		include( $path."/version.php" );

		$this->MODULE_VERSION      = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME        = GetMessage("SMM_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SMM_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = "SIMAI"; 
        $this->PARTNER_URI = "http://www.simai.ru";
	}
    
    
    public function DoInstall()
    {
        if( !IsModuleInstalled($this->MODULE_ID) )
        {
            if( !$this->InstallEvents() ) return false;
            if( !$this->InstallFiles() ) return false;
            if( !$this->InstallDB() ) return false;
            RegisterModule( $this->MODULE_ID );
        }
        return true;
    }
    
    
    public function DoUninstall()
    {
        if( !$this->UninstallEvents() ) return false;
        if( !$this->UninstallFiles() ) return false;
        if( !$this->UninstallDB() ) return false;
        UnRegisterModule( $this->MODULE_ID );
        return true;
    }
    
    
    
    public function InstallEvents()
	{
        // no events
		return true;
	}
    

	public function UnInstallEvents()
	{
        // no events
		return true;
	}
    
    
    public function InstallDB()
	{
        // no iblocks
		return true;
	}
    
    
    public function UninstallDB()
	{
        // no iblocks
		return true;
	}
    
    
    public function InstallFiles()
	{
        $install_basedir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/simai/';
        if( !is_dir( $install_basedir ) )
            mkdir( $install_basedir );
        if( !is_dir( $install_basedir.$this->COMPONENT_NAME ) )
            mkdir( $install_basedir.$this->COMPONENT_NAME );
        // copy component
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/simai.maps2gis/install/components/simai/'.$this->COMPONENT_NAME,
            $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/simai/'.$this->COMPONENT_NAME, true, true
        );
		return true;
	}
    
    
    public function UninstallFiles()
	{
        $absolute_basedir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/simai/'.$this->COMPONENT_NAME;
        $relative_basedir = '/bitrix/components/simai/'.$this->COMPONENT_NAME;
        if( is_dir( $absolute_basedir ) )
            DeleteDirFilesEx( $relative_basedir );
		return true;
	}
};

?>
