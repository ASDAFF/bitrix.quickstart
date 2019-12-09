<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(CModule::IncludeModule('iblock') && CModule::IncludeModule('redsign.quickbuy')){
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
		if($arIBlock = $rsIBlock->Fetch()){
			$arrIBlockIDs[$arFilterIBlock['IBLOCK_CODE']] = $arIBlock['ID'];
		}
	}
	$arrElementsQB = array(
		'catalog' => array(
			'voxtel_idect_eclipse' => array(
				'ACTIVE' => 'Y',
				'DISCOUNT' => 15,
				'VALUE_TYPE' => 'P',
				'CURRENCY' => 'RUB',
				'QUANTITY' => 0,
				'AUTO_RENEWAL' => 'Y',
			),
			'vertu_signature_s_design_ladies_mother_of_pearl' => array(
				'ACTIVE' => 'Y',
				'DISCOUNT' => 10,
				'VALUE_TYPE' => 'P',
				'CURRENCY' => 'RUB',
				'QUANTITY' => 0,
				'AUTO_RENEWAL' => 'Y',
			),
		),
		'offers' => array(
			'apple_iphone_5s_64_zolotoy' => array(
				'ACTIVE' => 'Y',
				'DISCOUNT' => 2000,
				'VALUE_TYPE' => 'F',
				'CURRENCY' => 'RUB',
				'QUANTITY' => 0,
				'AUTO_RENEWAL' => 'Y',
			),
		),
	);

	$arOrder = array('SORT' => 'ASC');
	$index = 0;
	$time = time();
	foreach($arrElementsQB as $sCatalogCode => $arIBlock){
		$sElementsQBCode = array();
		foreach($arIBlock as $sElementQBCode => $arElementQB){
			$sElementsQBCode[] = $sElementQBCode;
		}
		$arRes = CIBlockElement::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arrIBlockIDs[$sCatalogCode], 'CODE' => $sElementsQBCode));
		while($arElement = $arRes->GetNext()){
			$insert = 24*60*60*(15+$index);
			$arIBlock[$arElement['CODE']]['ELEMENT_ID'] = $arElement['ID'];
			$arIBlock[$arElement['CODE']]['DATE_FROM'] = ConvertTimeStamp(($time), 'FULL', 'ru');;
			$arIBlock[$arElement['CODE']]['DATE_TO'] = ConvertTimeStamp(($time+$insert), 'FULL', 'ru');
			CRSQUICKBUYElements::Add($arIBlock[$arElement['CODE']]);
			$index++;
		}
	}
}