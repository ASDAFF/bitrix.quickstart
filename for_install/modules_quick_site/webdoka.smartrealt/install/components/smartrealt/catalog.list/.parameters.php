<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true )die();

$arComponentParameters = array(
	'PARAMETERS'	=> array(
        'SET_TITLE'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_SHOW_TITLE'),
            'TYPE'      => 'CHECKBOX',
            'DEFAULT'    => 'N'
        ),
        'COUNT_ON_PAGE'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_COUNT_ON_PAGE'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '20'
        ),    
        'TITLE'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_TITLE'),
            'TYPE'      => 'STRING',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => ''
        ),	
		"CACHE_TIME"	=> array(
			"DEFAULT"	=> 3600
		)
	)
); 
?>
