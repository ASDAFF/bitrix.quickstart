<?
/**
 * Bitrix vars
 *
 * @var array     $arParams
 * @var array     $arResult
 *
 * @var CDatabase $DB
 * @var CUser     $USER
 * @var CMain     $APPLICATION
 *
 * @global API_SALE_MODULE_ID
 */
if(!$USER->IsAdmin())
    return false;

Class CApiPrint{}