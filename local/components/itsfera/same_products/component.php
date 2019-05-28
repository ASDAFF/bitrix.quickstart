<?
//Компонент кастомизирован из компонента mht:same_products
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult['SAME_PRODUCTS'] = WP::cache(
		'c_same_products_'.implode('-',$arParams),
		3600,
		function() use ($arParams){
			$result = array();
			WP::elements(array(
				'filter' => array(
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'SECTION_ID' => $arParams['SECTION_ID'],
					'!ID' => $arParams['ELEMENT_ID'],
					'ACITVE' => 'Y',
					'>catalog_PRICE_1' => 0
				),
				'sort' => array(
					'RAND' => 'Y'
				),
				'each' => function($f, $p, $i) use (&$result,$arParams){
					$result[] = new MobileCatalog($f, $p);
					if($i == ($arParams['ELEMENTS_COUNT']-1)){
						return false;
					}
				}
			));
			return $result;
		}
	);

	$this->IncludeComponentTemplate();
?>