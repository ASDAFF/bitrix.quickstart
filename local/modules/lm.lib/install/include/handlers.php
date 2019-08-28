<?php

$eventManager = \Bitrix\Main\EventManager::getInstance();

//page start
AddEventHandler('main', 'OnPageStart', 'loadLmLib', 1);
function loadLmLib()
{
    \Bitrix\Main\Loader::includeModule('lm.lib');
}

/**
 * @TODO make all handlers
 */


//BASKET
//basket add
AddEventHandler('sale', 'OnBeforeBasketAdd', array('Lm\Handlers\Basket', 'beforeAdd'));
AddEventHandler('sale', 'OnBasketAdd', array('Lm\Handlers\Basket', 'afterAdd'));

//basket update
AddEventHandler('sale', 'OnBeforeBasketUpdate', array('Lm\Handlers\Basket', 'beforeUpdate'));
AddEventHandler('sale', 'OnBasketUpdate', array('Lm\Handlers\Basket', 'afterUpdate'));

// basket delete
AddEventHandler('sale', 'OnBeforeBasketDelete', array('Lm\Handlers\Basket', 'beforeDelete'));
AddEventHandler('sale', 'OnBasketDelete', array('Lm\Handlers\Basket', 'afterDelete'));

//order
AddEventHandler('sale', 'OnOrderAdd', array('Lm\Handlers\Order', 'afterAdd'));
AddEventHandler('sale', 'OnOrderUpdate', array('Lm\Handlers\Order', 'afterUpdate'));

//user
AddEventHandler('main', 'OnBeforeUserRegister', array('\Lm\Handlers\User', 'beforeAdd'));
AddEventHandler('main', 'OnBeforeUserUpdate', array('\Lm\Handlers\User', 'beforeUpdate'));

//highload blocks
$eventManager->addEventHandler('', 'UserDataOnUpdate', array('\Lm\Handlers\UserData', 'afterUpdate'));
$eventManager->addEventHandler('', 'UserDataOnAdd', array('\Lm\Handlers\UserData', 'afterAdd'));