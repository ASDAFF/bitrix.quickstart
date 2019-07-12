<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(CModule::IncludeModule('iblock') && CModule::IncludeModule('redsign.daysarticle2')){
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

	foreach($arFilterIBlocks as $arFilterIBlock){
		$rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $arFilterIBlock['IBLOCK_TYPE'], 'CODE' => $arFilterIBlock['IBLOCK_CODE'], 'XML_ID' => $arFilterIBlock['IBLOCK_XML_ID'] ));
		if ($arIBlock = $rsIBlock->Fetch()){
			$arrIBlockIDs[$arFilterIBlock['IBLOCK_CODE']] = $arIBlock['ID'];
		}
	}

	$arFilterElementsDA2 = array(
		'offers' => array(
			'apple_iphone_5s_32_chernyy' => array(
				'DISCOUNT' => 12,
				'VALUE_TYPE' => 'P',
				'CURRENCY' => 'RUB',
				'QUANTITY' => 0,
				'DINAMICA' => "evenly",
				'DINAMICA_DATA' => array(),
			),
			'apple_iphone_4_32_chyernyy' => array(
				'DISCOUNT' => 10,
				'VALUE_TYPE' => 'P',
				'CURRENCY' => 'RUB',
				'QUANTITY' => 0,
				'DINAMICA' => "evenly",
				'DINAMICA_DATA' => array(),
			),
		),
	);

	$arOrder = array('SORT' => 'ASC');
	$index = 0;
	$time = time();
	foreach($arFilterElementsDA2 as $sCatalogCode => $arIBlock){
		$arElementsCode = array();
		foreach($arIBlock as $sElementCode => $arElementDA2){
			$arElementsCode[] = $sElementCode;
		}
		$arRes = CIBlockElement::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arrIBlockIDs[$sCatalogCode], 'CODE' => $sElementCode));
		while($arElement = $arRes->GetNext()){
			$insert = 24*60*60;
			$arFields = array(
				'ELEMENT_ID' => $arElement['ID'],
				'ACTIVE' => 'Y',
				'DATE_FROM' => ConvertTimeStamp(($time), 'FULL', 'ru'),
				'DATE_TO' => ConvertTimeStamp(($time+$insert), 'FULL', 'ru'),
				'DISCOUNT' => $arIBlock[$arElement['CODE']]['DISCOUNT'],
				'VALUE_TYPE' => $arIBlock[$arElement['CODE']]['VALUE_TYPE'],
				'CURRENCY' => $arIBlock[$arElement['CODE']]['CURRENCY'],
				'QUANTITY' => $arIBlock[$arElement['CODE']]['QUANTITY'],
				'DINAMICA' => $arIBlock[$arElement['CODE']]['DINAMICA'],
				'DINAMICA_DATA' => serialize($arIBlock[$arElement['CODE']]['DINAMICA_DATA']),
				'AUTO_RENEWAL' => 'Y',
			);
			CRSDA2Elements::Add($arFields);
		}
		$index++;
	}
}