<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

/** @var array $arCurrentValues */
$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "ID_CHAT" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("SOOBWA_COMMENTS_PARAMETERS_ID_CHAT_NAME"),
            "DEFAULT" => 'DEFAULT_'.getdate()[0],
        ),

        "AUTH" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("SOOBWA_COMMENTS_PARAMETERS_AUTH_NAME"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),

        "AUTH_URL" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("SOOBWA_COMMENTS_PARAMETERS_AUTH_URL_NAME"),
        ),

        "ENTRY_URL" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("SOOBWA_COMMENTS_PARAMETERS_ENTRY_URL_NAME"),
        ),

        "MODERATION" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("SOOBWA_COMMENTS_PARAMETERS_MODERATION_NAME"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "COUNT" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("SOOBWA_COMMENTS_PARAMETERS_COUNT_NAME"),
            "DEFAULT" => "5",
        ),

        "CACHE" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("SOOBWA_COMMENTS_PARAMETERS_CACHE_NAME"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),

        "CACHE_TIMES" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("SOOBWA_COMMENTS_PARAMETERS_CACHE_TIMES_NAME"),
            "DEFAULT" => 36000000,
        ),
    ),
);
?>