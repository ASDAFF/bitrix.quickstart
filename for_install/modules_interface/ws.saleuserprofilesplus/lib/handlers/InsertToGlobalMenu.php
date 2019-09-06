<?php
/**
 * @author Smotrov Dmitriy <dsxack@gmail.com>
 */

namespace WS\SaleUserProfilesPlus\handlers;


use WS\SaleUserProfilesPlus\Module;
use WS\SaleUserProfilesPlus\Object;

class InsertToGlobalMenu extends Object{
    static public function process() {
        if (file_exists($path = Module::get()->getModuleDir() . '/admin')) {
            if ($dir = opendir($path)) {
                while(false !== $item = readdir($dir)) {
                    if (in_array($item,array('.','..','menu.php'))) {
                        continue;
                    }

                    if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.Module::MODULE_ID.'_'.$item)) {
                        file_put_contents($file,'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.Module::MODULE_ID.'/admin/'.$item.'");?'.'>');
                    }
                }
            }
        }
    }
} 