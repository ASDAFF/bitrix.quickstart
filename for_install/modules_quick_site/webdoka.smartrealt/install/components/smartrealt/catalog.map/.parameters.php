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
        'COUNT_ON_PAGE'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_COUNT_ON_PAGE'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => '20'
        ),
        'MAP_ID'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_MAP_ID'),
            'TYPE'      => 'STRING',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => 'smartrealt_map'
        ),
        'MAP_WIDTH'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_MAP_WIDTH'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => 320
        ),
        'MAP_HEIGHT'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_MAP_HEIGHT'),
            'TYPE'      => 'INTEGER',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => 320
        ),
		"CACHE_TIME"	=> array(
			"DEFAULT"	=> 3600
		)
	)
); 
?>
