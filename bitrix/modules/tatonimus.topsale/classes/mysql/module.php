<?php

IncludeModuleLangFile(__FILE__);

/**
 * Класс настроек пересылки данных
 */
class CTopsale extends CTopsale_general
{

    /**
     * Конструктор объекта, определяет все параметры
     */
    function CTopsale()
    {
        self::CTopsale_general();
    }

    /**
     * Функция добавляет связку обновляемых параметров
     *
     * @param Array $arFields Массив полей
     * @return CDatabase
     */
    function Add($arFields)
    {
        if (isset($arFields['ID'])) {
            unset($arFields['ID']);
        }
        if (!empty($arFields) && is_array($arFields)) {
            foreach ($arFields as $key => $val) {
                if (!isset(self::$arFieldsKey[strtoupper($key)])) {
                    unset($arFields[$key]);
                }
            }
        }
        if (empty($arFields) || !is_array($arFields)) {
            self::$LAST_ERROR = GetMessage("TTSML_EMPTY_FIELDS");
            return false;
        }

        global $DB;
        self::$LAST_ERROR = '';
        $sSql = '';
        $NotSetRequiredFields = array();

        foreach (self::$arRequiredFieldsKey as $key => $b) {
            if (!isset($arFields[$key]) || empty($arFields[$key])) {
                $NotSetRequiredFields = $key;
            }
        }
        if (!empty($NotSetRequiredFields)) {
            self::$LAST_ERROR = GetMessage("TTSML_EMPTY_REQUIRED_FIELDS") . implode(', ',
                    $NotSetRequiredFields);
            return false;
        }

        foreach ($arFields as $key => $val) {
            $key = strtoupper($key);
            $val = htmlspecialchars_decode($val);
            $val = mysql_real_escape_string($val);
            $val = str_replace("'", "\\'", $val);
            $sSql .= ", `{$key}` = '{$val}' ";
        }
        $sSql = substr($sSql, 1);
        $sSql = 'INSERT `b_topsale` SET ' . $sSql;
        $res = $DB->Query($sSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);
        if (strlen($res->LAST_ERROR) > 0) {
            self::$LAST_ERROR = $res->LAST_ERROR;
        }

        return $res;
    }

    /**
     * Функция обновляет связку обновляемых параметров
     *
     * @param Int $ID ИД связи
     * @param Array $arFields Массив параметров
     * @return CDatabase
     */
    function Update($ID, $arFields)
    {
        $ID = intval($ID);
        if (isset($arFields['ID']))
            unset($arFields['ID']);
        if (empty($ID)) {
            self::$LAST_ERROR = GetMessage("TTSML_EMPTY_ID");
            return false;
        }
        if (!empty($arFields) && is_array($arFields))
            foreach ($arFields as $key => $val) {
                if (!isset(self::$arFieldsKey[strtoupper($key)])) {
                    unset($arFields[$key]);
                }
            }
        if (empty($arFields) || !is_array($arFields)) {
            self::$LAST_ERROR = GetMessage("TTSML_EMPTY_FIELDS");
            return false;
        }

        global $DB;
        self::$LAST_ERROR = '';
        $sSql = '';

        foreach ($arFields as $key => $val) {
            $key = strtoupper($key);
            $val = htmlspecialchars_decode($val);
            $val = mysql_real_escape_string($val);
            $val = str_replace("'", "\\'", $val);
            $sSql .= ", `{$key}` = '{$val}' ";
        }
        $sSql = substr($sSql, 1);
        $sSql = 'UPDATE `b_topsale` SET ' . $sSql . ' WHERE ID = "' . $ID . '"';
        $res = $DB->Query($sSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);
        if (strlen($res->LAST_ERROR) > 0) {
            self::$LAST_ERROR = $res->LAST_ERROR;
        }

        return $res;
    }

    /**
     * Функция удаляет связку параметров
     *
     * @param Int $ID ИД связки
     * @return CDatabase
     */
    function Delete($ID)
    {
        $ID = intval($ID);
        if (empty($ID)) {
            self::$LAST_ERROR = GetMessage("TTSML_EMPTY_ID");
            return false;
        }

        global $DB;
        self::$LAST_ERROR = '';

        $sSql = 'DELETE FROM `b_topsale` WHERE ID = "' . $ID . '"';
        $res = $DB->Query($sSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);
        if (strlen($res->LAST_ERROR) > 0) {
            self::$LAST_ERROR = $res->LAST_ERROR;
        }

        return $res;
    }

    /**
     * Функция возвращает связки обновляемых параметров в соответствии с параметрами
     *
     * @param Array $arSort Массив описывающий порядок сортировки
     * @param Array $arFilter Массив описывающий фильтрацию выбираемых значений
     * @return CDatabase
     */
    function GetList($arSort = array('ID' => 'ASC'), $arFilter = array())
    {
        global $DB;
        self::$LAST_ERROR = '';
        $sSelect = "t1.`" . implode("`, t1.`", array_keys(self::$arFieldsKey)) . "` ";
        $sWhere = '';
        $sOrder = '';


        if (!empty($arFilter))
            foreach ($arFilter as $key => $where) {
                $type = '=';
                if (strpos($key, '!=') === 0) {
                    $type = '!=';
                    $key = substr($key, 2);
                } elseif (strpos($key, '<=') === 0) {
                    $type = '<=';
                    $key = substr($key, 2);
                } elseif (strpos($key, '>=') === 0) {
                    $type = '>=';
                    $key = substr($key, 2);
                } elseif (strpos($key, '!%') === 0) {
                    $type = 'NOT LIKE';
                    $key = substr($key, 1);
                } elseif (strpos($key, '%') === 0) {
                    $type = ' LIKE ';
                    $key = substr($key, 1);
                } elseif (strpos($key, '<') === 0) {
                    $type = '<';
                    $key = substr($key, 1);
                } elseif (strpos($key, '>') === 0) {
                    $type = '>';
                    $key = substr($key, 1);
                } elseif (strpos($key, '!') === 0) {
                    $type = '!=';
                    $key = substr($key, 1);
                } elseif (strpos($key, '=') === 0) {
                    $type = '=';
                    $key = substr($key, 1);
                }
                if (!isset(self::$arFieldsKey[$key]))
                    continue;
                if (is_array($where)) {
                    foreach ($where as $k => $v)
                        $where[$k] = str_replace("'", "\\'", $v);
                    $sWhere .= "AND (t1.`{$key}` {$type} '" . implode("' OR t1.`{$key}` {$type} '",
                            $where) . "') ";
                } else {
                    $where = str_replace("'", "\\'", $where);
                    $sWhere .= "AND t1.`{$key}` {$type} '{$where}' ";
                }
            }

        if (!empty($arSort))
            foreach ($arSort as $key => $order) {
                if (!isset(self::$arFieldsKey[$key]))
                    continue;
                if (strtoupper($order) != 'ASC')
                    $order = 'DESC';
                $sOrder .= ', t1.`' . $key . '` ' . $order;
            }

        if (empty($sSelect)) {
            $sSelect = "*";
        }
        if (!empty($sWhere)) {
            $sWhere = "WHERE " . substr($sWhere, 3);
        }
        if (!empty($sOrder)) {
            $sOrder = "ORDER BY " . substr($sOrder, 1);
        }

        $sSql = "SELECT {$sSelect} FROM `b_topsale` as t1 {$sWhere} {$sOrder} ";
        $res = $DB->Query($sSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);
        if (isset($res->LAST_ERROR) && trlen($res->LAST_ERROR) > 0) {
            self::$LAST_ERROR = $res->LAST_ERROR;
        }

        return $res;
    }

}

?>