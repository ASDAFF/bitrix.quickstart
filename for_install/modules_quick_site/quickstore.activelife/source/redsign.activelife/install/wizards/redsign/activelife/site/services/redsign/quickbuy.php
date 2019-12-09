<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$module_id = 'redsign.quickbuy';

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
    CModule::IncludeModule('redsign.quickbuy')
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
    $arrElementsQB = array(
        'catalog' => array(
            'butsy_muzhskie_nike_5_bomba' => array(
                'DISCOUNT' => 1700,
                'QUANTITY' => 10,
                'ACTIVE' => 'Y'
            ),
        ),
        'offers' => array(
            'rolikovye_konki_detskie_razdvizhnye_roces_moody_1_1_boy' => array(
                'DISCOUNT' => 600,
                'QUANTITY' => 14,
                'ACTIVE' => 'Y'
            ),
            'palatka_nordway_ascona_5' => array(
                'DISCOUNT' => 700,
                'QUANTITY' => 8,
                'ACTIVE' => 'Y'
            ),
        ),
    );

    $arOrder = array('SORT' => 'ASC');
    $index = 0;
    $time = time();
    foreach($arrElementsQB as $sCatalogCode => $arIBlock)
    {
        $sElementsQBCode = array();
        foreach($arIBlock as $sElementQBCode => $arElementQB)
        {
            $sElementsQBCode[] = $sElementQBCode;
        }
        $arRes = CIBlockElement::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arrIBlockIDs[$sCatalogCode], 'CODE' => $sElementsQBCode));
        while($arElement = $arRes->GetNext())
        {
            $insert = 24*60*60*(15+$index);
            $arIBlock[$arElement['CODE']]['ELEMENT_ID'] = $arElement['ID'];
            $arIBlock[$arElement['CODE']]['DATE_FROM'] = ConvertTimeStamp(($time), 'FULL', 'ru');;
            $arIBlock[$arElement['CODE']]['DATE_TO'] = ConvertTimeStamp(($time+$insert), 'FULL', 'ru');
            CRSQUICKBUYElements::Add($arIBlock[$arElement['CODE']]);
            $index++;
        }
    }
}