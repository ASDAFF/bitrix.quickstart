<?php
/**
 * Copyright (c) 9/2/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper\User;


class CUserData
{
    function getUserData($string)
    {
        // поиск по логину
        $rsUser = CUser::GetByLogin($string);
        if (!$arUser = $rsUser->Fetch()) {

            //поиск по email
            $filter = Array("EMAIL" => $string);
            $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter);
            if (!$arUser = $rsUsers->GetNext()) {

                //поиск по телефону
                $filter = Array("PERSONAL_PHONE" => $string);
                $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter);
                $arUser = $rsUsers->GetNext();
            }
        }
        return $arUser;
    }
}