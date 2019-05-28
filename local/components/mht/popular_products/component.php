<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult['PRODUCTS'] = WP::cache(
		'c_popular_products',
		WP::time(10, 's'),
		function() use ($arParams){
			$ids = array();
			MHT::eachCatalogIBlock(function($iblock) use (&$ids){
				$ids[] = $iblock['ID'];
			});
			shuffle($ids);
			// $ids = array_slice($ids, 0, 3);

			$products = array();

			foreach($ids as $id){
				$iblockProducts = WP::bit(array(
					'of' => 'elements',
					'f' => array(
						'IBLOCK_ID' => $id,
						'ACTIVE' => 'Y',
						'!DETAIL_PICTURE' => false,
                        //'ID' => 675779
					),
					'p' => array(
						'IS_IN_STOCK' => 'Y',
						'!SAYT_NA_GLAVNUYU' => false, // 129522 'SAYT_NA_GLAVNUYU_VALUE' => "Да"
					),
					// 'debug' => $_GET['dbg'],
					'sel' => MHT\Product::getSelect(),
					'sort' => array(
						'ID' => 'ASC'
					),
					'map' => function($d, $f, $p){
						return new MHT\Product($f, $p);
					}
				));

				if(empty($iblockProducts)){
					continue;
				}
				
				$products = array_merge($products, $iblockProducts);
			}

			shuffle($products);
			$products = array_slice($products, 0, 3);
			
			return $products;
		}
	);
	
	$this->IncludeComponentTemplate();
?>