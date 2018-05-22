<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)	die();

if(CModule::IncludeModule('redsign.grupper') && CModule::IncludeModule('iblock')){
	// take some N iblock_properties
	$arrFilter1 = array(
		array(
			'IBLOCK_TYPE' => '1c_catalog',
			'IBLOCK_CODE' => 'catalog',
			'IBLOCK_XML_ID' => 'catalog_'.WIZARD_SITE_ID,
		),
	);
	
	foreach($arrFilter1 as $filter1){
		$rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $filter1['IBLOCK_TYPE'], 'CODE' => $filter1['IBLOCK_CODE'], 'XML_ID' => $filter1['IBLOCK_XML_ID'] ));
		if($arIBlock = $rsIBlock->Fetch()){
			$code1 = $filter1['IBLOCK_CODE'];
			$arrIBlockIDs[$code1] = $arIBlock['ID'];
		}
	}
	
	$arrGroups = array(
		array(
			'NAME' => GetMessage('GROUP_NAME_1'),
			'CODE' => 'OBSHCHIE_KHARAKTERISTIKI',
			'SORT' => '100',
			'BINDS' => array('YEAR', 'BRAND', 'OS', 'HEIGHT', 'TICKNESS', 'WIDTH'),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_2'),
			'CODE' => 'EKRAN',
			'SORT' => '200',
			'BINDS' => array('DIAGONAL', 'SOLUTION'),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_3'),
			'CODE' => 'ZVONKI',
			'SORT' => '300',
			'BINDS' => array(),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_4'),
			'CODE' => 'MULTIMEDIYNYE_VOZMOZHNOSTI',
			'SORT' => '400',
			'BINDS' => array('CARD'),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_5'),
			'CODE' => 'SVYAZ',
			'SORT' => '500',
			'BINDS' => array('INTERNET_ACCESS', 'INTERFACES', 'NAVI'),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_6'),
			'CODE' => 'PAMYAT_I_PROTSESSOR',
			'SORT' => '600',
			'BINDS' => array(),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_7'),
			'CODE' => 'SOOBSHCHENIYA',
			'SORT' => '700',
			'BINDS' => array(),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_8'),
			'CODE' => 'PITANIE',
			'SORT' => '800',
			'BINDS' => array(),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_9'),
			'CODE' => 'DRUGIE_FUNKTSII',
			'SORT' => '900',
			'BINDS' => array(),
		),
		array(
			'NAME' => GetMessage('GROUP_NAME_10'),
			'CODE' => 'DOPOLNITELNAYA_INFORMATSIYA',
			'SORT' => '1000',
			'BINDS' => array('CML2_ARTICLE', 'WEIGHT', 'YEARS'),
		),
	);
	
	foreach($arrGroups as $arGroup){
		$arFields = array(
			'NAME' => trim(htmlspecialchars($arGroup['NAME'])),
			'CODE' => trim(htmlspecialchars($arGroup['CODE'])),
			'SORT' => trim(htmlspecialchars($arGroup['SORT'])),
		);
		$ID = CRSGGroups::Add($arFields);
		if(IntVal($ID)>0){
			foreach($arGroup['BINDS'] as $propCode){
				$arOrder = array('sort'=>'asc','name'=>'asc');
				$arFilter = array('ACTIVE'=>'Y','IBLOCK_ID'=>$arrIBlockIDs['catalog'],'CODE'=>$propCode);
				$resProp = CIBlockProperty::GetList($arOrder,$arFilter);
				if($arProperty = $resProp->GetNext()){
					//CRSGBinds::DeleteBindsForGroupID($ID);
					$arFieldsBind = array(
						'IBLOCK_PROPERTY_ID' => $arProperty['ID'],
						'GROUP_ID' => $ID,
					);
					$BIND_ID = CRSGBinds::Add($arFieldsBind);
				}
			}
		}
	}
}
?>