<?php

class yandexProxy {
 
    static $module_id = 'yandexparser';

    static function save($proxyList) {

        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::$module_id . "/proxy_data/proxy.dat", $proxyList);
    }

    static function open() {

        $proxyList = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::$module_id . "/proxy_data/proxy.dat");

        return $proxyList;
    }
 
    
    static function block($proxy){
        
        
    }
    
    static function getBlockedList(){
        
        return false;
        
    }
    
    
}
