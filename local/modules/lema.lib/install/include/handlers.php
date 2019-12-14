<?php

$eventManager = \Bitrix\Main\EventManager::getInstance();

//page start
AddEventHandler('main', 'OnPageStart', 'loadLemaLib', 1);
function loadLemaLib()
{
    \Bitrix\Main\Loader::includeModule('lema.lib');
}

/**
 * @TODO make all handlers
 */


//BASKET
//basket add
AddEventHandler('sale', 'OnBeforeBasketAdd', array('Lema\Handlers\Basket', 'beforeAdd'));
AddEventHandler('sale', 'OnBasketAdd', array('Lema\Handlers\Basket', 'afterAdd'));

//basket update
AddEventHandler('sale', 'OnBeforeBasketUpdate', array('Lema\Handlers\Basket', 'beforeUpdate'));
AddEventHandler('sale', 'OnBasketUpdate', array('Lema\Handlers\Basket', 'afterUpdate'));

// basket delete
AddEventHandler('sale', 'OnBeforeBasketDelete', array('Lema\Handlers\Basket', 'beforeDelete'));
AddEventHandler('sale', 'OnBasketDelete', array('Lema\Handlers\Basket', 'afterDelete'));

//order
AddEventHandler('sale', 'OnOrderAdd', array('Lema\Handlers\Order', 'afterAdd'));
AddEventHandler('sale', 'OnOrderUpdate', array('Lema\Handlers\Order', 'afterUpdate'));

//user
AddEventHandler('main', 'OnBeforeUserRegister', array('\Lema\Handlers\User', 'beforeAdd'));
AddEventHandler('main', 'OnBeforeUserUpdate', array('\Lema\Handlers\User', 'beforeUpdate'));

//highload blocks
$eventManager->addEventHandler('', 'UserDataOnUpdate', array('\Lema\Handlers\UserData', 'afterUpdate'));
$eventManager->addEventHandler('', 'UserDataOnAdd', array('\Lema\Handlers\UserData', 'afterAdd'));