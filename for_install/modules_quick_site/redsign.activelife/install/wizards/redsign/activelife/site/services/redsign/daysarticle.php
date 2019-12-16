<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$module_id = 'redsign.daysarticle2';

if($obModule = CModule::CreateModuleObject($module_id)){
	if(!$obModule->IsInstalled()){
		$obModule->InstallDB();
		$obModule->InstallEvents();
		$obModule->InstallOptions();
		$obModule->InstallFiles();
		$obModule->InstallPublic();
	}
}

if (
    WIZARD_INSTALL_DEMO_DATA &&
    CModule::IncludeModule('iblock') &&
    CModule::IncludeModule('redsign.daysarticle2')
) {
    // take some N iblock_elements
    $arFilterIBlocks = array(
        array(
            'IBLOCK_TYPE' => 'catalog',
            'IBLOCK_CODE' => 'catalog',
            'IBLOCK_XML_ID' => 'catalog_'.WIZARD_SITE_ID,
        ),
        array(
            'IBLOCK_TYPE' => 'catalog',
            'IBLOCK_CODE' => 'offers',
            'IBLOCK_XML_ID' => 'offers_'.WIZARD_SITE_ID,
        ),
    );

    foreach($arFilterIBlocks as $arFilterIBlock)
    {
        $rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $arFilterIBlock['IBLOCK_TYPE'], 'CODE' => $arFilterIBlock['IBLOCK_CODE'], 'XML_ID' => $arFilterIBlock['IBLOCK_XML_ID'] ));
        if ($arIBlock = $rsIBlock->Fetch())
        {
            $arrIBlockIDs[$arFilterIBlock['IBLOCK_CODE']] = $arIBlock['ID'];
        }
    }

    $arFilterElementsDA2 = array(
        'catalog' => array(
            'vetrovka_asics' => array(
                'DISCOUNT' => 1100,
                'QUANTITY' => 10,
                'DINAMICA' => "custom",
                'DINAMICA_DATA' => array(15=>10, 30=>15, 75=>25),
            ),
        ),
    );

    $arOrder = array('SORT' => 'ASC');
    $index = 0;
    $time = time();
    foreach($arFilterElementsDA2 as $sCatalogCode => $arIBlock)
    {
        $arElementsCode = array();
        foreach($arIBlock as $sElementCode => $arElementDA2)
        {
            $arElementsCode[] = $sElementCode;
        }
        $arRes = CIBlockElement::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arrIBlockIDs[$sCatalogCode], 'CODE' => $sElementCode));
        while($arElement = $arRes->GetNext())
        {
            $insert = 24*60*60;
            $arFields = array(
                'ELEMENT_ID' => $arElement['ID'],
                'ACTIVE' => 'Y',
                'AUTO_RENEWAL' => 'Y',
                'DATE_FROM' => ConvertTimeStamp(($time), 'FULL', 'ru'),
                'DATE_TO' => ConvertTimeStamp(($time+$insert), 'FULL', 'ru'),
                'DISCOUNT' => $arIBlock[$arElement['CODE']]['DISCOUNT'],
                'QUANTITY' => $arIBlock[$arElement['CODE']]['QUANTITY'],
                'DINAMICA' => $arIBlock[$arElement['CODE']]['DINAMICA'],
                'DINAMICA_DATA' => serialize($arIBlock[$arElement['CODE']]['DINAMICA_DATA']),
            );
            CRSDA2Elements::Add($arFields);
        }
        $index++;
    }
}