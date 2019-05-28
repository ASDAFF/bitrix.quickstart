<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
	preg_match('#/o_kompanii/vakansii/(.*?)/$#', $APPLICATION->GetCurDir(), $m);
	$sectionCode = $m[1];
	
	$arResult = WP::cache(
		array(
			'c_vacancies_section',
			WP::get('region')->cur()->prop('code'),
			$sectionCode,
			WP::lastUpdate(59)
		),
		WP::time(1, 'd'),
		function() use (&$arParams, &$sectionCode){
			
			return WP::bit(array(
				'of' => 'iblock',
				'f' => array(
					'ID' => 59
				),
				'sel' => '
					NAME,
					LIST_PAGE_URL,
					ID
				',
				'one' => function($d, $f) use (&$sectionCode){
					$activeSectionID = null;

					WP::bit(array(
						'of' => 'sections',
						'f' => 'iblock='.$f['ID'].'; ACTIVE=Y; GLOBAL_ACTIVE=Y',
						'sel' => 'CODE, ID',
						'each' => function($d, $f) use (&$activeSectionID, &$sectionCode){
							if($f['CODE'] == $sectionCode){
								$activeSectionID = $f['ID'];
								$d['event']['break'] = true;
							}
						}
					));

					$iblock = array(
						'name' => $f['NAME'],
						'link' => $f['LIST_PAGE_URL'],
						'count' => 0,
						'acitve' => $activeSectionID === null
					);

					$sections = WP::bit(array(
						'of' => 'sections',
						'f' => 'iblock='.$f['ID'].'; ACTIVE=Y; GLOBAL_ACTIVE=Y',
						'sel' => 'ID, NAME, SECTION_PAGE_URL, CODE',
						'map' => function($d, $f) use ($activeSectionID){

							$section = array(
								'id' => $f['ID'],
								'name' => $f['NAME'],
								'link' => $f['SECTION_PAGE_URL'],
								'count' => 0,
								'active' => ($activeSectionID == $f['ID'])
							);

							WP::bit(array(
								'of' => 'elements',
								'f' => 'iblock='.$f['IBLOCK_ID'].'; SECTION_ID='.$section['id'].'; ACTIVE=Y; GLOBAL_ACTIVE=Y',
								'each' => function($d, $f, $p) use (&$section, $activeSectionID){
									if(WP::get('region')->cur()->excluded_p($p)){
										return;
									}

									if($activeSectionID && !$section['active']){
										return;
									}
									/*
									if(!empty($p['REGION_OUT']['VALUE']) && in_array(WP::get('region')->cur()->prop('code'),$p['REGION_OUT']['VALUE'])){
										return;
									}
									
									if(!empty($p['REGION_IN']['VALUE']) && !in_array(WP::get('region')->cur()->prop('code'),$p['REGION_IN']['VALUE'])){
										return;
									}
									*/
									$section['elements'][] = array(
										'name' => $f['NAME'],
										'address' => $p['ADDRESS']['VALUE'],
										'min-price' => $p['MIN_PRICE']['VALUE'],
										'region-in' => $p['REGION_IN']['VALUE'],
										'region-out' => $p['REGION_OUT']['VALUE'],
										'text' => $f['~PREVIEW_TEXT'],
										'id' => $f['ID'],
										'iblock' => $f['IBLOCK_ID']
									);
									
									$section['count']++;
								}
							));

							return $section;
						}
					));

					
					$regioncode = WP::get('region')->cur()->prop('code');
					foreach($sections as $k=>$section){
						if(!empty($section['elements'])){
							foreach($section['elements'] as $k2=>$element){
								if(!empty($element['region-out']) && in_array($regioncode,$element['region-out'])){
									unset($sections[$k]['elements'][$k2]);
								}
								if(!empty($element['region-in']) && !in_array($regioncode,$element['region-in'])){
									unset($sections[$k]['elements'][$k2]);
								}
							}
							if(empty($sections[$k]['elements'])){
								unset($sections[$k]);
							}
						}else{
							unset($sections[$k]);							
						}				
					}
					
					$elements = array();
					foreach($sections as $section){
						$iblock['count'] += $section['count'];
						if(!empty($section['elements'])){
							$elements = array_merge(
								$elements,
								$section['elements']
							);
						}
					}

					$iblock['name'] = 'Все вакансии';

					array_unshift($sections, $iblock);

					return array(
						'SECTIONS' => $sections,
						'ELEMENTS' => $elements
					);
				}
			));
		}
	);

	$this->IncludeComponentTemplate();
?>