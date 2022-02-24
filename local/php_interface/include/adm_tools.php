<?php
/**
 * Copyright (c) 24/2/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */


/**
 * IBlockProps
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('CAATIBlockPropSection', 'GetUserTypeDescription'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('CAATIBlockPropElement', 'GetUserTypeDescription'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnUserTypeBuildList", array('PropertyHTML', 'GetUserTypeDescription'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyCRM', 'GetUserTypeDescription')); // свойство "Выбор компании из CRM"
\Bitrix\Main\EventManager::getInstance()->addEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyColor', 'GetUserTypeDescription')); // свойство "Выбор цвета". Цвет хранится как строка вида ff0000 без знака #


/**
 * Свойство инфоблока Привязка к медиабиблиотеке
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnUserTypeBuildList", array('PropMediaLibUserType', 'GetUserTypeDescription'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('PropMediaLibIblockProperty', 'GetUserTypeDescription'));

/**
 * Пользовательское свойство "Да/Нет в виде Input Checkbox (Флажок)
 */
\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array("CUserTypeYesNo", "GetUserTypeDescription"), 50);