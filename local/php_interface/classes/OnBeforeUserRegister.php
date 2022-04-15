<?php
/**
 * Copyright (c) 15/4/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class OnBeforeUserRegister
{
    function OnBeforeUserRegisterHandler(&$arFields) {
        $arEventFields = array(
            "LOGIN"       =>      $arFields["LOGIN"],
            "PASSWORD"   =>     $arFields["PASSWORD"],
            "EMAIL"       =>      $arFields["EMAIL"],
            "NAME"       =>     $arFields["NAME"],
            "LAST_NAME"   =>      $arFields["LAST_NAME"],
        );
        CEvent::Send("REG", 's1', $arEventFields);
    }
}