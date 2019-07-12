<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true )die();

$arComponentParameters = array(
    'PARAMETERS'    => array(
        'SET_TITLE'    => array(
            'NAME'      => GetMessage('R_SET_TITLE'),
            'TYPE'      => 'CHECKBOX',
            'DEFAULT'    => 'N'
        ),
        'NUMBER'    => array(
            'NAME'      => GetMessage('R_NUMBER'),
            'TYPE'      => 'STRING',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => ''
        ),
        'TYPE_CODE'    => array(
            'NAME'      => GetMessage('R_TYPE_CODE'),
            'TYPE'      => 'STRING',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => ''
        ),    
        'TRANSACTION_TYPE'    => array(
            'NAME'      => GetMessage('R_TRANSACTION_TYPE'),
            'TYPE'      => 'STRING',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => ''
        ),    
        'CATALOG_LIST_URL'    => array(
            'NAME'      => GetMessage('R_CATALOG_LIST_URL'),
            'TYPE'      => 'STRING',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => ''
        ),
        
        "CACHE_TIME"    => array(
            "DEFAULT"    => 3600
        )
    )
); 
?>
