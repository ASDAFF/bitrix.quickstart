<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult['TREE'] = WP::cache(array(
		'c_catalog_menu____',
		$arParams['SECTION_ID'],
		$arParams['IBLOCK_ID'],
		WP::lastUpdate(null, null),
		WP::lastUpdate($arParams['IBLOCK_ID'], 'section'),
		floor(time() / 3600)
	), null, function() use (&$arParams){
	
		$iblocks = array();
		MHT::eachCatalogIBlock(function($iblock) use (&$iblocks){
			$iblocks[] = array(
				'id'           => -1 * $iblock['ID'],
				'parent'       => 0,
				'id_'          => $iblock['ID'],
				'name'         => $iblock['NAME'],
				'link'         => $iblock['LIST_PAGE_URL'],
				'section_path' => $iblock['SECTION_PAGE_URL'],
				'sort'         => $iblock['SORT']
			);
		});

		$items = array();

		global $APPLICATION;
		$dir = $APPLICATION->GetCurDir();

		foreach($iblocks as $iblock){
			$active = false;
			if(
				(
					isset($arParams['IBLOCK_ID']) &&
					$arParams['IBLOCK_ID'] == $iblock['id']
				) ||
				strpos($dir, $iblock['link']) !== false
			){
				$active = true;
			}
			$iblock['active'] = $active;
			$items[$iblock['id']] = $iblock;

			if(!$active){
				continue;
			}

			WP::bit(array(
				'of' => 'sections',
				'sort' =>  array(
					'SORT'=>"ASC",
					'NAME' => 'ASC'
				),
				'filter' => 'iblock='.$iblock['id_'].'; SECTION_ID=0; ACTIVE=Y',
				'each' => function($d, $f) use (&$items, &$iblock, &$dir, &$arParams){
					$link = $f['SECTION_PAGE_URL'];
					$items[$f['ID']] = array(
						'id' => $f['ID'],
						'parent' => $iblock['id'],
						'name' => trim($f['NAME']),
						'link' => $link,
						'sort' => $f['SORT'],
						'active' => (
							isset($arParams['SECTION_ID']) &&
							$arParams['SECTION_ID'] == $f['ID']
						)
					);

					
					$parent = $f;

					WP::sections(array(
						'sort' => array(
							'SORT'=>"ASC",
							'NAME' => 'ASC'
						),
						'filter' => array(
							'IBLOCK_ID' => $iblock['id_'],
							'SECTION_ID' => $f['ID'],
							'ACTIVE' => 'Y'
						),
						'each' => function($f) use (&$items, &$parent, &$arParams, $iblock){
							$items[$f['ID']] = array(
								'id' => $f['ID'],
								'parent' => $parent['ID'],
								'name' => trim($f['NAME']),
								'link' => $f['SECTION_PAGE_URL'],
								'active' => (
									isset($arParams['SECTION_ID']) &&
									$arParams['SECTION_ID'] == $f['ID']
								)
							);


                            $parent2 = $f;

                            WP::sections(array(
                                'sort' => array(
                                    'SORT'=>"ASC",
                                    'NAME' => 'ASC'
                                ),
                                'filter' => array(
                                    'IBLOCK_ID' => $iblock['id_'],
                                    'SECTION_ID' => $f['ID'],
                                    'ACTIVE' => 'Y'
                                ),
                                'each' => function($ff) use (&$items, &$parent2, &$arParams){
                                    $items[$ff['ID']] = array(
                                        'id' => $ff['ID'],
                                        'parent' => $parent2['ID'],
                                        'name' => trim($ff['NAME']),
                                        'link' => $ff['SECTION_PAGE_URL'],
                                        'active' => (
                                            isset($arParams['SECTION_ID']) &&
                                            $arParams['SECTION_ID'] == $ff['ID']
                                        )
                                    );
                                }
                            ));


						}
					));
					

				}
			));

			// $items = WP::sortBy($items, 'name');

		}

		$tree = WP::treeFromArray($items);
		/*usort($tree, function($a, $b){
		 	$av = $a['element']['name'];
		 	$bv = $b['element']['name'];
		 	$cmp = strcmp($av, $bv);
		 	return $cmp;
		});*/
		return $tree;
	});
	$this->IncludeComponentTemplate();
?>
