<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_RubricGroup extends SmartRealt_DataObject
{
    public function __construct()
    {
        $this->sTableName = 'smartrealt_rubric_group';
        $this->sPrimaryKeyName = 'Id';
        $this->sTitleFieldName = 'Name';       
        $this->sCreateDateField = 'CreateDate';
        $this->sUpdateDateField = 'UpdateDate';
        $this->sSortField = 'Sort';
        $this->arFields = array(
            'Id',                
            'Active',
            'Name',      
            'Code',      
            'Sort',
            'CreateDate',
            'UpdateDate',
        );
        $this->arTextFilterFields = array('Name');
        /*$this->arBindFields = array(
            'RubricExternalId' => 'RubricExternalId',
            'RubricName' => 'RubricName',   
        );*/
        $this->sActiveFieldId = 'Active';
        $this->arRelationFields = array(                     
        );
    }  
    
    public function FilterToWhere($arFilter = array(), $sWhr = '')
    {
        // обрабатываются специфичные фильтры
        if (strlen($sWhr)>0)
            $whrSQL = $sWhr;
        else
            $whrSQL = '1 ';
        $arNewFilter = array();    
        if (is_array($arFilter) && count($arFilter) > 0)
        {
            foreach ($arFilter as $sField=>$sVal)
            {
                if (strlen($sVal)<=0)
                    continue;
                switch ($sField)
                {                  
                    case 'Name':
                        $whrSQL .= " AND Name LIKE '$sVal'";
                        break;
                        
                    default:
                        $arNewFilter[$sField] = $sVal;
                        break;        
                }
            }
        }                             
        return parent::FilterToWhere($arNewFilter, $whrSQL);
    } 
    
    function CheckFields($arFields)
    {
        $oResult = new CAdminException();
        
        if (strlen($arFields['Name']) == 0)
        {
            $oResult->AddMessage(
                array(
                    'id' => 'Name',
                    'text' => GetMessage('B_UPDATE_FIELD_EMPTY', array('#FIELD_NAME#' => GetMessage('B_NAME')))
                )
            );
        }
        
        if (strlen($arFields['Code']) == 0)
        {
            $oResult->AddMessage(
                array(
                    'id' => 'Code',
                    'text' => GetMessage('B_UPDATE_FIELD_EMPTY', array('#FIELD_NAME#' => GetMessage('B_Code')))
                )
            );
        }
        else
        {
            $iCount = $this->GetCount(array(
                '!=Id' => $arFields['id'],
                'Code' => $arFields['Code'],
            ));          

            if ($iCount > 0)
            {
                $oResult->AddMessage(
                    array(
                        'id' => '',
                        'text' => GetMessage('B_CODE_ALREADY_EXISTS')
                    )
                );
            }
        } 
        
        return $oResult;
    }                  
}
?>