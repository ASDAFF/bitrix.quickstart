<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 16.05.2018
 * Time: 21:49
 *
 * Event handling.
 *
 * We strongly recommend to group event handlers in classes.
 *
 * For example, you can handle events "OnBeforeUserAdd" and "OnBeforeUserUpdate"
 * with methods UserHandlers::OnBeforeUserAdd() and UserHandlers::OnBeforeUserUpdate(), like this:
 *
 * AddEventHandler("main", "OnBeforeUserAdd", Array("UserHandlers", "OnBeforeUserAdd"));
 */

use \Bitrix\Main\Loader;

$eventManager = \Bitrix\Main\EventManager::getInstance();

AddEventHandler("main", "OnPageStart", array('ModelAuthEmailClass', 'auth')); // Авторизация с помощью EMAIL
AddEventHandler("main", "OnAfterEpilog", array('Urlrewrite', 'OnAfterEpilog')); // Сортировка urlrewrite

/**
 * Свойство инфоблока Привязка к медиабиблиотеке
 **/
AddEventHandler("main", "OnUserTypeBuildList", array('PropMediaLibUserType', 'GetUserTypeDescription'));
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array('PropMediaLibIblockProperty', 'GetUserTypeDescription'));

/**
 * Пользовательские свойства для инфоблоков
 **/
AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyCRM', 'GetUserTypeDescription')); // свойство "Выбор компании из CRM"
AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyColor', 'GetUserTypeDescription')); // свойство "Выбор цвета". Цвет хранится как строка вида ff0000 без знака #

/**
 * Пользовательское свойство "Да/Нет в виде Input Checkbox (Флажок)
 */
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CUserTypeYesNo", "GetUserTypeDescription"), 50);

/**
 * order
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd", array('CEshopEmailFieldsHandlers', 'OnBeforeEventAdd'));

$eventManager->addEventHandler("sale", "OnOrderNewSendEmail", array('CEshopEmailFieldsHandlers', 'bxModifySaleMails'));