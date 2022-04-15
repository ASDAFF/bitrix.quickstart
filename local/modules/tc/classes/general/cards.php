<? 
/*
 *  Класс для работы со скидочными картами 
 *  Автор - Александр Кудин
 *  kudinsasha@gmail.com
 *  25.03.2013
 *  Версия 1.0 
  
    CREATE TABLE  `cards` (
        `id` INT NOT NULL AUTO_INCREMENT ,
        `vladelec` VARCHAR( 255 ) NOT NULL ,
        `ostatok` INT( 4 ) NOT NULL ,
        `summa` FLOAT( 4 ) NOT NULL ,
        `procent` INT( 4 ) NOT NULL ,
        `nomer` VARCHAR( 10 ) NOT NULL ,
        PRIMARY KEY ( `id` )
    ) ENGINE = MYISAM ;
*/

class tcCards { 
 
    static private $tablename = 'cards';
 
    private function numValidate($str){ 
        $str = str_replace(",",'.',$str); 
        return preg_replace("/[^x\d|*\.]/","",$str); 
    }

    function GetList($order, $filter) {
        global $DB;
        if (!$order)
            $order = array('id' => 'asc');

        foreach ($order as $k => $v) {
            $v = $v == 'asc' ? $v : 'desc';
            if (in_array($k, array('id',       'vladelec', 'ostatok',
                                   'tipsidki', 'summa',    'procent', 
                                   'nomer')))
                $o[] = '`' . $DB->ForSql($k) . '` ' . $v;
        }
        $order = implode(', ', $o);
        
        $f = array();
        $f[] = '1=1';

        foreach ($filter as $k => $v) {
            if (in_array($k, array('tipsidki', 'vladelec')))
                $f[] = '`' . $k . '` = "' . $v . '"';
            
            if (in_array($k, array('id', 'ostatok', 'summa', 'procent', 'nomer'))){
                if (is_array($v)){
                    $filTmp = array();
                    foreach ($filter[$k] as $id)
                        $filTmp[] ='`' . $k . '` = "' . $id . '"';
                    $f[] = '(' . implode(') OR (', $filTmp) . ')';
                    }
                else{
                    $f[] = '`' . $k . '` = "' . $v . '"';
                    }
                }
            }

        $where = '(' . implode(') AND (', $f) . ')';
        $strSql = 'SELECT * FROM `' . self::$tablename . '`
                  ' . ($where ? ' WHERE ' . $where : '') . '
                  ' . ($order ? ' ORDER BY ' . $order : '') . ' LIMIT 5000;';
 
        $rs = $DB->Query($strSql);
        return $rs;
    } 
    
    function GetAll(){
        global $DB; 
        $result = $DB->Query("SELECT * FROM " . self::$tablename);
        return $result; 
    }
 
    function Add($arr) {
      
        global $DB, $APPLICATION;
  
        $arr['VLADELEC'] = $DB->ForSql(trim($arr['VLADELEC']));
        $arr['TIP_SKIDKI'] = $DB->ForSql(trim($arr['TIP_SKIDKI']));
        $arr['OSTATOK'] = intval($arr['OSTATOK']); 
        $arr['SUMMA'] = self::numValidate($arr['SUMMA']);
        $arr['PROCENT'] = intval($arr['PROCENT']); 
        $arr['NOMER'] = trim($arr['NOMER']); 
        
        $strSql = "INSERT INTO `" . self::$tablename . "` (
                        `id` ,  `vladelec` ,  `ostatok` ,
                        `summa` ,  `procent` ,  `nomer`, `tipsidki`  )
                   VALUES (
                        NULL , '{$arr['VLADELEC']}', '{$arr['OSTATOK']}',
                        '{$arr['SUMMA']}', '{$arr['PROCENT']}', '{$arr['NOMER']}', 
                        '{$arr['TIP_SKIDKI']}' ) ";
    
        $result = $DB->Query($strSql);
        return $result;
    }   

    function RemoveAll(){
        global $DB, $APPLICATION;
        $DB->Query("DELETE FROM `" . self::$tablename . "`");
    }
    
    function RemoveByID($id) {
        $id = intval($id);
        if ($id <= 0)
            return false;
        global $DB, $APPLICATION;
        $id = intval($id);
        $DB->Query("DELETE FROM `" . self::$tablename . "` WHERE `id` = {$id}");
    }
     
    function GetByID($id) {
        $id = intval($id);
        if ($id <= 0)
            return false;
        global $DB, $APPLICATION;
        $rs = $DB->Query('SELECT * FROM `' . self::$tablename . '` WHERE `id` = ' . $id);
        return $rs;
    }

    function GetByNum($id){
        global $DB;
        $rs = $DB->Query('SELECT * FROM `' . self::$tablename . '` WHERE `nomer` = ' . $id);
        return $rs;
    }
    
}
 