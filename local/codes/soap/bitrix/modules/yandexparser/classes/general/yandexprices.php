<?php

/* 
 *  Класс для работы с яндекс ценами
 *  Автор - Александр Кудин
 *  kudinsasha@gmail.com
 *  19.03.2013
 *  Версия 1.0
 */

/*
    CREATE TABLE IF NOT EXISTS `yandex_prices` (
      `ID` int(4) NOT NULL AUTO_INCREMENT,
      `ITEM_ID` int(4) NOT NULL,
      `SHOP_NAME` varchar(255) NOT NULL,
      `URL` varchar(255) NOT NULL,
      `PRICE` float NOT NULL,
      `DELIVERY` float NOT NULL,
      `DATE` datetime NOT NULL,
      PRIMARY KEY (`ID`)
    ) ENGINE=MyISAM  
 */
 
class yandexPrices {
 
    static private $tablename = 'yandex_prices';

    function Add($arr) {
      
        global $DB, $APPLICATION;
  
        $arr['ITEM_ID'] = intval($arr['ITEM_ID']);
        $arr['SHOP_NAME'] = $DB->ForSql(trim($arr['SHOP_NAME']));
        $arr['URL'] = trim($arr['URL']);     
        
        $arr['PRICE'] = str_replace(",",'.',$arr['PRICE']); 
        $arr['PRICE'] = preg_replace("/[^x\d|*\.]/","",$arr['PRICE']); 

        $arr['PRICE'] = str_replace(' ', '', $arr['PRICE']);
 
        $arr['DELIVERY'] = floatval($arr['DELIVERY']);
 
        $strSql = "INSERT INTO `" . self::$tablename . "` (
                    `ID` ,
                    `ITEM_ID` ,
                    `SHOP_NAME` ,
                    `URL` ,
                    `PRICE` ,
                    `DELIVERY` ,
                    `DATE`
                    )
                    VALUES (
                    NULL , '{$arr['ITEM_ID']}', 
                           '{$arr['SHOP_NAME']}',
                           '{$arr['URL']}',
                           '{$arr['PRICE']}',
                           '{$arr['DELIVERY']}',
                           NOW( )
                    );";
        $result = $DB->Query($strSql);
        return $result;
    }   
    function GetList($order, $filter) {
        global $DB, $APPLICATION;
        if (!$order)
            $order = array('SORT' => 'asc');

        foreach ($order as $k => $v) {
            $v = $v == 'asc' ? $v : 'desc';
            if (in_array($k, array('ID', 'ITEM_ID', 'SHOP_NAME', 'URL', 'PRICE', 'DELIVERY')))
                $o[] = '`' . $DB->ForSql($k) . '` ' . $v;
        }
        $order = implode(', ', $o);
        
        $f = array();
        $f[] = '1=1';

        foreach ($filter as $k => $v) {
            if (in_array($k, array('SHOP_NAME', 'URL')))
                $f[] = '`' . $k . '` = "' . $v . '"';
            
            if (in_array($k, array('ID','ITEM_ID', 'PRICE', 'DELIVERY'))) {
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
                  ' . ($order ? ' ORDER BY ' . $order : '') . ';';
 
        $rs = $DB->Query($strSql);
        return $rs;
    } 
    function GetAll() {
        global $DB, $APPLICATION;
        $strSql = "SELECT * FROM `" . self::$tablename . "`";
        $result = $DB->Query($strSql);
        return $result;
    }
    function Update($id, $arr) { 
        $id = intval($id);
        if (!$id || !$arr)
            return false;

        $f = array();
        foreach ($arr as $k => $v)
            if (in_array($k, array('ITEM_ID', 
                                   'SHOP_NAME', 
                                   'PRICE',
                                   'DELIVERY', 
                                   'URL')))
                $f[] = '`' . $k . '` = "' . $v . '" ';

        if (count($f) == 0)
            return false;

        $set = implode(',', $f);

        $strSql = 'UPDATE `' . self::$tablename . '` SET
                  ' . $set . '
                  WHERE `ID` = ' . $id . ';';

        global $DB, $APPLICATION;
        $result = $DB->Query($strSql);
    }
    function GetByID($id) {
        $id = intval($id);
        if ($id <= 0)
            return false;
        global $DB, $APPLICATION;
        $rs = $DB->Query('SELECT * FROM `' . self::$tablename . '` WHERE `ID` = ' . $id);
        return $rs;
    }
    function RemoveAll() {
        global $DB, $APPLICATION;
        $DB->Query("DELETE FROM `" . self::$tablename . "`");
    } 
    function RemoveByID($id) {
        $id = intval($id);
        if ($id <= 0)
            return false;
        global $DB, $APPLICATION;
        $id = intval($id);
        $DB->Query("DELETE FROM `" . self::$tablename . "` WHERE `ID` = {$id}");
    }
    function RemoveByItemID($id) {
        $id = intval($id);
        
        if ($id <= 0) 
            return false;
        
        global $DB;
      
        $DB->Query("DELETE FROM `" . self::$tablename . "` WHERE `ITEM_ID` = {$id}"); 
    }    
  
    
    function GetAllSuccessId(){ // должо вернуть только успешные
        global $DB, $APPLICATION;
  
        $rs = $DB->Query("SELECT DISTINCT ITEM_ID FROM `" . self::$tablename . "` WHERE 1;"); 
        while($id = $rs->fetch())
            $ids[] = $id["ITEM_ID"];
    
        return $ids;
    }
    
  
    
} 