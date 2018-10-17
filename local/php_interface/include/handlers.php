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

//page start
AddEventHandler("main", "OnPageStart", "loadLocalLib", 1);
function loadLocalLib()
{
    Loader::includeModule('local.lib');
}

AddEventHandler("main", "OnPageStart", array('ModelAuthEmailClass', 'auth')); // Авторизация с помощью EMAIL
AddEventHandler("main", "OnAfterEpilog", array('Urlrewrite', 'OnAfterEpilog')); // Сортировка urlrewrite


/**
 * Подсветки PHP в редакторе
 **/
if ($_SERVER['SCRIPT_NAME'] == "/bitrix/admin/fileman_file_edit.php") {
    AddEventHandler("main", "OnEpilog", "InitPHPHighlight");
}
/**
 * AdminArea
 **/


/**
 * IBlockProps
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('CAATIBlockPropSection', 'GetUserTypeDescription'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('CAATIBlockPropElement', 'GetUserTypeDescription'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnUserTypeBuildList", array('PropertyHTML', 'GetUserTypeDescription'));


/**
 * BASKET
 * basket add
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnBeforeBasketAdd", array('Local\Lib\Handlers\Basket', 'beforeAdd'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnBasketAdd", array('Local\Lib\Handlers\Basket', 'afterAdd'));

/**
 * basket update
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnBeforeBasketUpdate", array('Local\Lib\Handlers\Basket', 'beforeUpdate'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnBasketUpdate", array('Local\Lib\Handlers\Basket', 'afterUpdate'));

/**
 * basket delete
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnBeforeBasketDelete", array('Local\Lib\Handlers\Basket', 'beforeDelete'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnBasketDelete", array('Local\Lib\Handlers\Basket', 'afterDelete'));


/**
 * order
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnOrderAdd", array('Local\Lib\Handlers\Order', 'afterAdd'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnOrderUpdate", array('Local\Lib\Handlers\Order', 'afterUpdate'));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd", array('CEshopEmailFieldsHandlers', 'OnBeforeEventAdd'));

/**
 * property types
 **/
AddEventHandler("main", "OnUserTypeBuildList", array('Local\Lib\Properties\Complect', 'GetUserTypeDescription'));
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array('Local\Lib\Properties\Complect', 'GetUserTypeDescription'));


/**
 * user
 **/
AddEventHandler("main", "OnBeforeUserRegister", array('\Local\Lib\Handlers\User', 'beforeUpdate'));
AddEventHandler("main", "OnBeforeUserUpdate", array('\Local\Lib\Handlers\User', 'beforeUpdate'));

/**
 * highload blocks
 **/
$eventManager->addEventHandler('', 'UserDataOnUpdate', array('\Local\Lib\Handlers\UserData', 'afterUpdate'));
$eventManager->addEventHandler('', 'UserDataOnAdd', array('\Local\Lib\Handlers\UserData', 'afterAdd'));

