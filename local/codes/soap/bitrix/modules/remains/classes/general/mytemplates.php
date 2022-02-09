<?php

/*  Aбстактный класс с общими методами для доступа ко всем таблицам в рамках модуля подгрузка остатков
 *  Aвтор - Александр Кудин 
 *  kudinsasha@gmail.com
 *  04.04.2013
 *  Не реккомендуется просмотр людям, лишённым шизофрении 
 */

abstract class mytpl{

    function GetList($order, $filter){
        global $DB;      
        if (!$order)
            $order = array('ID' => 'ASC'); 
        foreach ($order as $k => $v) { 
            $v = strtoupper($v) == 'ASC' ? $v : 'DESC';
            if (in_array($k, array_keys($this->fields)))
                $o[] = '`' . $DB->ForSql($k) . '` ' . $v;
        }
        $order = implode(', ', $o);  
        foreach ($filter as $k => $v) {
            if (in_array($k, array_keys($this->fields))) {
                if (is_array($v)) {
                    $strTmp = '`' . $k . '` IN ( ';
                    foreach($filter[$k] as $key => $id){
                        $strTmp.= '"' . $DB->ForSql($id) . '"';
                        if($key != count($filter[$k]) - 1)
                            $strTmp.= ',';
                        }
                    $strTmp.= ')';
                    $f[] = $strTmp;
                } else {
                    $f[] = '`' . $k . '` = "' . $DB->ForSql($v) . '"';
                }
            } else {
                if(in_array($firstSymbol = substr($k, 0, 1), array('?', '!', '>', '<')) &&
                   in_array($k_ = substr($k, 1), array_keys($this->fields))){ 
                    switch (true) { 
                        case $firstSymbol == '?':
                                if (is_array($v)) {
                                    $filTmp = array();
                                    foreach ($filter[$k] as $id)
                                        $filTmp[] = '`' . $k_ . '` LIKE "' . $DB->ForSql($id) . '"';
                                    $f[] = '(' . implode(') OR (', $filTmp) . ')';
                                } else {
                                    $f[] = '`' . $k_ . '` LIKE "' . $DB->ForSql($v) . '"';
                                }
                            break; 
                        case $firstSymbol == '!': 
                                $f[] = '`' . $k_ . '` != "' . $DB->ForSql($v) . '"';
                            break;
                        case $firstSymbol == '>' || $firstSymbol == '<':  
                                $f[] = '`' . $k_ . '` ' . $firstSymbol . ' "' . $DB->ForSql($v) . '"';
                            break;
                        default: 
                            break;
                    }
                }
                
            }
        }

        if(!$f) 
            $where = '1';
        elseif(count($f) == 1)
            $where = "( {$f[0]} )";
        elseif(count($f) > 1)
            $where = '(' . implode(') AND (', $f) . ')'; 

        $strSql = 'SELECT * FROM `' . $this->tablename . '`
                  ' . ($where ? ' WHERE ' . $where : '') . '
                  ' . ($order ? ' ORDER BY ' . $order : '') . ';';
        $rs = $DB->Query($strSql);  
        return $rs;
    } 
 
    public function Update($id, $arr) { 
        $id = intval($id);
        if (!$id || !$arr || !is_array($arr))
            return false;  
        global $DB;
        $f = array(); 
        foreach ($arr as $k => $v)
            if (in_array($k, array_keys($this->fields)))
                $f[] = '`' . $k . '` = "' . $DB->ForSql($v) . '" ';   
        if (!count($f))
            return false;
        $result = $DB->Query( 'UPDATE `'. $this->tablename .
                            '` SET '. implode(',', $f) . 
                            '  WHERE `ID` = '. $id .';'); 
        return $result; 
    }
    
    function RemoveAll() {
        global $DB;
        $DB->Query("DELETE FROM `" . $this->tablename . "`");
    } 
     
    function RemoveByID($id) {
        $id = intval($id);
        if ($id <= 0)
            return false;
        global $DB; 
        $DB->Query("DELETE FROM `" . $this->tablename . "` WHERE `ID` = {$id}");
    }
    
    function GetByID($id){
        return $this->GetList(array(), array('ID' => $id));
    }
     
    function GetAll($arOrder = array("ID"=>"DESC")){
        return $this->GetList($arOrder, array());
    }
    
}
