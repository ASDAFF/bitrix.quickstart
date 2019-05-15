<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule( "acrit.exportpro" );

Loc::loadMessages( __FILE__ );

class AcritExportproSession{
    static private $sessionDir;
    static private $cronPage = -1;
    static public function Init( $cronpage ){
        if( !self::$sessionDir )
            self::$sessionDir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/acrit.exportpro/";
        
        self::$cronPage = $cronpage;
    }
    
    static public function GetAllSession( $id ){   
        $files = scandir( self::$sessionDir );
        $arSessionData = array();
        foreach( $files as $file ){
            if( $file == "." || $file == ".." )
                continue;
                
            if( false !== strpos( $file, "export_{$id}" ) ){
                $sessionData = file_get_contents( self::$sessionDir.$file );
                $sessionData = unserialize( $sessionData );
                if( !is_array( $sessionData ) )
                    $sessionData = array();
                    
                if( !empty( $sessionData ) )
                    $arSessionData[] = $sessionData;
            }
        }
        return array_filter( $arSessionData );
    }
    
    static public function GetSessionPage( $id, $page = null ){
        if( $page > 0 )
            $id .= "_".$page;
    
        if( file_exists( self::$sessionDir."export_{$id}.session" ) ){
            $sessionData = file_get_contents( self::$sessionDir."export_{$id}.session" );
            $sessionData = unserialize( $sessionData );
        }

        if( !is_array( $sessionData ) )
            $sessionData = array();
    
        return $sessionData;
    }
    
    static public function SetSessionPage( $id, $data, $page=null ){
        if( !is_array( $data ) )
            $data = array();
    
        if( $page > 0 )
            $id .= "_".$page;
    
        file_put_contents( self::$sessionDir."export_{$id}.session", serialize( $data ) );
    }
        
    static public function GetSession( $id ){
        if( self::$cronPage > 0 )
            $id .= "_".self::$cronPage;
        
        if( file_exists( self::$sessionDir."export_{$id}.session" ) ){
            $sessionData = file_get_contents( self::$sessionDir."export_{$id}.session" );
            $sessionData = unserialize( $sessionData );
        }
        
        if( !is_array( $sessionData ) )
            $sessionData = array();
            
        return $sessionData;
    }
    
    static public function SetSession( $id, $data ){
        if( !is_array( $data ) )
            $data = array();
        
        if( self::$cronPage > 0 )
            $id .= "_".self::$cronPage;
            
        file_put_contents( self::$sessionDir."export_{$id}.session", serialize( $data ) );
    }
    
    static public function DeleteSession( $id ){
        if( file_exists( self::$sessionDir."export_{$id}.session" ) )
            unlink( self::$sessionDir."export_{$id}.session" );
        
        $files = scandir( self::$sessionDir );
        foreach( $files as $file ){
            if( $file == "." || $file == ".." )
                continue;
            
            if( false !== strpos( $file, "export_{$id}" ) ){
                unlink( self::$sessionDir.$file );
            }
        }
    }
}