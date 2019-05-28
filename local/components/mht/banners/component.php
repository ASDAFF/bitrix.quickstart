<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult['BANNERS'] = WP::cache(
		'c_small_banners',
		WP::time(10, 's'),
		function(){
		return WP::bit(array(
			'of' => 'elements',
			'f' => 'iblock=12; ACTIVE=Y',
			'max' => 3,
			'sort' => 'rand',
			'map' => function($d, $f, $p){
				$k = $d['i'];
				$images = array();
				foreach(
					array(
						array('back', 'BG'),
						array('front', 'FG')
					) as $a
				){
					list($i, $j) = $a;
					$id = $p['IMAGE_'.$j]['VALUE'];
					if(!$id){
						continue;
					}
					$small = CFile::ResizeImageGet($id, array(
						'width' => 330,
						'height' => 165
					), BX_RESIZE_IMAGE_EXACT, true, Array(
						"name" => "sharpen", 
						"precision" => 0
					));
					$images[$i] = array(
						'big' => CFile::GetPath($id),
						'small' => $small['src']
					);
				}

				return array(
					'images' => $images,
					'link' => $p['LINK']['VALUE'],
					'iblock' => 12,
					'id' => $f['ID']
				);
			}
		));
	});

	$this->IncludeComponentTemplate();
?>