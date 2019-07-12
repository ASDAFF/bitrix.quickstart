<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_Options
{
    private static $sPhotoFolder = null;
    private static $sSEFFolder = null;
    private static $sListUrl = null;
    private static $sDetailUrl = null;
    private static $sToken = null;
    private static $sWebServiceUrl = null;
    private static $sMapType = null;
    private static $bShowEmptyParameters = null;
    
    public static function GetSEFFolder()
    {
        if (!self::$sSEFFolder)
        {
            self::$sSEFFolder = COption::GetOptionString('webdoka.smartrealt', 'SEF_FOLDER', '/');
        }
        
        return self::$sSEFFolder;
    }
    
    public static function GetPhotoFolder()
    {
        if (!self::$sPhotoFolder)
        {
            self::$sPhotoFolder = COption::GetOptionString('webdoka.smartrealt', 'PHOTO_FOLDER');
        }
        
        return self::$sPhotoFolder;
    }
    
    public static function GetDetailUrl()
    {
        if (!self::$sDetailUrl)
        {
            self::$sDetailUrl = COption::GetOptionString('webdoka.smartrealt', 'CATALOG_DETAIL_URL');
        }
        
        return self::$sDetailUrl;
    }
    
    public static function GetListUrl()
    {
        if (!self::$sListUrl)
        {
            self::$sListUrl = COption::GetOptionString('webdoka.smartrealt', 'CATALOG_LIST_URL');
        }
        
        return self::$sListUrl;
    }
    
    public static function GetToken()
    {
        if (!self::$sToken)
        {
            self::$sToken = COption::GetOptionString('webdoka.smartrealt', 'TOKEN');
        }
        
        return self::$sToken;
    }
    
    public static function GetWebServiceUrl()
    {
        if (!self::$sWebServiceUrl)
        {
            self::$sWebServiceUrl = COption::GetOptionString('webdoka.smartrealt', 'WEB_SERVICE_URL');
        }
        
        return self::$sWebServiceUrl;
    }
    
    public static function GetMapType()
    {
        if (!self::$sMapType)
        {
            self::$sMapType = COption::GetOptionString('webdoka.smartrealt', 'MAP_TYPE');
        }
        
        return self::$sMapType;
    }

    public static function GetShowEmptyParameters()
    {
        if (!self::$bShowEmptyParameters)
        {
            self::$bShowEmptyParameters = COption::GetOptionString('webdoka.smartrealt', 'SHOW_EMPTY_PARAMETERS');
        }

        return self::$bShowEmptyParameters;
    }
    
    public static function IsTokenDemo()
    {
        return SmartRealt_Common::IsTokenDemo(self::GetToken());        
    }
    
    public static function IsTokenEmpty()
    {
        return SmartRealt_Common::IsTokenEmpty(self::GetToken());        
    }

    public static function Clear()
    {
        self::$sPhotoFolder = null;
        self::$sSEFFolder = null;
        self::$sListUrl = null;
        self::$sDetailUrl = null;
        self::$sToken = null;
        self::$sWebServiceUrl = null;
    }
    
}
?>
