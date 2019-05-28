<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult['SAME_PRODUCTS'] = WP::cache(
		'21c_same_products_'.$arParams['IBLOCK_ID'].'_'.$arParams['SECTION_ID'].'_'.$arParams['ELEMENT_ID'],
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
				'each' => function($f, $p, $i) use (&$result){
					$result[] = new MHT\Product($f, $p);
					if($i == 5){
						return false;
					}
				}
			));
			return $result;
		}
	);

	$this->IncludeComponentTemplate();
?>
