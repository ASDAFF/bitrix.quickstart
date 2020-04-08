<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$arSorts = array("ASC" => GetMessage("USERS_LIST_DESC_ASC"), "DESC" => GetMessage("USERS_LIST_DESC_DESC"));
$arSortFields = array(
    "ID" => GetMessage("USERS_LIST_FIELDS_ID"),
    "TITLE" => GetMessage("USERS_LIST_FIELDS_NAME"),
    "UF_SORT" => GetMessage("USERS_LIST_FIELDS_SORT"),
);

$arComponentParameters = array(
    "PARAMETERS" => array(
        "PAGE_ELEMENT_COUNT" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_LIST_PAGE_ELEMENT_COUNT"),
            "TYPE" => "STRING",
            "DEFAULT" => "15",
        ),
        "LIST_AJAX_ID" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_LIST_AJAX_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => "users_block",
        ),
        "USERS_COUNT" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_LIST_USERS_COUNT"),
            "TYPE" => "STRING",
            "DEFAULT" => "999",
        ),
        "SORT_BY" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("USERS_LIST_SORT_BY"),
            "TYPE" => "LIST",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("USERS_LIST_SORT_ORDER"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y"
        ),
        "FIELDS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_LIST_FIELDS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => array(
                "ID" => GetMessage("USERS_LIST_FIELDS_ID"),
                "NAME" => GetMessage("USERS_LIST_FIELDS_NAME"),
                "LAST_NAME" => GetMessage("USERS_LIST_FIELDS_LAST_NAME"),
                "PERSONAL_PHOTO" => GetMessage("USERS_LIST_FIELDS_PERSONAL_PHOTO"),
                "PERSONAL_CITY" => GetMessage("USERS_LIST_FIELDS_CITY")
            ),
            "ADDITIONAL_VALUES" => "Y",
        ),
        "DETAIL_FIELDS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_DETAIL_FIELDS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => array(
                "ID" => GetMessage("USERS_LIST_FIELDS_ID"),
                "NAME" => GetMessage("USERS_LIST_FIELDS_NAME"),
                "LAST_NAME" => GetMessage("USERS_LIST_FIELDS_LAST_NAME"),
                "PERSONAL_PHOTO" => GetMessage("USERS_LIST_FIELDS_PERSONAL_PHOTO"),
                "PERSONAL_CITY" => GetMessage("USERS_LIST_FIELDS_CITY")
            ),
            "ADDITIONAL_VALUES" => "Y",
        ),
        'FILTER_NAME' => array(
            'NAME' => GetMessage('USERS_LIST_FILTER_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'arrFilter',
            'PARENT' => 'BASE',
        ),
        'LIST_TITLE' => array(
            'NAME' => GetMessage('USERS_LIST_LIST_TITLE'),
            'TYPE' => 'STRING',
            'PARENT' => 'BASE',
        ),
        "SET_ELEMENT_TITLE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_DETAIL_SET_ELEMENT_TITLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
        ),
        "ADD_ELEMENT_CHAIN" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_DETAIL_ADD_ELEMENT_CHAIN"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
        ),
        "LIST_LINK_DETAIL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_LIST_SHOW_LINK_DETAIL"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
        ),
        "LIST_SHOW_PHOTO" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_LIST_SHOW_PHOTO"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        ),
        "LIST_SHOW_PAGER" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_LIST_SHOW_PAGER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        ),
        "SEF_MODE" => Array(),
        "CACHE_TIME" => array(
            "DEFAULT" => 36000000
        )
    )
);
?>
