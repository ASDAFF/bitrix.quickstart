<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

UET ( "SMARTREALT_FEEDBACK_FORM", "Поступила заявка на объект", $lid, "
    #NAME# - Имя   автора  
    #EMAIL# - Email автора   
    #PHONE# - Телефон автора            
    #MESSAGE# - Сообщение
    #OBJECT_NUMBER# - Номер объекта    
    #OBJECT_SECTION_NAME# - Название раздела   
    #OBJECT_ADDRESS# - Адрес   
    #OBJECT_PRICE# - Цена
       
    #DEFAULT_EMAIL_FROM# - E-Mail адрес по умолчанию (устанавливается в настройках)
    #SITE_NAME# - Название сайта (устанавливается в настройках)
    #SERVER_NAME# - URL сервера (устанавливается в настройках) 
");
 
$arr ["EVENT_NAME"] = "SMARTREALT_FEEDBACK_FORM";
$arr ["SITE_ID"] = $arSites;
$arr ["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
$arr ["EMAIL_TO"] = "#DEFAULT_EMAIL_FROM#";
$arr ["BCC"] = "";
$arr ["SUBJECT"] = "#SITE_NAME#: Поступила заявка на объект";
$arr ["BODY_TYPE"] = "text";
$arr ["MESSAGE"] = "   
Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Вам было отправлено сообщение через форму обратной связи

Автор: #NAME#
E-mail автора: #EMAIL#
Телефон автора: #PHONE#
Номер объекта: #OBJECT_NUMBER#
Объект: #OBJECT_SECTION_NAME#, #OBJECT_ADDRESS#
Цена: #OBJECT_PRICE#

Текст сообщения:
#MESSAGE#

Сообщение сгенерировано автоматически.
";

$arTemplates [] = $arr;
?>