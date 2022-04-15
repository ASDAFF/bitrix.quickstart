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

\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnPageStart", array('OnPageStart', 'authEmailClass')); // Авторизация с помощью EMAIL
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnAfterEpilog", array('Urlrewrite', 'OnAfterEpilog')); // Сортировка urlrewrite


/**
 * order
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd", array( 'OnBeforeEventAdd','CEshopEmailFieldsHandlers'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnOrderNewSendEmail", array('OnOrderNewSendEmail', 'bxModifySaleMails'));