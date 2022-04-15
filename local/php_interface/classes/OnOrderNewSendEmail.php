<?php
/**
 * Copyright (c) 15/4/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class OnOrderNewSendEmail
{
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