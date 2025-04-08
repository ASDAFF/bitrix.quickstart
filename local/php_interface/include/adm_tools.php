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
\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array("'\Helper\UserProp\CUserTypeYesNo", "GetUserTypeDescription"), 50);

\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('\Helper\UserProp\Store', 'GetPropertyDescription'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('\Helper\UserProp\Price', 'GetPropertyDescription'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('\Helper\UserProp\Group', 'GetPropertyDescription'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('\Helper\UserProp\Iblock', 'GetPropertyDescription'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('\Helper\UserProp\IblockProperty', 'GetPropertyDescription'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array('\Helper\UserProp\IblockPropertyEnum', 'GetPropertyDescription'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("catalog", "OnGroupUpdate", array('\Helper\UserProp\Price', 'OnGroupUpdate'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("catalog", "OnGroupDelete", array('\Helper\UserProp\Price', 'OnGroupDelete'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("catalog", "OnGroupAdd", array('\Helper\UserProp\Price', 'OnGroupAdd'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("catalog", "OnCatalogStoreUpdate", array('\Helper\UserProp\Store', 'OnCatalogStoreUpdate'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("catalog", "OnCatalogStoreDelete", array('\Helper\UserProp\Store', 'OnCatalogStoreDelete'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("catalog", "OnCatalogStoreAdd", array('\Helper\UserProp\Store', 'OnCatalogStoreAdd'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnAfterGroupUpdate", array('\Helper\UserProp\Group', 'OnGroupUpdate'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnGroupDelete", array('\Helper\UserProp\Group', 'OnGroupDelete'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnAfterGroupAdd", array('\Helper\UserProp\Group', 'OnGroupAdd'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler('iblock', 'OnAfterIBlockUpdate', array('\Helper\UserProp\Iblock', 'OnIblockUpdate'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler('iblock', 'OnIBlockDelete', array('\Helper\UserProp\Iblock', 'OnIblockDelete'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler('iblock', 'OnAfterIBlockAdd', array('\Helper\UserProp\Iblock', 'OnIblockAdd'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler('iblock', 'OnBeforeIBlockPropertyAdd', array('\Helper\UserProp\IblockProperty', 'OnBeforeIBlockPropertyAdd'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler('iblock', 'OnBeforeIBlockPropertyUpdate', array('\Helper\UserProp\IblockProperty', 'OnBeforeIBlockPropertyUpdate'));

\Bitrix\Main\EventManager::getInstance()->addEventHandler('iblock', 'OnBeforeIBlockPropertyDelete', array('\Helper\UserProp\IblockProperty', 'OnBeforeIBlockPropertyDelete'));