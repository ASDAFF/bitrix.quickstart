<?php

/*  Товар - Наличие
    Сущность, заполняемая как вручную через систему управления, так и через Подгрузчик
    остатков содержит в себе следующий набор параметров:
    ● ID - чило, заполняется автоматически;
    ● ID Товара - выбор из сущности “Товар”, обязательное;
    ● ID Сопоставления товара - выбор из справочника “Сопоставление товара”, обязательное;
    ● Код склада– выбор из справочника «Пункты самовывоза», обязательное;
    ● Наличие товара - Выбор между “Есть”, “Нет”, обязательное;
    ● Код поставщика - выбор из справочника “Поставщик”, обязательное;
    ● Дата последнего обновления - заполняется автоматически парсером остатков; */

class availability extends mytpl{
 
    
     public function Update($id, $arr, $flag) { 
         if(!$id)
             return;
         
         
         parent::Update($id, $arr); 
         
         
         if(!$flag){
         global $DB;
         $DB->Query(
             " UPDATE `". $this->tablename ."` 
                 SET `DATE` = NOW( ) 
                 WHERE `ID` =" . $id);
                 ;
         }
     }
    
    public function Add($arr){
        global $DB; 
        foreach ($this->fields as $fieldName => $fieldArr) {
            if($fieldArr['TYPE'] == 'INT'){
                $arr[$fieldName] = intval($arr[$fieldName]);
            }elseif($fieldArr['TYPE'] == 'VARCHAR'){
                $arr[$fieldName] = $DB->ForSql($arr[$fieldName]);  
            }        
        }
        
        if(!$arr['STORE_ID'])
            return false; 
        
        $strSql = "INSERT INTO `" . $this->tablename . "` (
                  `ID` , `ITEM_ID` ,  `MATCHING_ID` ,  `STORE_ID` ,
                  `AVIABLE` ,  `SUPPLIER_ID` ,  `DATE` )
                   VALUES ( NULL , '{$arr['ITEM_ID']}', '{$arr['MATCHING_ID']}',
                  '{$arr['STORE_ID']}', '{$arr['AVIABLE']}', 
                  '{$arr['SUPPLIER_ID']}', NOW( ));";
                           
        $DB->Query($strSql); 
        
    }
    
    var $tablename = 'my_availability';
    
    var $fields = array('ID'         =>  array(  'TYPE'  =>  'INT'),
                        'ITEM_ID'    =>  array(  'TYPE'  =>  'INT'),
                        'MATCHING_ID'=>  array(  'TYPE'  =>  'INT'),
                        'STORE_ID'   =>  array(  'TYPE'  =>  'INT'),
                        'AVIABLE'    =>  array(  'TYPE'  =>  'INT'),
                        'SUPPLIER_ID'=>  array(  'TYPE'  =>  'INT'),
                        'DATE'       =>  array(  'TYPE'  =>  'DATE'),
                        );
    
    
    
}
