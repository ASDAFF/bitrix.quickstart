<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult['TREE'] = WP::cache('c_we_will_help', null, function(){
		CModule::IncludeModule('iblock');
		$list = CIBlockElement::GetList(array('SORT' => 'ASC'), array(
			'IBLOCK_ID' => 8,
			'ACITVE' => 'Y'
		));

		$elements = array();
		while(($element = $list->GetNextElement()) !== false){
			$f = $element->GetFields();
			$p = $element->GetProperties();

			$parent = $p['PARENT']['VALUE'];
			if(!$parent){
				$parent = 0;
			}

			$id = $f['ID'];
			$elements[$id] = array(
				'id' => $id,
				'iblock' => 8,
				'name' => $f['NAME'],
				'link' => $p['LINK']['VALUE'],
				'desc' => $p['INFO']['VALUE'],
				'sort' => $f['SORT'],
				'parent' => $parent
			);
		}

		foreach($elements as $element){
			if(!isset($elements[$element['parent']])){
				continue;
			}
			$elements[$element['parent']]['childed'] = true;
		}

		$result = array();

		WP::mapTree(
			WP::treeFromArray($elements, 0, 'parent', 'id', 'sort'),
			function($element, $parentData) use (&$result){
				if($parentData){
					$depth = $parentData['depth'];
				}
				else{
					$depth = 0;
				}

				$result[$depth][] = $element;

				return array(
					'depth' => $depth + 1
				);
			}
		);

		return $result;
	});


	$this->IncludeComponentTemplate();
?>