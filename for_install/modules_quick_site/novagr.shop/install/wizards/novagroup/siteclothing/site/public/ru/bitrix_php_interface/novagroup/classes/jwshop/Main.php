<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 13.07.13
 * Time: 19:49
 * To change this template use File | Settings | File Templates.
 */

class Novagroup_Classes_General_Main extends Novagroup_Classes_Abstract_Main {

    static public function seoUrlsFileUpdate($id) {
        global $CACHE_MANAGER;
        //сбрасываем кэш
        $CACHE_MANAGER->ClearByTag("seo_id_".$id);

        self::updateSeoUrls();
    }

    static public function getVersion()
    {
        if(defined('NOVAGROUP_GET_VERSION_MODULE'))
        {
            $version = NOVAGROUP_GET_VERSION_MODULE;
        } else {

            //if exists novagr.jwshop
            if (file_exists($includeFile = NOVAGR_JSWSHOP_MODULE_DIR . '/install/index.php')) {
                include($includeFile); $module = new novagr_jwshop(); if($module->IsInstalled()) $version = $module->MODULE_VERSION;
            }
            //if nothing
            else
                $version = "0.0.0";
            //set current version
            define('NOVAGROUP_GET_VERSION_MODULE',$version);
        }
        return $version;
    }

    static public function getView($component, $template, $params = array())
    {
        $includeFile = "/local".DIRECTORY_SEPARATOR."php_interface".DIRECTORY_SEPARATOR."novagroup".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.$component.DIRECTORY_SEPARATOR.$template.".php";
        self::includeView($includeFile, $params);
    }
}
