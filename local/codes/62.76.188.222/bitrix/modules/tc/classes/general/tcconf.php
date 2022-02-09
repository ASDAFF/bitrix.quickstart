<?php
 
class tcConfig {

    // тянет коды свойств, не отображаемых на странице сравнения
    function getHiddenProps($iblockID){   
        $opt = COption::GetOptionString("tc", "props" . $iblockID );
        $arr = explode(",", $opt);
        if(is_array($arr)){
            CModule::IncludeModule('iblock');
            foreach ($arr as $id){
                $res = CIBlockProperty::GetByID($id, $iblockID);
                if($ar_res = $res->GetNext())
                  if($ar_res['CODE'])
                      $result[] = $ar_res['CODE'];
            }
        }
        return $result;
    }
    
   
    
    
    
}