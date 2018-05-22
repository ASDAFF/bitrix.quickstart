<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule( "acrit.exportpro" );

Loc::loadMessages( __FILE__ );

class CAcritExportproUrlRewrite{
    private static $__instance = array();
    private $__siteId = false;
    private $__urlRewrite = array();
    
    private function __construct( $siteId ){
        $this->__siteId = $siteId;

        foreach( Bitrix\Main\UrlRewriter::getList( $this->__siteId ) as $rule ){
            if( !strlen( $rule["ID"] ) ){
                $rule["ID"] = 0;
            }
            $this->__urlRewrite[$rule["ID"]][] = $rule;
        }
    } 

    public static function getInstance( $siteId = false ){
        $siteId = $siteId ? $siteId : SITE_ID;

        if( !array_key_exists( $siteId, static::$__instance ) )
            static::$__instance[$siteId] = new self( $siteId );

        return static::$__instance[$siteId];
    } 


    public function getRuleByComponentId( $componentId ){
        $componentId = trim( $componentId );

        if( !array_key_exists( $componentId, $this->__urlRewrite ) )
            return false;

        foreach( $this->__urlRewrite[$componentId] as $rule ){
            $status = $this->__checkRule( $rule );

            if( $status !== false )
                return $rule;
        }

        return false;
    }
    
    public function getUrlRewrite(){
        return $this->__urlRewrite;
    }

    private function __checkRule( $rule, $requestUri = false ){
        $requestUri = $requestUri ? $requestUri : $_SERVER["REQUEST_URI"];

        if( preg_match( $rule["CONDITION"], $requestUri ) ){
            if( strlen( $rule["RULE"] ) > 0 )
                $url = preg_replace( $rule["CONDITION"], ( ( strlen( $rule["PATH"] ) > 0 ) ? $rule["PATH"]."?" : "" ).$rule["RULE"], $requestUri );
            else
                $url = $rule["PATH"];

            if( ( $pos = strpos( $url, "?" ) ) !== false )
                $url = substr( $url, 0, $pos );

            $url = Bitrix\Main\IO\Path::normalize( $url );

            if( !file_exists( $_SERVER["DOCUMENT_ROOT"].$url ) )
                return false;

            if( Bitrix\Main\IO\Path::getExtension( $url ) != "php" )
                return false;

            return $rule;
        }

        return false;
    } 
}