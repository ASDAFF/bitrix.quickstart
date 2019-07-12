<?
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
        'SET_TITLE'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_SET_TITLE'),
            'TYPE'      => 'CHECKBOX',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => 'Y'
        ),
        'COUNT_ON_PAGE'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_COUNT_ON_PAGE'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '20'
        ),
        'LIST_IMAGE_WIDTH'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_LIST_IMAGE_WIDTH'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '74'
        ),
        'LIST_IMAGE_HEIGHT'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_LIST_IMAGE_HEIGHT'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '56'
        ),
        'DETAIL_IMAGE_MEDIUM_WIDTH'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_DETAIL_IMAGE_MEDIUM_WIDTH'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '290'
        ),
        'DETAIL_IMAGE_MEDIUM_HEIGHT'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_DETAIL_IMAGE_MEDIUM_HEIGHT'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '218'
        ),
        'DETAIL_IMAGE_BIG_WIDTH'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_DETAIL_IMAGE_BIG_WIDTH'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '800'
        ),
        'DETAIL_IMAGE_BIG_HEIGHT'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_DETAIL_IMAGE_BIG_HEIGHT'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '600'
        ),
		"VARIABLE_ALIASES" => Array(
			"TYPE_CODE" => Array("NAME" => GetMessage("TYPE_CODE_DESC")),
            "TRANSACTION_TYPE" => Array("NAME" => GetMessage("TRANSACTION_TYPE_DESC")),
            "NUMBER" => Array("NAME" => GetMessage("NUMBER_DESC")),
		),
		"SEF_MODE" => Array(
			"list" => array(
				"NAME" => GetMessage("R_LIST_PAGE"),
				"DEFAULT" => "",
				"VARIABLES" => array('TYPE_CODE', 'TRANSACTION_TYPE'),
			),
			"element" => array(
				"NAME" => GetMessage("R_ELEMENT_PAGE"),
				"DEFAULT" => "",
				"VARIABLES" => array('TYPE_CODE', 'TRANSACTION_TYPE', "NUMBER"),
			),
		),

	),
);

?>
