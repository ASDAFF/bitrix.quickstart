<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php
$APPLICATION->IncludeComponent("bitrix:sale.personal.order.cancel", ".default", array(
        "PATH_TO_LIST" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["orders"],
        "PATH_TO_DETAIL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["orders"],
        "SET_TITLE" => "N",
        "ID" => $_REQUEST["ID"]
    ),
    $component
);
?>