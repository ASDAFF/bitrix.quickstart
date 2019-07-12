<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	
	// get preview pictures, property color and property std_size
	// and add this data in arResult['ITEMS']
	CModule::IncludeModule("iblock");
	$arElementID = array();
	foreach($arResult['ITEMS'] as $val)
		$arElementID[] = $val['PRODUCT_ID'];
	if( !empty($arElementID) )
	{
		$rsElement = CIBlockElement::GetList(
			array(),
			array('ID' => $arElementID),
			false,
			false,
			array(
				'IBLOCK_ID',
				'ID',
				'PROPERTY_CML2_LINK',
				'PREVIEW_PICTURE',
				'PROPERTY_COLOR.NAME',
				'PROPERTY_COLOR.PREVIEW_PICTURE',
				'PROPERTY_STD_SIZE.NAME'
			)
		);
		$arProductID = array();
		while($arElement = $rsElement -> Fetch())
		{
			$arProductID[ $arElement['ID'] ] =  $arElement['PROPERTY_CML2_LINK_VALUE'];
			$arMixData[ $arElement['ID'] ] = $arElement;
		}
		if( !empty($arProductID) )
		{
			$rsElement = CIBlockElement::GetList(
				array(),
				array('ID' => $arProductID),
				false,
				false,
				array(
					'IBLOCK_ID',
					'ID',
					'PROPERTY_VENDOR.NAME',
				)
			);
			while($arElement = $rsElement -> Fetch())
				$arMixData['VENDOR'][ $arElement['ID'] ] = $arElement;
		}
		foreach($arResult['ITEMS'] as $key => $val)
		{
			$arResult['ITEMS'][$key]['PREVIEW_PICTURE'] = CFile::GetPath($arMixData[ $val['PRODUCT_ID'] ]['PREVIEW_PICTURE']);
			$arResult['ITEMS'][$key]['CML2_LINK'] = $arMixData[ $val['PRODUCT_ID'] ]['PROPERTY_CML2_LINK_VALUE'];
			$arResult['ITEMS'][$key]['COLOR_NAME'] = $arMixData[ $val['PRODUCT_ID'] ]['PROPERTY_COLOR_NAME'];
			$arResult['ITEMS'][$key]['COLOR_PIC'] = CFile::GetPath($arMixData[ $val['PRODUCT_ID'] ]['PROPERTY_COLOR_PREVIEW_PICTURE']);
			$arResult['ITEMS'][$key]['SIZE'] = $arMixData[ $val['PRODUCT_ID'] ]['PROPERTY_STD_SIZE_NAME'];
			$arResult['ITEMS'][$key]['VENDOR'] = $arMixData['VENDOR'][ $arResult['ITEMS'][$key]['CML2_LINK'] ]['PROPERTY_VENDOR_NAME'];
		}
	}
?>