<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

$arComponentDescription = array(
    "NAME" => Loc::getMessage("SOOBWA_COMMENTS_DESCRIPTION_NAME"),
    "DESCRIPTION" => Loc::getMessage("SOOBWA_COMMENTS_DESCRIPTION_DESCRIPTION"),
    "SORT" => 20,
    "CACHE_PATH" => "N",
    "PATH" => array(
        "ID" => "soobwa",
        "NAME" => Loc::getMessage("SOOBWA_COMMENTS_DESCRIPTION_PATH_NAME"),
    ),
);
?>