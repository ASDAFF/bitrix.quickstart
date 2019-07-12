<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("highloadblock"))
    return;

use Bitrix\Highloadblock as HL; //подключаем модуль

try {
    $data = array(
        "NAME" => "SeoReference",
        "TABLE_NAME" => "ng_seo_reference"
    );
    $hlblock = HL\HighloadBlockTable::add($data);
    $id = $hlblock->getId();
} catch (Exception $e) {
    print $e->getMessage();
    $id = 0;
}

if ($id > 0) {
    $HLBLOCK_ENTITY_ID = "HLBLOCK_" . $id;
    $fields = Array(
        "UF_NAME" => Array(
            "ENTITY_ID" => $HLBLOCK_ENTITY_ID,
            "FIELD_NAME" => "UF_NAME",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_NAME",
            "SORT" => 100,
            "MULTIPLE" => "N",
            "MANDATORY" => "Y",
            "SHOW_FILTER" => "N",
            "SHOW_IN_LIST" => "Y",
            "EDIT_IN_LIST" => "Y",
            "IS_SEARCHABLE" => "N",
            "SETTINGS" => Array(
                "SIZE" => 20,
                "ROWS" => 1,
                "REGEXP" => "",
                "MIN_LENGTH" => 0,
                "MAX_LENGTH" => 0,
                "DEFAULT_VALUE" => ""
            ),
            "USER_TYPE" => Array(
                "USER_TYPE_ID" => "string",
                "CLASS_NAME" => "CUserTypeString",
                "DESCRIPTION" => "String",
                "BASE_TYPE" => "string"
            ),
            "VALUE" => ""
        ),
        "UF_NEW_URL" => Array(
            "ENTITY_ID" => $HLBLOCK_ENTITY_ID,
            "FIELD_NAME" => "UF_NEW_URL",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_NEW_URL",
            "SORT" => 200,
            "MULTIPLE" => "N",
            "MANDATORY" => "N",
            "SHOW_FILTER" => "N",
            "SHOW_IN_LIST" => "Y",
            "EDIT_IN_LIST" => "Y",
            "IS_SEARCHABLE" => "N",
            "SETTINGS" => Array(
                "SIZE" => 20,
                "ROWS" => 1,
                "REGEXP" => "",
                "MIN_LENGTH" => 0,
                "MAX_LENGTH" => 0,
                "DEFAULT_VALUE" => ""
            ),
            "USER_TYPE" => Array(
                "USER_TYPE_ID" => "string",
                "CLASS_NAME" => "CUserTypeString",
                "DESCRIPTION" => "String",
                "BASE_TYPE" => "string"
            ),
            "VALUE" => ""
        ),
        "UF_TITLE" => Array(
            "ENTITY_ID" => $HLBLOCK_ENTITY_ID,
            "FIELD_NAME" => "UF_TITLE",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_TITLE",
            "SORT" => 300,
            "MULTIPLE" => "N",
            "MANDATORY" => "N",
            "SHOW_FILTER" => "N",
            "SHOW_IN_LIST" => "Y",
            "EDIT_IN_LIST" => "Y",
            "IS_SEARCHABLE" => "N",
            "SETTINGS" => Array(
                "SIZE" => 20,
                "ROWS" => 1,
                "REGEXP" => "",
                "MIN_LENGTH" => 0,
                "MAX_LENGTH" => 0,
                "DEFAULT_VALUE" => ""
            ),
            "USER_TYPE" => Array(
                "USER_TYPE_ID" => "string",
                "CLASS_NAME" => "CUserTypeString",
                "DESCRIPTION" => "String",
                "BASE_TYPE" => "string"
            ),
            "VALUE" => ""
        ),
        "UF_KEYWORDS" => Array(
            "ENTITY_ID" => $HLBLOCK_ENTITY_ID,
            "FIELD_NAME" => "UF_KEYWORDS",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_KEYWORDS",
            "SORT" => 400,
            "MULTIPLE" => "N",
            "MANDATORY" => "N",
            "SHOW_FILTER" => "N",
            "SHOW_IN_LIST" => "Y",
            "EDIT_IN_LIST" => "Y",
            "IS_SEARCHABLE" => "N",
            "SETTINGS" => Array(
                "SIZE" => 20,
                "ROWS" => 1,
                "REGEXP" => "",
                "MIN_LENGTH" => 0,
                "MAX_LENGTH" => 0,
                "DEFAULT_VALUE" => ""
            ),
            "USER_TYPE" => Array(
                "USER_TYPE_ID" => "string",
                "CLASS_NAME" => "CUserTypeString",
                "DESCRIPTION" => "String",
                "BASE_TYPE" => "string"
            ),
            "VALUE" => ""
        ),
        "UF_DESCRIPTION" => Array(
            "ENTITY_ID" => $HLBLOCK_ENTITY_ID,
            "FIELD_NAME" => "UF_DESCRIPTION",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_DESCRIPTION",
            "SORT" => 500,
            "MULTIPLE" => "N",
            "MANDATORY" => "N",
            "SHOW_FILTER" => "N",
            "SHOW_IN_LIST" => "Y",
            "EDIT_IN_LIST" => "Y",
            "IS_SEARCHABLE" => "N",
            "SETTINGS" => Array(
                "SIZE" => 20,
                "ROWS" => 1,
                "REGEXP" => "",
                "MIN_LENGTH" => 0,
                "MAX_LENGTH" => 0,
                "DEFAULT_VALUE" => ""
            ),
            "USER_TYPE" => Array(
                "USER_TYPE_ID" => "string",
                "CLASS_NAME" => "CUserTypeString",
                "DESCRIPTION" => "String",
                "BASE_TYPE" => "string"
            ),
            "VALUE" => ""
        ),
        "UF_SITE_ID" => Array(
            "ENTITY_ID" => $HLBLOCK_ENTITY_ID,
            "FIELD_NAME" => "UF_SITE_ID",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_SITE_ID",
            "SORT" => 600,
            "MULTIPLE" => "N",
            "MANDATORY" => "N",
            "SHOW_FILTER" => "N",
            "SHOW_IN_LIST" => "Y",
            "EDIT_IN_LIST" => "Y",
            "IS_SEARCHABLE" => "N",
            "SETTINGS" => Array(
                "SIZE" => 20,
                "ROWS" => 1,
                "REGEXP" => "",
                "MIN_LENGTH" => 0,
                "MAX_LENGTH" => 0,
                "DEFAULT_VALUE" => ""
            ),
            "USER_TYPE" => Array(
                "USER_TYPE_ID" => "string",
                "CLASS_NAME" => "CUserTypeString",
                "DESCRIPTION" => "String",
                "BASE_TYPE" => "string"
            ),
            "VALUE" => ""
        ),
        "UF_SEO_TEXT" => Array(
            "ENTITY_ID" => $HLBLOCK_ENTITY_ID,
            "FIELD_NAME" => "UF_SEO_TEXT",
            "USER_TYPE_ID" => "string",
            "XML_ID" => "UF_SEO_TEXT",
            "SORT" => 700,
            "MULTIPLE" => "N",
            "MANDATORY" => "N",
            "SHOW_FILTER" => "N",
            "SHOW_IN_LIST" => "Y",
            "EDIT_IN_LIST" => "Y",
            "IS_SEARCHABLE" => "N",
            "SETTINGS" => Array(
                "SIZE" => 20,
                "ROWS" => 4,
                "REGEXP" => "",
                "MIN_LENGTH" => 0,
                "MAX_LENGTH" => 0,
                "DEFAULT_VALUE" => ""
            ),
            "USER_TYPE" => Array(
                "USER_TYPE_ID" => "string",
                "CLASS_NAME" => "CUserTypeString",
                "DESCRIPTION" => "String",
                "BASE_TYPE" => "string"
            ),
            "VALUE" => ""
        )
    );
    foreach ($fields as $field) {
        $uu = new CUserTypeEntity();
        $uu->Add($field);
    }
}

?>