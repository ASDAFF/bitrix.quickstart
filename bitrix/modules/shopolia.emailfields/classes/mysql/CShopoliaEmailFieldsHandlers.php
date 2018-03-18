<?php

if (!function_exists("d")) {
    function d ($v) {
        echo "<pre>";
        print_r($v);
        echo "</pre>";
    }
}

class CShopoliaEmailFieldsHandlers {
    function OnBeforeEventAdd (&$event, &$lid, &$arFields, &$message_id) {
        if ($arFields['ORDER_ID']>0) {
            $order = CSaleOrder::GetByID($arFields['ORDER_ID']);
            if ($event=="SALE_NEW_ORDER" AND $arFields['ORDER_ID']>0) { 
                CModule::IncludeModule("sale");
                $fields4email = array();
                $arOrderProps = array();
                $rsOrderProps = CSaleOrderPropsValue::GetOrderProps($arFields['ORDER_ID']);
                while ($ar = $rsOrderProps->GetNext()) {
                    $arOrderProps[$ar['CODE']?$ar['CODE']:$ar['ORDER_PROPS_ID']] = $ar;
                    $val = $ar['VALUE'];
                    if ($ar['TYPE']=="LOCATION") {
                        $v = CSaleLocation::GetByID($val);
                        $val = $v['CITY_NAME_LANG'];
                    } elseif (in_array($ar['TYPE'], array("SELECT", "MULTISELECT", "RADIO"))) {
                        $v = CSaleOrderPropsVariant::GetByValue($ar['ORDER_PROPS_ID'], $val);
                        $val = $v['NAME'];
                    }
                    $fields4email[$ar['CODE']?$ar['CODE']:$ar['ORDER_PROPS_ID']] = $val;
                }
                if (is_array($fields4email) AND !empty($fields4email)) {
                    foreach ($fields4email as $code=>$prop_val) {
                        $name = $arOrderProps[$code]['NAME'];
                        $arFields['PROP_'.$code] = $name.": ".$prop_val;
                        $arFields['PROP_VALUE_'.$code] = $prop_val;
                        $arFields['PROP_NAME_'.$code] = $name;
                    }
                }
            } elseif ($event=="SALE_ORDER_DELIVERY") {
                $arFields['DELIVERY_DOC_NUM'] = $order['DELIVERY_DOC_NUM'];
                $arFields['DATE_ALLOW_DELIVERY'] = $order['DATE_ALLOW_DELIVERY'];            
            }
        }
    }
}

?>
