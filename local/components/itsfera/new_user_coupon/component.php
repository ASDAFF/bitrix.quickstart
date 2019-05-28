<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

GLOBAL $APPLICATION,$USER;

if (!$USER->IsAuthorized()) {
    $this->IncludeComponentTemplate();
}

