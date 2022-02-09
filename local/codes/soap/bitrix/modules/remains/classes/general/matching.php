<?php

/*
    Сопоставления товаров
    Справочник, заполняемый автоматически Подгрузчиком остатков, содержит в себе
    следующий набор параметров:
    ● ID - чило, заполняется автоматически;
    ● ID товара - число, обязательное;
    ● Hash запись - строка, обязательное;
    ● Код поставщика - выбор из справочника “Поставщик”, обязательное;
    ● Оригинальное наименование - строка, обязательное;
 
  */

class matching extends mytpl{ 
    
    var $tablename = 'matching';
    
    static $suppliers_iblock_id = 4;
    static $store_iblock_id = 8;
    
    var $fields = array('ID'         =>  array(  'TYPE'  =>  'INT'    ),
                        'ITEM_ID'    =>  array(  'TYPE'  =>  'INT'    ),
                        'HASH'       =>  array(  'TYPE'  =>  'VARCHAR'),
                        'SUPPLIER_ID'=>  array(  'TYPE'  =>  'INT'    ),
                        'NAME'       =>  array(  'TYPE'  =>  'VARCHAR'));

    static function getStoreIdByCode($storeCode){
        CModule::IncludeModule('iblock');
        $res = CIBlockElement::GetList(array(),
                                        array("IBLOCK_ID" => self::$store_iblock_id,
                                              "PROPERTY_STORE_CODE" => $storeCode),
                                       false, false, array("ID", "IBLOCK_ID", "CODE"));
        if($ob = $res->GetNextElement()){
           $arFields = $ob->GetFields();
           return $arFields['ID']; 
        }
        return false;
    }
    
    static function getSupplierIdByCode($supplierCode){
        CModule::IncludeModule('iblock');
        $res = CIBlockElement::GetList(array(),   
                                       array("IBLOCK_ID" => self::$suppliers_iblock_id,
                                             "PROPERTY_SUPPLIER_CODE" => $supplierCode),
                                       false,
                                       false,
                                       array("ID",
                                             "IBLOCK_ID", 
                                             "CODE"));
        if($ob = $res->GetNextElement()){
           $arFields = $ob->GetFields();
           return $arFields['ID']; 
        }
        return false;
    }
              
    function Add($arr){
        global $DB;

        if(!is_numeric($arr['SUPPLIER_ID'])) 
            $arr['SUPPLIER_ID'] = $this->getSupplierIdByCode($arr['SUPPLIER_ID']);

        foreach ($this->fields as $fieldName => $fieldArr) {
            if($fieldArr['TYPE'] == 'INT'){
                $arr[$fieldName] = intval($arr[$fieldName]);
            }elseif($fieldArr['TYPE'] == 'VARCHAR'){
                $arr[$fieldName] = $DB->ForSql($arr[$fieldName]);  
            }        
        }
        
        if(!$arr['SUPPLIER_ID'])
            return false;
        
        $strSql = "INSERT INTO `" . $this->tablename . "` (
                  `ID` ,
                  `ITEM_ID` ,
                  `HASH` ,
                  `SUPPLIER_ID` ,
                  `NAME`)
                  VALUES (NULL, '{$arr['ITEM_ID']}', '{$arr['HASH']}',
                         '{$arr['SUPPLIER_ID']}', '{$arr['NAME']}');";

        $result = $DB->Query($strSql); 
        return intval($DB->LastID());
    }
    
    
    public function createTable(){
        global $DB; 
        $DB->Query("CREATE TABLE `" . $this->tablename . "` (
                   `ID` INT( 4 ) NOT NULL AUTO_INCREMENT ,
                   `ITEM_ID` INT( 4 ) NOT NULL ,
                   `HASH` VARCHAR( 255 ) NOT NULL ,
                   `SUPPLIER_ID` INT( 4 ) NOT NULL ,
                   `NAME` VARCHAR( 255 ) NOT NULL , PRIMARY KEY ( `ID` ) ) ENGINE = InnoDB;");
    }
    
    static function getHash($str){
        return md5($str);
    }
     
}
