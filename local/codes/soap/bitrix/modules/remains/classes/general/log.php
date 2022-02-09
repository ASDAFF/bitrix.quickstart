<?php
 
class remainsLog extends mytpl{
    
    var $tablename = 'my_log';
    
    var $fields = array('N1'     =>  array( 'TYPE'  =>  'INT'     ),
                        'N2'     =>  array( 'TYPE'  =>  'INT'     ),
                        'N3'     =>  array( 'TYPE'  =>  'INT'     ), 
                        'ID'     =>  array( 'TYPE'  =>  'INT'     ),
                        'DATE'   =>  array( 'TYPE'  =>  'DATE'    ),
                        'STR'    =>  array( 'TYPE'  =>  'VARCHAR' ),
                        'TYPE'   =>  array( 'TYPE'  =>  'INT'     ),
                        'TIME'   =>  array( 'TYPE'  =>  'FLOAT'   )); 
  
    function Add($arr){  
        
        
        global $DB; 
        
        if(!$arr['TYPE'])
            $arr['TYPE'] = 1; 
        
        if($arr['TYPE'] == 'ERROR')  $arr['TYPE'] = 2;
        
        if($arr['TYPE'] == 'OK')  $arr['TYPE'] = 1;
        
        $sqlStr = "INSERT INTO  `" . $this->tablename . "` (
                  `ID` ,  `DATE` , `STR` , `TYPE`, 
                  `N1`,  `N2`,  `N3`, `TIME`)
                   VALUES ( NULL , NOW( ) ,'{$arr['STR']}','{$arr['TYPE']}',
                  '{$arr['N1']}', '{$arr['N2']}', '{$arr['N3']}', '{$arr['TIME']}');"; 
                   
        $result = $DB->Query($sqlStr); 
        
        return intval($DB->LastID()); 
        
    }
    
    
    

}