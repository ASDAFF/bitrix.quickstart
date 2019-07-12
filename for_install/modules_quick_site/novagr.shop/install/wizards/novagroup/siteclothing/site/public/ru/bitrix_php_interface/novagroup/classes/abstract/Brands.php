<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 13.07.13
 * Time: 19:49
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_Brands extends Novagroup_Classes_Abstract_Catalog
{

    protected $catalogPath = "#SITE_DIR#brands/", $catalogName = "Бренды", $catalogIBlockCode = "vendor";

    function getCatalogPath()
    {
        return str_replace('#SITE_DIR#',SITE_DIR,$this->catalogPath);
    }

    function getCatalogName()
    {
        return $this->catalogName;
    }

    function getCatalogIBlockCode()
    {
        return $this->catalogIBlockCode;
    }

    function setPageProperties()
    {
        Novagroup_Classes_General_Main::setTitle($this->getCatalogName());
    }

    function addChainItems()
    {
        Novagroup_Classes_General_Main::AddChainItem($this->getCatalogName(), $this->getCatalogPath());
    }
}