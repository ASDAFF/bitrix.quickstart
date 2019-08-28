<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$eventManager = \Bitrix\Main\EventManager::getInstance();

//page start
AddEventHandler('main', 'OnPageStart', 'loadCollectedLibrary', 1);
function loadCollectedLibrary()
{
    \Bitrix\Main\Loader::includeModule('collected.library');
}

/**
 * @TODO make all handlers
 */


//BASKET
//basket add
AddEventHandler('sale', 'OnBeforeBasketAdd', array('Collected\Handlers\Basket', 'beforeAdd'));
AddEventHandler('sale', 'OnBasketAdd', array('Collected\Handlers\Basket', 'afterAdd'));

//basket update
AddEventHandler('sale', 'OnBeforeBasketUpdate', array('Collected\Handlers\Basket', 'beforeUpdate'));
AddEventHandler('sale', 'OnBasketUpdate', array('Collected\Handlers\Basket', 'afterUpdate'));

// basket delete
AddEventHandler('sale', 'OnBeforeBasketDelete', array('Collected\Handlers\Basket', 'beforeDelete'));
AddEventHandler('sale', 'OnBasketDelete', array('Collected\Handlers\Basket', 'afterDelete'));

//order
AddEventHandler('sale', 'OnOrderAdd', array('Collected\Handlers\Order', 'afterAdd'));
AddEventHandler('sale', 'OnOrderUpdate', array('Collected\Handlers\Order', 'afterUpdate'));

//user
AddEventHandler('main', 'OnBeforeUserRegister', array('\Collected\Handlers\User', 'beforeAdd'));
AddEventHandler('main', 'OnBeforeUserUpdate', array('\Collected\Handlers\User', 'beforeUpdate'));

//highload blocks
$eventManager->addEventHandler('', 'UserDataOnUpdate', array('\Collected\Handlers\UserData', 'afterUpdate'));
$eventManager->addEventHandler('', 'UserDataOnAdd', array('\Collected\Handlers\UserData', 'afterAdd'));