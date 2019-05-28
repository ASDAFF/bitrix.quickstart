<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	echo WP::cache('c_amount_'.$arParams['TYPE'], null, function() use (&$arParams){
		$total = 0;
		switch($arParams['TYPE']){
			case 'PRODUCTS':
				MHT::eachCatalogIBlock(function($iblock) use (&$total){
					$count = CIBlock::GetElementCount($iblock['ID']);
					$total += $count;
				});
				break;

			case 'BRANDS':
				$allProperties = array();
				MHT::eachCatalogIBlock(function($iblock) use (&$allProperties){
					$properties = WP::getListPropertyValues($iblock['ID'], 'CML2_MANUFACTURER');
					if(is_array($properties) && count($properties)){
						foreach ($properties as $p) {
							$allProperties[$p['VALUE']] = 1;
						}
					}
				});
				$total = count($allProperties);
				break;
		}
		return number_format($total, 0, '.', ' ');
	});
?>