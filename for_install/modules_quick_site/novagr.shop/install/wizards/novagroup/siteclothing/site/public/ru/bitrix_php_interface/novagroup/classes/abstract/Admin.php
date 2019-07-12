<?php

abstract class Novagroup_Classes_Abstract_Admin
{
    protected $moduleID, $nameTemplate, $templatesPath;

    function __construct($adminFile)
    {
        $this->nameTemplate = basename($adminFile,".php");
        $this->templatesPath = dirname(__FILE__) . '/../../admin';

        $segments = explode(DIRECTORY_SEPARATOR, $adminFile);
        $this->setModuleID($segments[count($segments)-3]);
    }

    function setModuleID($moduleID=false)
    {
        if($moduleID<>false) $this->moduleID = $moduleID;
    }

    function getFilePath()
    {
        if (file_exists($template = $this->templatesPath . '/' . $this->moduleID . '/' . $this->nameTemplate . '.php')) {
            return $template;
        } elseif (file_exists($template = $this->templatesPath . '/abstract/' . $this->nameTemplate . '.php')) {
            return $template;
        }
        return false;
    }

    function getLangPath()
    {
        if (file_exists($template = $this->templatesPath . '/' . $this->moduleID . '/lang/' . $this->nameTemplate . '.php')) {
            return $template;
        } elseif (file_exists($template = $this->templatesPath . '/abstract/lang/' . $this->nameTemplate . '.php')) {
            return $template;
        }
        return false;
    }
}