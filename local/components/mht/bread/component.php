<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$name = 'c_bread';
	foreach(array(
		'TYPE',
		'IBLOCK_ID',
		'SECTION_ID',
		'ID',
		'UNIQUE'
	) as $i){
		if(!strlen($arParams[$i])){
			continue;
		}
		$name .= '_'.$arParams[$i];
	}

	$arResult['LINKS'] = WP::cache(
		$name,
		null,
		function() use (&$arParams){
			$links = array();

			if(in_array($arParams['TYPE'], array('element', 'section'))){
				$links[] = array('Каталог', '/catalog/');
				if($iblock = CIBlock::GetList(array(), array(
					'ID' => $arParams['IBLOCK_ID']
				))->Fetch()){
					$links[] = array($iblock['NAME'], $iblock['LIST_PAGE_URL']);
				}

				$list = CIBlockSection::GetNavChain(
					$arParams['IBLOCK_ID'],
					$arParams['SECTION_ID'],
					array('NAME', 'SECTION_PAGE_URL')
				);
				while(($section = $list->GetNext()) !== false){
					$links[] = array($section['NAME'], $section['SECTION_PAGE_URL']);
				}
			}

			switch($arParams['TYPE']){
				case 'element':
					WP::element(array(
						'filter' => array(
							'ID' => $arParams['ID']
						),
						'each' => function($element) use (&$links){
							$links[] = array($element['NAME'], '');
						}
					));
					break;
			}

			$result = array();
			foreach($links as $a){
				$result[] = array(
					'name' => $a[0],
					'link' => $a[1]
				);
			}

			$result[count($result) - 1]['last'] = true;

			return $result;
		}
	);


	$this->IncludeComponentTemplate();
?>