<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	$arResult = WP::cache(array(
		'c_catalog_categories_',
		$arParams['TYPE'],
		$arParams['ID'],
		WP::lastUpdate(null, null),
	), null, function() use(&$arParams){
		$id = $arParams['ID'];
		$items = array();
		$title = '';
		$each = function($f) use (&$items){
			$items[] = array(
				'name' => $f['NAME'],
				'link' => $f['SECTION_PAGE_URL'],
				'image' => CFile::GetPath($f['PICTURE'])
			);
		};

		switch($arParams['TYPE']){
			case 'IBLOCKS':
				$blocks = array();
				MHT::eachCatalogIBlock(function($iblock) use (&$items){
					$items[] = array(
						'name' => $iblock['NAME'],
						'link' => $iblock['LIST_PAGE_URL'],
						'image' => CFile::GetPath($iblock['PICTURE'])
					);
				});
				$title = 'Каталог';
				break;

			case 'IBLOCK':
			
				WP::sections(array(
					'filter' => array(
						'IBLOCK_ID' => $id,
						'SECTION_ID' => 0
					),
					'each' => $each,
					'sort' => array(
						'SORT'=>'ASC',
						'NAME' => 'ASC'
					)
				));
				$title = CIBlock::GetList(array(), array('ID' => $id))->Fetch();
				$title = $title['NAME'];
				break;

			default:
				WP::sections(array(
					'filter' => array(
						'IBLOCK_ID' => $arParams['IBLOCK_ID'],
						'SECTION_ID' => $id
					),
					'each' => $each
				));
				$title = CIBlockSection::GetList(array(), array('ID' => $id))->GetNext();
				$title = $title['NAME'];
				break;
		}

		return array(
			'ITEMS' => $items,
			'NAME' => $title
		);
	});
	$this->IncludeComponentTemplate();
?>