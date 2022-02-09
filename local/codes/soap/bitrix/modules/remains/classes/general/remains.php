<?php

// вспомогательный класс для модуля выгрузки остатков
 
class remainsHelper{ 
     
    private $sklad_cache = array();
    
    private function getDays($sklad_id){
        if(isset($this->sklad_cache[$sklad_id]))
            return $this->sklad_cache[$sklad_id];
        
        CModule::IncludeModule('iblock');
        $db_props = CIBlockElement::GetProperty(8, $sklad_id, array(), Array("ID"=>30));
        if($ar_props = $db_props->Fetch())
            $this->sklad_cache[$sklad_id] = IntVal($ar_props["VALUE"]);
        else
            $this->sklad_cache[$sklad_id] = false;

        return $this->sklad_cache[$sklad_id];
    }
    
    private function dateDiff($dateStr, $daysInt){ // вернёт true если просрочено 
 
        $stmp = MakeTimeStamp($dateStr, "YYYY-MM-DD HH:MI:SS");
 
        $arrAdd = array("DD"	=> $daysInt,
                        "MM"	=> 0,
                        "YYYY"	=> 0, 
                        "HH"	=> 0,
                        "MI"	=> 0,
                        "SS"	=> 0);

        $stmp = AddToTimeStamp($arrAdd, $stmp); 
        return($stmp < time());  
      
    }
    
    function removePastDueDate($item_id){
        $av = new availability();
        $res = $av->GetList(array('DATE'=>'DESC'), array('ITEM_ID'=>$item_id));
        while($item = $res->Fetch()){ 
            $items[] = array_merge($item, array('SKLAD_DAYS' => $this->getDays($item['STORE_ID'])));
        }
        
        foreach ($items as $item){   
            if($item['SKLAD_DAYS'] == 0)
                continue; 
            if($this->dateDiff($item['DATE'], $item['SKLAD_DAYS'])){
               $av->RemoveByID($item['ID']); // удаляем с просроченой датой 
               
               
            }
        }
     
    }
    
    
     
    function compare($item_id, $remains_id){
        if(!$item_id) return;
        if(!$remains_id) return;
        if(!is_array($remains_id))
            $remains_id = array($remains_id); 
        $matching = new matching();
        $availability = new availability();  
        foreach ($remains_id as $remain_id) { 
            $matching->Update($remain_id, array('ITEM_ID'=>$item_id)); 
            $obj = $availability->GetList(array(), array('MATCHING_ID'=>$remain_id));
            while($av = $obj->Fetch()){
                $availability->Update($av['ID'], array('ITEM_ID'=>$item_id), true);
            } 
        }  
    }
    
    function getDataForAdminTable($id){
        $id = intval($id);
        if(!$id)
            return; 
        global $DB;
        $sql = "SELECT * FROM `my_availability` WHERE `ITEM_ID` = {$id} GROUP BY STORE_ID";
        $res = $DB->Query($sql);
        return $res; 
    }
  
    function setHiddenProperty($avail){
        $availability = new availability();
        foreach($avail as $id=>$cnt)
            $availability->Update ($id, array('AVIABLE'=>$cnt), $true);
    }
    
    
}