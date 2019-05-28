<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult['BANNERS'] = WP::cache(
		'c_big_banners_',
		WP::time(3, 's'),
		function(){
			return WP::bit(array(
				'of' => 'elements',
				'f' => 'IBLOCK_ID=129; ACTIVE=Y'.(isset($_GET['promo']) ? '; ID='.intval($_GET['promo']) : ''),
				'sort' => 'RAND',
				'sel' => array(
					'f' => array(
						'IBLOCK_ID',
						'ID'
					),
					'p' => array(
						'IMAGE_FG',
						'IMAGE_BG',
						'HEIGHT',
						'LINK',
						'PHONES_SHADOW',
						'PHONES_COLOR'
					)
				),
				'max'=>isset($arParams['ELEMENTS_COUNT'])?$arParams['ELEMENTS_COUNT']:3,
				'map' => function($d, $f, $p){
					if($_GET['a']){
						WP::log(func_get_args());
					}

					$IMAGE_SIZES = array(
						"BG_WIDTH" => 1920,
						"BG_HEIGHT" => 825,
						"FG_WIDTH" => 990,
						"FG_HEIGHT" => 300
					);

					$images = array();
					
					$small = CFile::ResizeImageGet($p['IMAGE_BG']['VALUE'], array(
						'width' => $IMAGE_SIZES['BG_WIDTH'],
						'height' => $IMAGE_SIZES['BG_HEIGHT']
					), BX_RESIZE_IMAGE_EXACT, true, Array(
						"name" => "sharpen", 
						"precision" => 0
					));

					$images['back'] = array(
						'small' => array(
							'src' => $small['src'],
							'width' => $small['width'],
							'height' => $small['height'],
						)
					);

					$small = CFile::ResizeImageGet($p['IMAGE_FG']['VALUE'], array(
						'width' => $IMAGE_SIZES['FG_WIDTH'],
						'height' => $IMAGE_SIZES['FG_HEIGHT']
					), BX_RESIZE_IMAGE_PROPORTIONAL, true, Array(
						"name" => "sharpen", 
						"precision" => 0
					));

					$images['front'] = array(
						'small' => array(
							'src' => $small['src'],
							'width' => $small['width'],
							'height' => $small['height'],
						)
					);

					/* 
					foreach(array(
						array('back', 'BG'),
						array('front', 'FG')
					) as $a){
						list($i, $j) = $a;
						$id = $p['IMAGE_'.$j]['VALUE'];
						if(!$id){
							continue;
						}
						//$big = CFile::GetFileArray($id);

						$big = CFile::ResizeImageGet($id, array(
							'width' => $IMAGE_SIZES['BG_WIDTH'],
							'height' => $IMAGE_SIZES['BG_HEIGHT']
						), BX_RESIZE_IMAGE_EXACT, true);

						$small = $big;

						$small = CFile::ResizeImageGet($id, array(
							'width' => $big['WIDTH'] / 2,
							'height' => $big['HEIGHT'] / 2
						), BX_RESIZE_IMAGE_PROPORTIONAL, true, 
						Array(
							"name" => "sharpen", 
							"precision" => 0
						   ));
						

						$images[$i] = array(
							'big' => array(
								'src' => $big['src'],
								'width' => $big['width'],
								'height' => $big['height'],
							),
							'small' => array(
								'src' => $small['src'],
								'width' => $small['width'],
								'height' => $small['height'],
							)
						);
					}
					*/


					$result = array(
						'images' => $images,
						'link' => $p['LINK']['VALUE'],
						'iblock' => $f['IBLOCK_ID'],
						'id' => $f['ID'],
						'height' => 0, //$p['HEIGHT']['VALUE'],
						'phones' => array(
							'shadow' => $p['PHONES_SHADOW']['VALUE'] == 'Y',
							'color' => $p['PHONES_COLOR']['VALUE'],
						)
					);


					return $result;
				}
			));
		}
	);

	$this->IncludeComponentTemplate();
?>