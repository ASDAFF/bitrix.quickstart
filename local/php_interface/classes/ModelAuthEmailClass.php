<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 17.10.2018
 * Time: 9:14
 */

/**
 * Class ModelAuthEmailClass
 * Авторизация с помощью EMAIL
 * Очень часто необходимо авторизовывать пользователя в одном поле с помощью LOGIN или EMAIL в стандартном компоненте авторизации.
 * Для программистов:
 * пользователь авторизуется через $_REQUEST['USER_LOGIN']
 *
 * При проверке EMAIL используется функция filter_var
 */

class ModelAuthEmailClass
{
    function auth(){
        if(!filter_var($_REQUEST['USER_LOGIN'], FILTER_VALIDATE_EMAIL)) return;
        CModule::IncludeModule("main");
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