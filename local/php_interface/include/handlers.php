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
AddEventHandler('main', 'OnPageStart', 'loadLemaLib', 1);
function loadLemaLib()
{
    \Bitrix\Main\Loader::includeModule('lema.lib');
}

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




/**
 * order
 **/
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd", array('CEshopEmailFieldsHandlers', 'OnBeforeEventAdd'));

/**
 * property types
 **/


// Добавляем фильтр на изображение, если только в CFile::ResizeImageGet в $arFilters есть ключ irf_text
AddEventHandler('main', 'OnAfterResizeImage', Array('ImageResizeFilter', 'add'));


/**
 * user
 **/


/**
 * highload blocks
 **/
