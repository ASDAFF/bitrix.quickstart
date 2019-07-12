<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_Rubric extends SmartRealt_DataObject
{
    public function __construct()
    {
        $this->sTableName = 'smartrealt_rubric';
        $this->sPrimaryKeyName = 'Id';
        $this->sTitleFieldName = 'Name';       
        $this->sCreateDateField = 'CreateDate';
        $this->sUpdateDateField = 'UpdateDate';
        $this->sSortField = 'Sort';
        //$this->sVersionFieldName = 'RubricVersion';
        $this->arFields = array(
            'Id',                
            'Active',
            'Name',      
            'RubricGroupId',        
            'TypeName',      
            'TypeId',      
            'SectionId',      
            'EstateMarket',      
            'TransactionType',      
            'Code',      
            'PageTitle',
            'Description',
            'Sort',
            'CreateDate',
            'UpdateDate',
        );
        $this->arTextFilterFields = array('Name', 'TypeName', 'PageTitle', 'Description');
        $this->sActiveFieldId = 'Active';
        $this->arRelationFields = array(                     
        );
        $this->arConstFields = array('Active','Sort', 'TypeName','Code', 'RubricGroupId');
    }
    
    /**
    * @desc Получение списка
    * @param array массив фильтра
    * @param array массив сортировки
    * @return CDBResult результат выполнения запроса
    */
    public function GetList($arFilter = array(), $arSort = array(), $arGroup = array())
    {
        global $DB;
        
        $sWhrSql = $this->FilterToWhere($arFilter);       
        
        $sLimit = $this->GetLimitSql($arFilter);
        
        $sGroup = $this->GetGroupSql($arGroup);
                                                
        
        $sSort = $this->GetSortSql($arSort);
        
        $sSqlForSelect = '';
        if (in_array($this->sPrimaryKeyName,$this->arFields) && in_array($this->sTitleFieldName,$this->arFields))
        {
            $sSqlForSelect = ", T." . $this->sPrimaryKeyName . " as REFERENCE_ID, T." . $this->sTitleFieldName . " as REFERENCE";        
        }
        
        $oRubricGroup = new SmartRealt_RubricGroup();
          
        $sSql = "SELECT
                    T.*
                    " . $sSqlForSelect . ",
                    RG.Code AS RubricGroupCode
                FROM " . $this->sTableName . " T
                LEFT JOIN ". $oRubricGroup->sTableName ." RG ON (RG.Id=T.RubricGroupId)
                WHERE RG.Active = 'Y' AND " . $sWhrSql .                                                      
                $sGroup. $sSort . $sLimit;
        //echo $sSql;
        $rs = $DB->Query($sSql, false, 'FILE: '.__FILE__.'<br>LINE: ' . __LINE__);
        return $rs;
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
                if (strlen($sVal)<=0 && count($sVal)<=0)
                    continue;
                
                if (!is_array($sVal))
                    $sVal = addslashes($sVal);
            
                switch ($sField)
                {                  
                    case 'Name':
                        $whrSQL .= " AND T.Name LIKE '$sVal'";
                        break;      
                    case 'RubricGroupCode':
                        $whrSQL .= " AND RG.Code = '$sVal'";
                        break;                  
                    case 'TypeId':
                        $whrSQL .= " AND (T.TypeId = '$sVal' OR T.TypeId LIKE '%;$sVal;%' OR T.TypeId LIKE '$sVal;%' OR T.TypeId LIKE '%;$sVal')";
                        break;                 
                    case 'SectionId':
                        $whrSQL .= " AND (T.SectionId = '$sVal' OR T.SectionId = '' OR T.SectionId IS NULL)";
                        break;                
                    case 'EstateMarket':
                        $whrSQL .= " AND (T.EstateMarket = '$sVal' OR T.EstateMarket = '' OR T.EstateMarket IS NULL)";
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

        //Не используется
        /*if (strlen($arFields['TypeName'])==0)
        {
            $oResult->AddMessage(
                array(
                    'id' => 'TypeName',
                    'text' => GetMessage('B_UPDATE_FIELD_EMPTY', array('#FIELD_NAME#' => GetMessage('B_TypeName')))
                )
            );
        }*/ 
        
        if (strlen($arFields['RubricGroupId'])==0 || $arFields['RubricGroupId']=="NOT_REF")
        {
            $oResult->AddMessage(
                array(
                    'id' => 'RubricGroupId',
                    'text' => GetMessage('B_UPDATE_FIELD_EMPTY', array('#FIELD_NAME#' => GetMessage('B_RubricGroupId')))
                )
            );
        } 
        
        if (strlen($arFields['TypeId'])==0 || $arFields['TypeId']=="NOT_REF")
        {
            $oResult->AddMessage(
                array(
                    'id' => 'TypeId[]',
                    'text' => GetMessage('B_UPDATE_FIELD_EMPTY', array('#FIELD_NAME#' => GetMessage('B_TypeId')))
                )
            );
        }
        
        /*if (strlen($arFields['TransactionType'])==0)
        {
            $oResult->AddMessage(
                array(
                    'id' => 'TransactionType',
                    'text' => GetMessage('B_UPDATE_FIELD_EMPTY', array('#FIELD_NAME#' => GetMessage('B_TransactionType')))
                )
            );
        }*/
        
        if (strlen($arFields['Code'])==0)
        {
            $oResult->AddMessage(
                array(
                    'id' => 'Code',
                    'text' => GetMessage('B_UPDATE_FIELD_EMPTY', array('#FIELD_NAME#' => GetMessage('B_Code')))
                )
            );
        }

        if (strlen($arFields['Code']) > 0 && !empty($arFields['TypeId']))
        {
            $iCount = $this->GetCount(array(
                '!=Id' => $arFields['id'],
                'RubricGroupId' => $arFields['RubricGroupId'],
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
    
    public function GetTypeIdByCode($sCode)
    {
        if (strlen($sCode) > 0)
        {
            $rs = $this->GetList(array('Code' => $sCode));
            
            if ($ar = $rs->Fetch())
            {
                return $ar['TypeId'];
            }
        }
    }
    
    public function GetTypeCodeById($sTypeId)
    {
        if (strlen($sTypeId) > 0)
        {
            $rs = $this->GetList(array('TypeId' => $sTypeId));
            
            if ($ar = $rs->Fetch())
            {
                return $ar['Code'];
            }
        }
            
    }
}
?>
