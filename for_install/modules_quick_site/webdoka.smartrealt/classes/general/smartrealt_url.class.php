<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_Url
{
    public function Parse(&$arVariables, $sSEFFolder = '')
    {
        if (strlen($sSEFFolder) == 0)
            $sSEFFolder = COption::GetOptionString('webdoka.smartrealt', 'SEF_FOLDER');
        
        $arDefaultUrlTemplates = array(
            "list" => COption::GetOptionString('webdoka.smartrealt', 'CATALOG_LIST_URL', SMARTREALT_CATALOG_LIST_URL_DEF),
            "element" => COption::GetOptionString('webdoka.smartrealt', 'CATALOG_DETAIL_URL', SMARTREALT_CATALOG_DETAIL_URL_DEF),
        );
        
        $arVariables = array();

        $componentPage = CComponentEngine::ParseComponentPath(
                $sSEFFolder,
                $arDefaultUrlTemplates,
                $arVariables
            );
        
        return $componentPage;
    }
    
}
?>