<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_CatalogElementType extends SmartRealt_WebServiceDataObject
{     
    const TYPE_NAME = __CLASS__;
    
    public function __construct()
    {
        $this->sTableName = 'smartrealt_catalog_element_type';
        $this->sWebServiceMethodName  = 'GetObjectsTypes';
        $this->sPrimaryKeyName = 'Id';
        /*$this->sCreateDateField = 'CreateDate';
        $this->sUpdateDateField = 'UpdateDate';*/
        $this->arFields = array( 
            'Id',
            'Name'
        );
        $this->arTextFilterFields = array('Name');
    }
    
    protected function OnAfterWebServiceDataLoad($arData)
    {
        $arResult = array();
        
        if (count($arData) > 0)
            $this->TruncateTable();
        
        return parent::OnAfterWebServiceDataLoad($arData);
    }  
}
?>
