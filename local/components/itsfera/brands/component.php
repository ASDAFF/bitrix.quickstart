<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$sectionCode = WP::matchDir('/brands/*/', 0);
	$isPredefined = (@$arParams['IS_PREDEFINED_BRANDS'] == 'Y');
	$cacheInfo = array(
		'c_brandsitsfera',
		$sectionCode,
		$isPredefined,
	);


	if($isPredefined){
		MHT::eachCatalogIBlock(function($f) use (&$cacheInfo){
			$cacheInfo[] = WP::lastUpdate($f['ID']);
		});
	}

	$cacheInfo[] = CustomCML2::getLastUpdateTime();


	$arResult = WP::cache(
		$cacheInfo,
		null,
		function() use (&$sectionCode, &$path, &$arParams, &$isPredefined){
			$brands = array();
			$categories = array();
			$categoryIDs = array();

			$activeCategory = null;
			MHT::eachCatalogIBlock(function($iblock) use (&$brands, &$categories, &$sectionCode, &$path, &$activeCategory, &$categoryIDs){
				$categoryIDs[] = $iblock['ID'];
				$code = $iblock['CODE'];
				$category = array(
					'id' => $iblock['ID'],
					'name' => $iblock['NAME'],
					'code' => $code,
					'link' => $path.$code.'/',
					'full-link' => $iblock['LIST_PAGE_URL']
				);
				if($sectionCode == $code){
					$activeCategory = $category;
					$category['active'] = true;
				}
				$categories[$iblock['ID']] = $category;
			});

			if($activeCategory !== null){
				global $APPLICATION;
				$APPLICATION->AddChainItem($activeCategory['name'], $activeCategory['link']);
			}

			if($isPredefined){

				$brandGroups = \WP::bit(array(
					'of' => 'e',
					'f' => array(
						'IBLOCK_ID' => 441,
						'ACTIVE' => 'Y'
					),
					'map' => function($e, $f, $p){
						return array(
							'id' => $f['ID'],
							'name' => $f['NAME'],
							'brands' => array_map('strtolower', $p['BRANDS']['VALUE'])
						);
					}
				));


				$brandNames = array();

				foreach($brandGroups as $group){
					$brandNames = array_merge($brandNames, $group['brands']);
				}

				array_unique($brandNames);
			}

			foreach(($activeCategory === null ? $categoryIDs : array($activeCategory['id'])) as $id){
				$category = $categories[$id];
				$properties = WP::getListPropertyValues($id, 'CML2_MANUFACTURER');
				if(is_array($properties) && count($properties)){
					foreach($properties as $p){
						$value = $p['VALUE'];
						$name = trim($value);
						$lname = strtolower($name);
						if(
							in_array($lname, array(
								'бибер',
								'eco mist',
								'tikkurila',
								'tefal'
							)) ||
							(
								!empty($brandNames) &&
								!in_array($lname, $brandNames)
							)
						){
							continue;
						}
						$brands[] = array(
							'name' => $name,
							'letter' => mb_strtoupper(mb_substr($name, 0, 1)),
							'brand-link' => '/brand/'.strtolower($value).'/',
							'link' => WP::getSmartFilterName(array(
								'id' => $p['ID'],
								'property' => $p['PROPERTY_ID'],
								'full' => '?'
							)),
							'iblock-id' => $id,
							'value' => $p['VALUE'],
							'category' => $category['name'],
							'category_id' => $category['id'],
						);
					}
				}
			}

			WP::sortBy($brands, 'name');

			// combine same names
			$prev_i = null;
			foreach($brands as $cur_i => &$brand_){
				if(
					$prev_i !== null &&
					$brand_['name'] == $brands[$prev_i]['name']
				){
					$brands[$prev_i]['childs'][] = $brand_;
					$brand_['inactive'] = true;
					continue;
				}
				$prev_i = $cur_i;
			}
			unset($brand_);

			foreach($brands as $brandIndex => $brand){

				$iblocks = array($brand['iblock-id']);
				foreach ($brand['childs'] as $brandchild) {
					$iblocks[] = $brandchild['iblock-id'];
				}

				/*
					// Неработающий блок кода
					$iblocks = empty($brand['childs'])
					? array($brand['iblock-id'])
					: array_map(
						function($child){
							return $child['iblock-id'];
						},
						$brand['childs']
					);
				*/

				foreach($iblocks as $iblock){
					$id = \WP::bit(array(
						'of' => 'element',
						'f' => array(
							'IBLOCK_ID' => $iblock,
							'ACTIVE' => 'Y'
						),
						'p' => array(
							'CML2_MANUFACTURER_VALUE' => $brand['value']
						),
						// 'debug' => true,
						'sel' => 'ID',
						'one' => 'f.ID'
					));

					$id = intval($id);
					if($id > 0){
						break;
					}
				}
				if($id > 0){
					continue;
				}

				unset($brands[$brandIndex]);
			}


			if(@$arParams['IS_GROUP_BY_LETTERS'] == 'Y'){
				// group by letters
				$brands_ = array();
				foreach($brands as $brand){
					if($brand['inactive']){
						continue;
					}
					$brands_[$brand['letter']][] = $brand;
				}

				$brands = $brands_;
			}

			if($isPredefined){
				foreach($brandGroups as $i => $group){
					foreach($group['brands'] as $j =>$brandName){
						$addedNames = array();
						foreach($brands as $brand_){
							if(
								in_array($brand_['name'], $addedNames) ||
								strtolower($brand_['name']) != strtolower($brandName)
							){
								continue;
							}
							$addedNames[] = $brand_['name'];
							$brandGroups[$i]['brands_'][] = $brand_;
						}
					}
					$brandGroups[$i]['brands'] = $brandGroups[$i]['brands_'];
				}
				$brands = $brandGroups;
			}



			return array(
				'BRANDS' => $brands,
				'CATEGORIES' => $categories
			);
		}
	);

	$this->IncludeComponentTemplate();
?>