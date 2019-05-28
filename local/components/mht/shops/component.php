<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$region = null;
	if(isset($_GET['shop'])){
		$region = WP::get('region')->byCode($_GET['shop']);
	}
	if($region == null){
		$region = WP::get('region')->cur();
	}


	$arResult = WP::cache(array(
		'c_shops',
		$region->prop('code')
	), null, function() use (&$region){
		return array(
			'SHOPS' => WP::bit(array(
				'of' => 'elements',
				'f' => 'ACTIVE=Y; iblock=74',
				'sel' => array(
					'f' => '
						ID,
						DETAIL_PICTURE,
						PREVIEW_PICTURE,
                        DETAIL_PAGE_URL
					',
					'p' => '
						STREET,
						HOUSE,
						TIME_1,
						TIME_2,
						TIME_3,
						PANORAM,
						COORDS,
						IMAGES,
						REGION_IN,
						REGION_OUT,
						IS_COMING_SOON,
						PHONES,
                        SUBWAY,
                        SUBWAY_COLOR,
                        ADM_OKRUG,
                        HOW_TO_REACH
					'
				),
				'map' => function(&$d, $f, $p) use (&$region){
					if($region->excluded_p($p)){
						$d['event']['skip'] = true;
						return;
					}

					$images = array();
					$image = $f['DETAIL_PICTURE'] ? $f['DETAIL_PICTURE'] : ($f['PREVIEW_PICTURE'] ? $f['PREVIEW_PICTURE'] : false);
					if($image){
						$images[] = $image;
					}

					if(!empty($p['IMAGES']['VALUE'])){
						foreach($p['IMAGES']['VALUE'] as $image){
							$images[] = $image;
						}
					}

					foreach($images as $i => $image){
						$images[$i] = CFile::GetPath($image);
					}

					$link = '/magaziny/';
					if(isset($_GET['shop'])){
						$link .= '?shop='.$region->prop('code');
					}
					$link .= '#shop'.$f['ID'];

					$isShowPanoram = $p['PANORAM']['VALUE'] == 'Y';

					return array(
						'id'           => $f['ID'],
						'detail_page_url'           => $f['DETAIL_PAGE_URL'],
						'street'       => $p['STREET']['VALUE'],
						'house'        => $p['HOUSE']['VALUE'],
						'isComingSoon' => $p['IS_COMING_SOON']['VALUE'] == 'Y',
						'time1'         => $p['TIME_1']['VALUE'],
						'time2'         => $p['TIME_2']['VALUE'],
						'time3'         => $p['TIME_3']['VALUE'],
						'phones'       => $p['PHONES']['VALUE'],
						'link'         => $link,
						'panoram'      => $isShowPanoram,
						'images'       => $images,
						'house_html'   => preg_replace('/(\d+)/', '<span class="dealer_build_number">$1</span>', $p['HOUSE']['VALUE']),
						'coords'       => array_map('trim', explode(',', $p['COORDS']['VALUE'])),
                        'subway'       => $p['SUBWAY']['VALUE'],
                        'subway_color'       => explode(' ', $p['SUBWAY_COLOR']['VALUE']),
                        'adm_okrug'         => getPropertyEnumCodeById('ADM_OKRUG', getPropertyEnumIdByValue('ADM_OKRUG', $p['ADM_OKRUG']['VALUE'], 74), 74)
                        //'how_to_reach'      => $p['HOW_TO_REACH']['VALUE']['TEXT']
					);
				}
			)),
			'ACTIVE_REGION' => $region->prop('code')
		);
	});

	$this->IncludeComponentTemplate();
?>