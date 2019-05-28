<?
	// Быстрая галлерея — Галлерея для быстрого показа детальных изображений.
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult = WP::cache(
		array(
			'c_mht_fast_gallery',
			WP::lastUpdate(343)
		),
		WP::time(1, 'h'),
		function(){
			return array(
				'ELEMENTS' => WP::bit(
					array(
						'of' => 'elements',
						'f' => 'IBLOCK_ID=343; ACTIVE=Y',
						'sel' => array(
							'f' => 'DETAIL_PICTURE, NAME'
						),
						'map' => function($d, $f, $p){
							return array(
								'name' => $f['NAME'],
								'image' => CFile::GetPath($f['DETAIL_PICTURE']),
							);
						}
					)
				)
			);
		}
	);

	$this->IncludeComponentTemplate();

?>