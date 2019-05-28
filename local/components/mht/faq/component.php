<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult = WP::cache('c_faq1', WP::time(1, 'm'), function(){
		$sections = array();
		$sectionIDs = array();
		WP::sections(array(
			'filter' => array(
				'IBLOCK_ID' => 57,
				'ACTIVE' => 'Y'
			),
			'each' => function($section) use (&$sections, &$sectionIDs){
				$id = $section['ID'];
				$sections[$id] = array(
					'name' => $section['NAME']
				);
				$sectionIDs[] = $id;
			}
		));

		WP::elements(array(
			'filter' => array(
				'IBLOCK_ID' => 57,
				'SECTION_ID' => $sectionIDs,
				'ACTIVE' => 'Y'
			),
			'each' => function($f, $p) use (&$sections){
				$sections[$f['IBLOCK_SECTION_ID']]['elements'][] = array(
					'q' => $p['QUESTION']['~VALUE']['TEXT'],
					'a' => $p['ANSWER']['~VALUE']['TEXT']	
				);
			}
		));

		return array(
			'SECTIONS' => $sections
		);
	});

	$this->IncludeComponentTemplate();
?>