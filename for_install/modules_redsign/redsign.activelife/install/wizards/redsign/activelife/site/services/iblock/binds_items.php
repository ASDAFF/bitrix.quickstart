<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;

$arFilterIBlocks = array(
	array(
		'IBLOCK_TYPE' => 'catalog',
		'IBLOCK_CODE' => 'catalog',
		'IBLOCK_XML_ID' => 'catalog_'.WIZARD_SITE_ID,
	),
	array(
		'IBLOCK_TYPE' => 'catalog',
		'IBLOCK_CODE' => 'offers',
		'IBLOCK_XML_ID' => 'offers_'.WIZARD_SITE_ID,
	),
);


$arrFilterElements = array(
	"catalog" => array(
		"stels_navigator_500_long_name_supa_long_name" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"multiklyuch_sks_tom_18",
					"veloshlem_polisport_black_thunder",
				),
			),
		),
		"velosiped_zhenskiy_stern_city_2_0" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"multiklyuch_sks_tom_18",
				),
			),
		),
		"velosiped_stern_city_1_0" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"veloshlem_polisport_black_thunder",
				),
			),
		),
		"trek_skye" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"multiklyuch_sks_tom_18",
					"veloshlem_polisport_black_thunder",
				),
			),
		),
		"velosiped_stern_energy_2_0" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"multiklyuch_sks_tom_18",
					"veloshlem_polisport_black_thunder",
					"merida_juliet_5_v",
					"stels_navigator_600",
					"basseyn_bestway_170x53_sm",
				),
			),
		),
		"stels_miss_6100" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"multiklyuch_sks_tom_18",
					"veloshlem_polisport_black_thunder",
				),
			),
		),
		"vetrovka_asics" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"stels_miss_6100",
					"merida_hardy_5",
					"merida_juliet_5_v",
				),
			),
		),
	),
    
    "additional_banners" => array(
		"maska-vratarya" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "snoubordist",
                ),
            ),
        ),
		"konki-bauer-1x" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "snoubordist",
                ),
            ),
        ),
		"krossovki-nike" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "snoubordist",
                ),
            ),
        ),
		"velosipedist-4" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "velosipedist",
                ),
            ),
        ),
		"velosipedist-3" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "velosipedist",
                ),
            ),
        ),
		"velosipedist-1" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "velosipedist",
                ),
            ),
        ),
		"velosipedist-5" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "velosipedist",
                ),
            ),
        ),
    ),
);



$arrFilterElementIDs = array();
$arElementsUsed = array();
$arrIBlockIDs = array();
foreach($arFilterIBlocks as $arFilterIBlock){
	$rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $arFilterIBlock['IBLOCK_TYPE'], 'CODE' => $arFilterIBlock['IBLOCK_CODE'], 'XML_ID' => $arFilterIBlock['IBLOCK_XML_ID'] ));
	if($arIBlock = $rsIBlock->Fetch()){
		$arrIBlockIDs[$arFilterIBlock['IBLOCK_CODE']] = $arIBlock['ID'];
	}
}
foreach($arrFilterElements as $sCatalogCode1 => $arFilterCatalog1){
	foreach($arFilterCatalog1 as $sElementCode1 => $arFilterElement1){
		$arElementsUsed[$sCatalogCode1][] = $sElementCode1;
		foreach($arFilterElement1 as $sPropertyCode => $arPropertyValue){
			foreach($arPropertyValue as $sCatalogCode2 => $arFilterCatalog2){
				foreach($arFilterCatalog2 as $sElementCode2){
						$arElementsUsed[$sCatalogCode2][] = $sElementCode2;
				}
			}
		}
	}
}
foreach($arElementsUsed as $sCatalogCode => $arCatalogElementsUsed){
	$arElementsUsed[$sCatalogCode] = array_unique($arCatalogElementsUsed);
}
foreach($arElementsUsed as $sCatalogCode => $arCatalogElementsUsed){
$res = CIBlockElement::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arrIBlockIDs[$sCatalogCode], 'CODE' => $arCatalogElementsUsed));
	while($arElement = $res->GetNext()){
		$arElementIDs[$sCatalogCode][$arElement['CODE']] = $arElement['ID'];
	}
}
foreach($arrFilterElements as $sCatalogCode1 => $arFilterCatalog1){
	foreach($arFilterCatalog1 as $sElementCode1 => $arFilterElement1){
		$arFilterProps = array();
		foreach($arFilterElement1 as $sPropertyCode => $arPropertyValue){
			foreach($arPropertyValue as $sCatalogCode2 => $arFilterCatalog2){
				foreach($arFilterCatalog2 as $sElementCode2){
					$arFilterProps[$sPropertyCode][] = $arElementIDs[$sCatalogCode2][$sElementCode2];

				}
			}
		}
		CIBlockElement::SetPropertyValuesEx($arElementIDs[$sCatalogCode1][$sElementCode1], $arrIBlockIDs[$sCatalogCode1],  $arFilterProps);
	}
}