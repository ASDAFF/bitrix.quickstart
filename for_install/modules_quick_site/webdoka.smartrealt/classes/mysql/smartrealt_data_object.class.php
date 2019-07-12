<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_DataObject
{
    /**
    * @desc массив полей
    */
    public $arFields = null;
    
    public $arSortFields = null;
    
    /**
    * @desc название таблицы
    */
    public $sTableName = null;
    
    /**
    * @desc поле первичного ключа
    */
    public $sPrimaryKeyName = null;
    
    /**
    * @desc поле названия/заголовка объекта
    */
    public $sTitleFieldName = null;
    
    /**
    * поле даты создания
    */
    public $sCreateDateField = null;
    
    /**
    * поле даты изменения
    */
    public $sUpdateDateField = null;
    
    /**
    * поле сортировки
    */
    public $sSortField = null;
    
    /**
    * поля по которым будет фильтроваться по условию "Найти"
    */
    public $arTextFilterFields = null;
    
    public function __construct()
    {
        if (is_null($this->arFields))
            $this->arFields = array();
        if (is_null($this->arSortFields))
            $this->arSortFields = array();
            
        if (is_null($this->arTextFilterFields))
            $this->arTextFilterFields = array();
        
        if (is_null($this->sTableName)) 
            $this->sTableName = '';
        
        if (is_null($this->sPrimaryKeyName)) 
            $this->sPrimaryKeyName = '';
        
        if (is_null($this->sTitleFieldName)) 
            $this->sTitleFieldName = '';   
        
        if (is_null($this->sCreateDateField)) 
            $this->sCreateDateField = '';
        
        if (is_null($this->sUpdateDateField)) 
            $this->sUpdateDateField = '';
        
        if (is_null($this->sSortField)) 
            $this->sSortField = '';
    }
    
    /**
    * исходя из массива фильтра формирует строку LIMIT для sql-запроса
    */
    protected function GetLimitSql($arFilter)
    {
        $sLimit = '';
        if (isset($arFilter['Limit']))
        {
            if (is_array($arFilter['Limit']) && count($arFilter['Limit'])>0 && count($arFilter['Limit'])<=2)
            {
                $sLimit = ' LIMIT ' . intval($arFilter['Limit'][0]);
                if (count($arFilter['Limit'])==2)
                    $sLimit .= ', ' . intval($arFilter['Limit'][1]);    
            }
            else
            {
                if (intval($arFilter['Limit']) > 0)
                    $sLimit = ' LIMIT ' . intval($arFilter['Limit']);
            }
        }    
        return $sLimit;
    }
    
    /**
    * формирует строку ORDER для sql-запроса
    */
    protected function GetSortSql($arSort)
    {                     
        
        $sSort = '';
        if ((!is_array($arSort) || count($arSort)==0) && $this->sSortField)
        {
            $arSort[$this->sSortField] = 'asc';
        }
        if (!isset($arSort[$this->sTitleFieldName]))
            $arSort[$this->sTitleFieldName] = 'asc';
        
        if (is_array($arSort) && count($arSort)>0)
        {                 
            foreach($arSort as $key=>$val)
            {
                if ((in_array($key, $this->arFields) || in_array($key, $this->arSortFields))
                    && in_array(strtolower($val), array('asc', 'desc')))
                    $sSort .= "T." . addslashes($key).' '.addslashes($val).', ';
                else if ('rand' == strtolower($key))
                     $sSort .= 'rand asc, ';
            }
            
            if (strlen($sSort) > 0)
            {
                $sSort = " ORDER BY ".$sSort;
                $sSort = substr($sSort, 0, strlen($sSort)-2);
            }
        }
        return $sSort;
    }
    
    /**
    * формирует строку GROUP для sql-запроса
    */
    protected function GetGroupSql($arGroup)
    {
        $sGroup = '';

        if (!isset($arSort[$this->sTitleFieldName]))
            $arSort[$this->sTitleFieldName] = 'asc';
        if (is_array($arGroup) && count($arGroup)>0)
        {                 
            foreach($arGroup as $field)
            {
                if (in_array($field, $this->arFields))
                    $sGroup .= "T." . addslashes($field).', ';
            }
            
            if (strlen($sGroup) > 0)
            {
                $sGroup = " GROUP BY ".$sGroup;
                $sGroup = substr($sGroup, 0, strlen($sGroup)-2);
            }
        }
        return $sGroup;
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
          
        $sSql = "SELECT *" . $sSqlForSelect . ", RAND() as rand
                FROM " . $this->sTableName . " T
                " . $sJoin . "
                WHERE " . $sWhrSql .                                                      
                $sGroup. $sSort . $sLimit;
        //echo $sSql;
        $rs = $DB->Query($sSql, false, 'FILE: '.__FILE__.'<br>LINE: ' . __LINE__);
        return $rs;
    }
    
    /**
    * @desc Получение количества записей
    * @param array массив фильтра
    * @return int количество записей
    */
    public function GetCount($arFilter = array(), $arSort = array(), $arGroup = array())
    {
        global $DB;
        
        $iCount = 0;
        $sWhrSql = $this->FilterToWhere($arFilter);

        $sSqlForSelect = '';
          
        $sSql = "SELECT COUNT(*) as iCount
                FROM " . $this->sTableName . " T 
                WHERE " . $sWhrSql ;
        //echo $sSql;
        $rs = $DB->Query($sSql, false, 'FILE: '.__FILE__.'<br>LINE: ' . __LINE__);
        if ($ar = $rs->Fetch())
        {
            $iCount = $ar['iCount'];
        }
        
        return $iCount;
    }
    
    /**
    * @desc формирует строку WHERE на основе массива фильтра
    * @param array массив фильтра
    * @return string строка WHERE
    * 
    * фильтрует по:
    *     - FindText, т.е. по полям определенным в arTextFilterFields
    *   - по полям arFields с возможностью задания оператора проверки перед названием поля (=, !=, <>, <, и т.д.)
    *   - по границам дат создания и редактирования
    */
    public function FilterToWhere ($arFilter=array(), $sWhr = '')
    {
        if (strlen($sWhr)>0)
            $whrSQL = $sWhr;
        else
            $whrSQL = '1 ';
        if (is_array($arFilter) && count($arFilter) > 0)
        {
            foreach($arFilter as $key=>$val)
            {
                switch ($key)
                {
                    case $this->sCreateDateField.'From':
                    case $this->sCreateDateField.'To':
                        $val = SmartRealt_Common::DateToDB($val);
                        if ($key==$this->sCreateDateField.'To' && strpos(' ', $val)===false)
                            $val .= ' 23:59:59';
                        $sOp = ($key==$this->sCreateDateField.'To') ? '<=' : '>=';
                        $whrSQL .= ' AND T.'.$this->sCreateDateField.$sOp."'".$val."'";
                        break;
                    case $this->sUpdateDateField.'From':
                    case $this->sUpdateDateField.'To':
                        $val = SmartRealt_Common::DateToDB($val);
                        if ($key==$this->sUpdateDateField.'To' && strpos(' ', $val)===false)
                            $val .= ' 23:59:59';
                        $sOp = ($key==$this->sUpdateDateField.'To') ? '<=' : '>=';
                        $whrSQL .= ' AND T.'.$this->sUpdateDateField.$sOp."'".$val."'";
                        break;
                    case 'FindText':
                        if (strlen($val)>0)
                        {
                            if (is_array($this->arTextFilterFields) && count($this->arTextFilterFields)>0)
                            {
                                $arF = $this->arTextFilterFields;
                            }
                            else
                            {
                                foreach ($this->arFields as $sField)
                                {
                                    if ($sField!=$this->sPrimaryKeyName)
                                        $arF[] = $sField;
                                }
                            }
                            $arWhere = array();
                            foreach ($arF as $sField)
                            {
                                $arWhere[] = "(T.".$sField." LIKE '%".$val."%')";
                            }
                            if (count($arWhere)>0)
                                $whrSQL .= ' AND ('.implode(' OR ', $arWhere).') ';
                        }   
                        break;
                        
                    default:  
                        $sOp = '';
                        if (in_array(substr($key,0,1),array('!','=','>','<')))
                            $sOp .= substr($key,0,1);
                        if (substr($key,1,1) == '=')
                            $sOp .= '='; 
                         
                        $key = substr($key,strlen($sOp));
   
                        if (!in_array($key,$this->arFields))
                            continue;
                            
                        $key = addslashes($key);
                        $value = '';
                        if (is_array($val) && count($val) > 0)
                        {
                            $sOperator = 'IN';
                            if ($sOp=='!' || $sOp=='!=')
                                $sOperator = 'NOT IN';
                            foreach ($val as $v)
                                $value .= ", '".addslashes($v)."'";
                            $value = " ".$sOperator." (".substr($value, 2).")";
                        }
                        elseif (strlen($val) > 0)
                        {
                            $sOperator = '=';
                                             
                            if (strlen($sOp)>0)
                            {                                    
                                if ($sOp=='!' || $sOp=='!=')
                                    $sOperator = '<>';
                                else
                                    $sOperator = $sOp;
                            }                      
                            $value = $sOperator . " '".addslashes($val) . "'";
                        }

                        if (strlen($value) > 0)
                        {      
                            $whrSQL .= " AND T." . $key . ' ' . $value;      
                        }  
                        break;
                }
            }
        }
        //echo $whrSQL;
        return $whrSQL;
    }
    
    /**
    * @desc добавление или обновление
    * @param array массив полей
    * @param string идентификатор существующего объекта, если редактирование
    * @param boolean игнорировать ошибки
    * @return CDBResult результат запроса
    */
    public function Add($arFields = array(), $ID = false, $bIgnoreErrors = false)
    {
        global $DB;
        $ID = trim($ID);            
        if (is_array($arFields) && count($arFields) > 0)
        {
            //unset($arFields[$this->sPrimaryKeyName]);
            $arFields = $this->PrepareFields($arFields);
                                      
            $sCurDateTime = date('Y-m-d H:i:s');   
            if ($this->sUpdateDateField && !isset($arFields[$this->sUpdateDateField]))
                $arFields[$this->sUpdateDateField] = "'".$sCurDateTime."'";                         
            if (strlen($ID) > 0)
            {    
                $sWhere = 'WHERE ' . $this->sPrimaryKeyName . '=\'' . $ID . '\'';
                                         
                $iAffectedRows = $this->Update($this->sTableName, $arFields, $sWhere, 'FILE: '.__FILE__.'<br>LINE: '.__LINE__,false,$bIgnoreErrors);
                
                if ($iAffectedRows == 1)
                {
                    return $ID;
                }
                else 
                {
                    return false;
                }
            }
            else 
            {                                                    
                if ($this->sCreateDateField)
                    $arFields[$this->sCreateDateField] = "'".$sCurDateTime."'";
                                    
                $res = $DB->Insert($this->sTableName, $arFields, 'FILE: '.__FILE__.'<br>LINE: '.__LINE__, false, false, $bIgnoreErrors);
                if ($res!==false)
                    return $arFields[$this->sPrimaryKeyName]?$arFields[$this->sPrimaryKeyName]:$DB->LastID();
                else
                    return false;
            }
        }
    }
    
    private function Update($table, $arFields, $WHERE="", $error_position="", $DEBUG=false, $ignore_errors=false)
    {
        global $DB;
        
        $rows = 0;
        if(is_array($arFields))
        {
            $str = "";
            foreach($arFields as $field => $value)
            {
                if (strlen($value)<=0)
                    $str .= ", `".$field."` = ''";
                else
                    $str .= ",  `".$field."` = ".$value;
            }
            $str = substr($str, 2);
            $strSql = "UPDATE ".$table." SET ".$str." ".$WHERE;
            if ($DEBUG) echo "<br>".$strSql."<br>";
            $w = $DB->Query($strSql, $ignore_errors, $error_position);
            $rows = $w->AffectedRowsCount();
        }
        return $rows;
    }
    
    /**
    * @desc подготовка данных для записи в базу
    * @param array массив полей
    * @return array обработанный массив
    */
    public function PrepareFields($arFields, $bSetNullIfEmpty=false)
    {    
        $arPreparedFields = array();
        
        foreach ($this->arFields as $sField)
        {
            $sValue = $arFields[$sField];

            switch ($sField)
            {
                case $this->sSortField:
                    if (strlen($sValue)<=0 || intval($sValue)<0)
                        $sValue = 100;
                    break;
            }
            if (strlen($sValue)==0)
            {
                if ($bSetNullIfEmpty)
                    $arPreparedFields[$sField] = "NULL";
                else
                    unset($arPreparedFields[$sField]);
            }
            else if (strtoupper($sValue)!='NULL')
                $arPreparedFields[$sField] = "'".addslashes($sValue)."'";
            else
                $arPreparedFields[$sField] = "";
        }

        return $arPreparedFields;
    }
    
    /**
    * @desc проверка полей 
    * @param array массив полей
    * @return array массив ошибок
    */
    public function CheckFields($arFileds)
    {
        return array();
    }
    
    /**
    * копирование объекта
    */
    public function CopyObject($ID, $sNewName = '#NAME#_copy')
    {
        $arFilter = array($this->sPrimaryKeyName=>$ID);
        $rsObj = $this->GetList($arFilter);
        if ($arObj = $rsObj->GetNext())
        {
            unset($arObj[$this->sPrimaryKeyName]);
            $arObj[$this->sTitleFieldName] = str_replace('#NAME#', $arObj[$this->sTitleFieldName], $sNewName);
            $sNewID = $this->Add($arObj);
            return $sNewID;
        }
        return false;
    }
    
    /**
    * @desc проверка возможности удаления
    * @param integer идентфикатор записи
    * @return boolean true если можно удалить
    */
    public function IsDelete($ID)
    {
        return true;
    }
    
    /**
    * @desc Удаление
    * @param integer идентификтаор записи
    * @param boolean проверить на возможность удаления
    */
    public function Delete($ID, $bCheck = false)
    {                               
        global $DB;
        $ID = trim($ID);
        if ($bCheck)
        {
            if (!$this->IsDelete($ID))
                return false;
        }
        $sql = 'DELETE FROM ' . $this->sTableName . ' WHERE ' . $this->sPrimaryKeyName . ' = \'' . $ID . '\'';

        $rs = $DB->Query($sql, false, 'FILE: '.__FILE__.'<br>LINE: ' . __LINE__);
        return $rs;
    }
    
    public function DeleteAll($bCheck = false)
    {                               
        global $DB;
        $ID = trim($ID);
        if ($bCheck)
        {
            if (!$this->IsDelete($ID))
                return false;
        }
        $sql = 'DELETE FROM ' . $this->sTableName . ' WHERE ' . $this->sPrimaryKeyName . ' = \'' . $ID . '\'';

        $rs = $DB->Query($sql, false, 'FILE: '.__FILE__.'<br>LINE: ' . __LINE__);
        return $rs;
    }
    
    public function GetByID($ID)
    {
        $rsObject = $this->GetList(array($this->sPrimaryKeyName => $ID));
        if (!$rsObject || $rsObject->SelectedRowsCount()!=1)
            return false;
        return $rsObject->GetNext();
    }   
    
    public function TruncateTable()
    {
        global $DB;

        $DB->Query('TRUNCATE TABLE ' . $this->sTableName, false, 'FILE: ' . __FILE__ . ';<br />LINE: ' . __LINE__);
    }
    
}

?>