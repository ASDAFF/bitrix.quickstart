<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"PARAMETERS" => array(
        "SET_STATUS_404" => Array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("CATALOG_STATUS_404"),
	        "TYPE" => "CHECKBOX",
	        "DEFAULT" => "N",
        ),
        "VARIABLE_ALIASES" => Array(
            "ELEMENT_ID" => Array("NAME" => GetMessage("CATALOG_SECTION_ELEMENT_ID")),
            "ELEMENT_CODE" => Array("NAME" => GetMessage("CATALOG_SECTION_ELEMENT_CODE")),
            "SUB_ELEMENT_ID" => Array("NAME" => GetMessage("CATALOG_SUB_SECTION_ELEMENT_ID")),
            "SUB_ELEMENT_CODE" => Array("NAME" => GetMessage("CATALOG_SUB_SECTION_ELEMENT_CODE")),
        ),
		"SEF_MODE" => Array(
			"parent_page_index" => array(
				"NAME" => GetMessage("CATALOG_SECTION_INDEX_NAME"),
				"DEFAULT" => "/parent_page_index/",
				"VARIABLES" => array("ELEMENT_CODE"),
			),
			"parent_page_detail" => array(
				"NAME" => GetMessage("CATALOG_SECTION_DETAIL_NAME"),
				"DEFAULT" => "/parent_page_index/#ELEMENT_CODE#/",
				"VARIABLES" => array("ELEMENT_CODE"),
			),
			"child_page_index" => array(
				"NAME" => GetMessage("CATALOG_SUB_SECTION_INDEX_NAME"),
				"DEFAULT" => "/parent_page_index/#ELEMENT_CODE#/child_page_index/",
				"VARIABLES" => array("ELEMENT_CODE"),
			),
			"child_page_detail" => array(
				"NAME" => GetMessage("CATALOG_SUB_SECTION_DETAIL_NAME"),
				"DEFAULT" => "/parent_page_index/#ELEMENT_CODE#/child_page_index/#SUB_ELEMENT_CODE#/",
				"VARIABLES" => array("ELEMENT_CODE", "SUB_ELEMENT_CODE"),
			),
		),
	),
);