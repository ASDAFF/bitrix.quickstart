<?php
if(LANG_CHARSET){ header('Content-Type: text/html; charset='.LANG_CHARSET); }
global $MESS;
$MESS['SHEEPLA_EDITSETTINGS_TITLE'] = "Настройки модуля Sheepla";
$MESS['SHEEPLA_SETTINGS_ADMINAPIKEY_NAME'] = "Административный ключ API";
$MESS['SHEEPLA_SETTINGS_PUBLICAPIKEY_NAME'] = "Публичный ключ API";
$MESS['SHEEPLA_SETTINGS_APIURL_NAME'] = "API URL";
$MESS['SHEEPLA_SETTINGS_JSURL_NAME'] = "JS URL";
$MESS['SHEEPLA_SETTINGS_CSSURL_NAME'] = "CSS URL";
$MESS['SHEEPLA_SETTINGS_SAVE_TITLE'] = "Сохранить";
$MESS['SHEEPLA_SETTINGS_CONF_OK'] = "Настройки верны и сохранены";
$MESS['SHEEPLA_SETTINGS_CONF_ERR'] = "Настройки не верны";
$MESS['SHEEPLA_YES'] = "Да";
$MESS['SHEEPLA_NO'] = "Нет";
$MESS['SHEEPLA_SETTINGS_CHEKOUT_URL_TITLE'] = "URL корзины магазина";
$MESS['SHEEPLA_SETTINGS_SYNC_TYPE'] = "Синхронизировать все заказы или только Sheepla";
$MESS['SHEEPLA_SETTINGS_PROFILE_TITLE'] = "Название профиля";
$MESS['SHEEPLA_SETTINGS_PROFILE_DESCRIPTION'] = "Описание профиля";
$MESS['SHEEPLA_SETTINGS_PROFILE_TEMPLATE'] = "Шаблон Sheepla";
$MESS['SHEEPLA_SETTINGS_PROFILE_SORT'] = "Сортировка";
$MESS['SHEEPLA_SETTINGS_PROFILE_MARK'] = "Удалить";
$MESS['SHEEPLA_SETTINGS_PROFILE_MSG1'] = "Заполните поля";
$MESS['SHEEPLA_SETTINGS_PROFILE_MSG2'] = "Неверные настройки. Сначала укажите API ключи.";
?>