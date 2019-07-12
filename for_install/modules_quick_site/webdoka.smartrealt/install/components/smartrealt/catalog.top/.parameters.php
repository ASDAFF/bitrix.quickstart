<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('webdoka.smartrealt'))
{
    ShowError(GetMessage('MODULE_SMARTREALT_NOT_INSTALED'));
    return;
}

$oRubric = new SmartRealt_Rubric();
$rsTypes = $oRubric->GetList(array('Active' => 'Y'), array('Sort' => 'asc'), array('TypeId',));
$arTypes = array('' => GetMessage("R_ALL_TYPES"));
while ($arType = $rsTypes->Fetch())
{
    $arTypes[$arType['TypeId']] = $arType['TypeName'];
}

$arObjectTransactionTypes = array(
    "" => GetMessage('R_ALL'),
    "SALE" => GetMessage('R_SALE'),
    "RENT" => GetMessage('R_RENT'),
    "DAILY_RENT" => GetMessage('R_DAILY_RENT'),
);

$arEstateMarkets = array(
    "" => GetMessage('R_ALL'),
    "PRIMARY" => GetMessage('R_PRIMARY'),
    "SECONDARY" => GetMessage('R_SECONDARY'),
);

$arSectionIds = array(
    '' => GetMessage('R_ALL'),
    1 => GetMessage('B_SECTION_CITY_IN'),
    3 => GetMessage('B_SECTION_CITY_OUT'),
    4 => GetMessage('B_SECTION_COMMERCIAL'),
    6 => GetMessage('B_SECTION_OTHER'),
    );

$arComponentParameters = array(
	'PARAMETERS'	=> array(
        'SHOW_TITLE'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_SHOW_TITLE'),
            'TYPE'      => 'CHECKBOX',
            'DEFAULT'    => 'N'
        ),    
        'TITLE'    => array(
            'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_TITLE'),
            'TYPE'      => 'STRING',
            'MULTIPLE'  => 'N',
            'DEFAULT'    => ''
        ),	
		'COUNT'	=> array(
			'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_COUNT'),
			'TYPE'      => 'STRING',
			'MULTIPLE'  => 'N',
			'DEFAULT'	=> 3
		),
        "TYPE" => array(
            'PARENT'      => 'BASE',
            "NAME" => GetMessage("R_TYPE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arTypes,
        ),
        "TRANSACTION_TYPE" => array(
            'PARENT'      => 'BASE',
            "NAME" => GetMessage("R_TRANSACTION_TYPE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "VALUES" => $arObjectTransactionTypes,
            'DEFAULT'    => 'SALE'
        ),
        "SECTION_ID" => array(
            'PARENT'      => 'BASE',
            "NAME" => GetMessage("R_SECTION_ID"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "VALUES" => $arSectionIds,
            'DEFAULT'    => 'SALE'
        ),
        "ESTATE_MARKET" => array(
            'PARENT'      => 'BASE',
            "NAME" => GetMessage("R_ESTATE_MARKET"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "VALUES" => $arEstateMarkets,
            'DEFAULT'    => 'SALE'
        ),
        "SORT_RAND" => array(
            'PARENT'      => 'BASE',
            "NAME" => GetMessage("R_SORT_RAND"),
            "TYPE" => "CHECKBOX",
            "MULTIPLE" => "N",
            'DEFAULT'    => 'Y'
        ),
		'CATALOG_TOP_LIST_URL'	=> array(
			'PARENT'      => 'BASE',
            'NAME'      => GetMessage('R_CATALOG_TOP_LIST_URL'),
			'TYPE'      => 'STRING',
			'MULTIPLE'  => 'N',
			'DEFAULT'	=> ''
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
		"CACHE_TIME"	=> array(
			"DEFAULT"	=> 3600
		)
	)
); 
?>
