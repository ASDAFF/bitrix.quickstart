<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	
	"PARAMETERS" => array(
    	"LINK" => array(
    		"PARENT" => "BASE",
    		"NAME" => GetMessage('LINK'),
    		"TYPE" => "STRING",
    		"MULTIPLE" => "N",
    		"DEFAULT" => "",
    		"REFRESH" => "Y",
    	),
    	"USE_JQUERY" => array(
    		"PARENT" => "SECTIONS",
    		"NAME" => GetMessage('USE_JQUERY'),
    		"TYPE" => "CHECKBOX",
	    	"MULTIPLE" => "N",
    		"DEFAULT" => "Y"
    	),
        "USE_AJAXFORM" => array(
    		"PARENT" => "SECTIONS",
    		"NAME" => GetMessage('USE_AJAXFORM'),
    		"TYPE" => "CHECKBOX",
    		"MULTIPLE" => "N",
    		"DEFAULT" => "Y"
    	),
        "USE_FANCYBOX" => array(
    		"PARENT" => "SECTIONS",
    		"NAME" => GetMessage('USE_FANCYBOX'),
    		"TYPE" => "CHECKBOX",
    		"MULTIPLE" => "N",
    		"DEFAULT" => "Y"
    	),
        "BASKET_CONTEINER" => array(
    		"PARENT" => "BASE",
    		"NAME" => GetMessage('BASKET_CONTEINER'),
    		"TYPE" => "STRING",
    		"MULTIPLE" => "N",
    		"DEFAULT" => "#basket-container",
    		"REFRESH" => "Y",
    	),
        "FORM_SELECTOR" => array(
    		"PARENT" => "BASE",
    		"NAME" => GetMessage('FORM_SELECTOR'),
    		"TYPE" => "STRING",
    		"MULTIPLE" => "N",
    		"DEFAULT" => ".add2basket_form",
    		"REFRESH" => "Y",
    	),
        "BASKET_URL" => array(
    		"PARENT" => "BASE",
    		"NAME" => GetMessage('BASKET_URL'),
    		"TYPE" => "STRING",
    		"MULTIPLE" => "N",
    		"DEFAULT" => "/personal/cart/",
    		"REFRESH" => "Y",
    	),
        "INPUT_SELECTOR" => array(
    		"PARENT" => "BASE",
    		"NAME" => GetMessage('INPUT_SELECTOR'),
    		"TYPE" => "STRING",
    		"MULTIPLE" => "N",
    		"DEFAULT" => "[name='actionADD2BASKET']",
    		"REFRESH" => "Y",
    	),
         "INPUT_TEXT" => array(
    		"PARENT" => "BASE",
    		"NAME" => GetMessage('INPUT_TEXT'),
    		"TYPE" => "STRING",
    		"MULTIPLE" => "N",
    		"DEFAULT" => GetMessage('INPUT_TEXT_VALUE'),
    		"REFRESH" => "Y",
    	),
	),
    
    
);

?>