<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arRes = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
$userProp = array();
if (!empty($arRes))
{
	foreach ($arRes as $key => $val)
		$userProp[$val["FIELD_NAME"]] = (strLen($val["EDIT_FORM_LABEL"]) > 0 ? $val["EDIT_FORM_LABEL"] : $val["FIELD_NAME"]);
}

$arComponentParameters = array(
	"GROUPS" => array(
		"TABS" => array(
		    "NAME" => GetMessage("TABS"),
		    "SORT" => "150",
	    ),
	    "PERSONAL" => array(
		    "NAME" => GetMessage("PERSONAL_TAB_CAPTION"),
		    "SORT" => "170",
	    ),
	    "ORDERS" => array(
		    "NAME" => GetMessage("ORDERS_TAB_CAPTION"),
		    "SORT" => "175",
	    ),
	    "ADDRESES" => array(
		    "NAME" => GetMessage("ADDRESES_TAB_CAPTION"),
		    "SORT" => "180",
	    ),
	),
	"PARAMETERS" => array(
		// Настройки меню
		"ORDERS" => Array(
			"PARENT" => "TABS",
			"NAME" => GetMessage("ORDERS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		),
		"USER_ADDRESES" => Array(
			"PARENT" => "TABS",
			"NAME" => GetMessage("USER_ADDRESES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"LOGOUT" => Array(
			"PARENT" => "TABS",
			"NAME" => GetMessage("LOGOUT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		// Настройки компонента личной информации
		"USER_PROPERTY"=>array(
			"PARENT" => "PERSONAL",
			"NAME" => GetMessage("USER_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $userProp,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),
		),
		"SEND_INFO"=>array(
			"PARENT" => "PERSONAL",
			"NAME" => GetMessage("SEND_INFO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CHECK_RIGHTS"=>array(
			"PARENT" => "PERSONAL",
			"NAME" => GetMessage("CHECK_RIGHTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		// Настройки компонента заказов
		"PATH_TO_COPY" => Array(
			"NAME" => GetMessage("SPOL_PATH_TO_COPY"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "ORDERS",
		),
		"PATH_TO_CANCEL" => Array(
			"NAME" => GetMessage("SPOL_PATH_TO_CANCEL"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "ORDERS",
		),
		"PATH_TO_BASKET" => Array(
			"NAME" => GetMessage("SPOL_PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "ORDERS",
		),
		"ORDERS_PER_PAGE" => Array(
			"NAME" => GetMessage("SPOL_ORDERS_PER_PAGE"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => 20,
			"PARENT" => "ORDERS",
		),
		"SAVE_IN_SESSION" => array(
			"PARENT" => "ORDERS",
			"NAME" => GetMessage("SPOL_SAVE_IN_SESSION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"NAV_TEMPLATE" => array(
			"PARENT" => "ORDERS",
			"NAME" => GetMessage("SPOL_NAV_TEMPLATE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"PATH_TO_PAYMENT" => Array(
			"NAME" => GetMessage("SPOD_PATH_TO_PAYMENT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "payment.php",
			"COLS" => 25,
			"PARENT" => "ORDERS",
		),
		// Настройки компонента Адресов
		"PER_PAGE_ADR" => Array(
			"NAME" => GetMessage("SPPL_PER_PAGE"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => 20,
			"PARENT" => "ADDRESES",
		),
		'USE_AJAX_LOCATIONS' => array(
			'NAME' => GetMessage("SPPD_USE_AJAX_LOCATIONS"),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'N',
			"PARENT" => "ADDRESES",
		),
		"SET_TITLE" => array(),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),

	),
);
?>