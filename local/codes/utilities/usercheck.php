<?php
/**
 * Copyright (c) 15/4/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */



/*** ПРОВЕРКА ПАРОЛЯ ПОЛЬЗОВАТЕЛЯ ***/
$pass = $_REQUEST['pass']; // получаем введенный пароль
$login = CUser::GetLogin(); // получаем логин текущего юзера
$USERcheck = new CUser;
$check = $USERcheck->Login($login, $pass, "N", "Y"); // проверяем верный ли пароль