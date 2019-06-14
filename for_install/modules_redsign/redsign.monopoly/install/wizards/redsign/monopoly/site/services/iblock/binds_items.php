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
		"servelat_slivochnyy_p_k_1s_300_400g_yatran" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"folgers_medium_classic_roast_ground_coffee",
					"absent_ksenta_70_0_2_l_italiya",
					"bri_select_slivochnyy_125g",
				),
			),
		),
	),
    
    "additional_banners" => array(
		"tovar-kolbasa" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "tovary",
                ),
            ),
        ),
		"tovar-kokot" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "tovary",
                ),
            ),
        ),
		"tovar-syr" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "tovary",
                ),
            ),
        ),
		"tovar-babek" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "tovary",
                ),
            ),
        ),
		"tovar-kofe" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "tovary",
                ),
            ),
        ),
		"kategoriya-morskie-delikatesy" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "kategorii",
                ),
            ),
        ),
		"tovar-napitki" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "kategorii",
                ),
            ),
        ),
		"tovar-molochka" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "kategorii",
                ),
            ),
        ),
		"kategorii-myaso" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "kategorii",
                ),
            ),
        ),
		"kategorii-chai" => array(
			"SUPER_BANNER" => array(
				"banners" => array(
                    "kategorii",
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