<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
if ($arParams["LIST_TITLE"]) {
    ?>
    <h2><?= $arParams["LIST_TITLE"] ?></h2>
    <?
}

// список пользователей
$APPLICATION->IncludeComponent(
    "site:users.list",
    "",
    Array(
        "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
        'FILTER_NAME' => $arParams["FILTER_NAME"],
        'SORT_BY' => $arParams["SORT_BY"],
        'SORT_ORDER' => $arParams["SORT_ORDER"],
        'FIELDS' => $arParams["FIELDS"],
        'AJAX_ID' => $arParams["LIST_AJAX_ID"],
        'USERS_COUNT' => $arParams["USERS_COUNT"],
        "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
        "LIST_SHOW_PHOTO" => $arParams["LIST_SHOW_PHOTO"],
        "LIST_LINK_DETAIL" => $arParams["LIST_LINK_DETAIL"],
        "FILTER_USER_FIELD" => $arParams["FILTER_USER_FIELD"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "SHOW_PAGER" => $arParams["LIST_SHOW_PAGER"],
        "SEF_FOLDER" => $arParams["SEF_FOLDER"]
    ),
    $component
);
