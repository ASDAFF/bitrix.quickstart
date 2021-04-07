<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 16.10.2018
 * Time: 15:10
 */

/**
 * Расширение уведомлений о заказах
 * Добавляет в стандартные почтовые шаблоны значения свойств заказа.
 * Применимо для писем с уведомлением об оформлении нового заказа, о разрешении доставки.
 *
 **/

class CEshopEmailFieldsHandlers
{
    function OnBeforeEventAdd (&$event, &$lid, &$arFields, &$message_id) {
        if ($arFields['ORDER_ID']>0) {
            $order = CSaleOrder::GetByID($arFields['ORDER_ID']);
            if ($event=="SALE_NEW_ORDER" AND $arFields['ORDER_ID']>0) {
                \Bitrix\Main\Loader::IncludeModule("sale");
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

    function bxModifySaleMails($orderID, &$eventName, &$arFields)
    {
        $arOrder = CSaleOrder::GetByID($orderID);

        //-- получаем телефоны и адрес
        $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
        $phone = "";
        $index = "";
        $country_name = "";
        $city_name = "";
        $address = "";
        $fio = "";
        while ($arProps = $order_props->Fetch()) {
            if ($arProps["CODE"] == "PHONE") {
                $phone = htmlspecialchars($arProps["VALUE"]);
            }
            if ($arProps["CODE"] == "FIO") {
                $fio = htmlspecialchars($arProps["VALUE"]);
            }
            if ($arProps["CODE"] == "EMAIL") {
                $email = htmlspecialchars($arProps["VALUE"]);
            }

            if ($arProps["CODE"] == "INDEX") {
                $index = $arProps["VALUE"];
            }

            if ($arProps["CODE"] == "ADDRESS") {
                $address = $arProps["VALUE"];
            }
        }

        $full_address = $address;

        //-- получаем название службы доставки
        $arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
        $delivery_name = "";
        if ($arDeliv) {
            $delivery_name = $arDeliv["NAME"];
        }

        //-- получаем название платежной системы
        $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
        $pay_system_name = "";
        if ($arPaySystem) {
            $pay_system_name = $arPaySystem["NAME"];
        }

        //-- добавляем новые поля в массив результатов
        $arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"];
        $arFields["PHONE"] = $phone;
        $arFields["DELIVERY_NAME"] = $delivery_name;
        $arFields["PAY_SYSTEM_NAME"] = $pay_system_name;
        $arFields["FULL_ADDRESS"] = $full_address;
        $arFields["FIO"] = $fio;
    }
}