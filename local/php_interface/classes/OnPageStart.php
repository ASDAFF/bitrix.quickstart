<?php
/**
 * Copyright (c) 15/4/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class OnPageStart
{
    /**
     * Авторизация с помощью EMAIL
     * Очень часто необходимо авторизовывать пользователя в одном поле с помощью LOGIN или EMAIL в стандартном компоненте авторизации.
     * Для программистов:
     * пользователь авторизуется через $_REQUEST['USER_LOGIN']
     *
     * При проверке EMAIL используется функция filter_var
     */
    function authEmailClass(){
        if(!filter_var($_REQUEST['USER_LOGIN'], FILTER_VALIDATE_EMAIL)) return;
        \Bitrix\Main\Loader::IncludeModule("main");
        $rsUser = CUser::GetList(
            ($by="id"),
            ($order="asc"),
            array(
                "=EMAIL"=>htmlspecialcharsbx($_REQUEST['USER_LOGIN'])
            )
        );
        global $USER;
        if($arU = $rsUser->GetNext()){
            if($_REQUEST["USER_LOGIN"]==$arU['EMAIL']){
                $_POST["USER_LOGIN"] = $_REQUEST["USER_LOGIN"] = $arU['LOGIN'];
            }
        }
    }
}