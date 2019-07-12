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
		"shurupovyert_ryobi_18_volt_one" => array(
			"ACCESSORIES" => array(
				"catalog" => array(
					"dewalt_20_volt_max_li_ion_cordless_reciprocating_saw_tool_only",
					"dewalt_20_volt_max_lithium_ion_circular_saw_tool_only",
					"fairview_6_light_heritage_bronze_chandelier",
					"ok_lighting_25_in_antique_brass_rosie_crystal_ceiling_lamp",
					"milwaukee_m18_18_volt_lithium_ion_cordless_sawzall_reciprocating_saw_tool_only",
					"progress_lighting_richmond_hill_collection_brushed_nickel_5_light_chandelier",
				),
			),
			"SIMILAR_PRODUCTS" => array(
				"catalog" => array(
					"bernzomatic_wh0159_universal_torch_extension_hose",
					"blue_max_16_in_38_cc_high_performance_chainsaw",
					"max_18_in_45_cc_heavy_duty_gas_chainsaw",
					"progress_lighting_richmond_hill_collection_brushed_nickel_5_light_chandelier",
					"sprite_showers_8_spray_filtered_showerhead_in_chrome",
					"shurupovyert-ridgid-18-volt-x4-hyper-lithium-ion",
					"dewalt_18_volt_ni_cad_1_2_in_compact_drill_driver_kit",
				),
			),
		),
	),
	"offers" => array(
		"dewalt_15_amp_12_in_sliding_compound_miter_saw1" => array(
			"CML2_LINK" => array(
				"catalog" => array(
					"dewalt-15-amp-12-in-sliding-compound-miter-saw1",
				),
			),
		),
		"dewalt_15_amp_12_in_sliding_compound_miter_saw12" => array(
			"CML2_LINK" => array(
				"catalog" => array(
					"dewalt-15-amp-12-in-sliding-compound-miter-saw1",
				),
			),
		),
		"dewalt_15_amp_12_in_sliding_compound_miter_saw13" => array(
			"CML2_LINK" => array(
				"catalog" => array(
					"dewalt-15-amp-12-in-sliding-compound-miter-saw1",
				),
			),
		),
		"" => array(
			"CML2_LINK" => array(
				"catalog" => array(
					"shurupovyert_ryobi_18_volt_one",
					"shurupovyert_ryobi_18_volt_one",
					"shurupovyert-ridgid-18-volt-x4-hyper-lithium-ion",
					"shurupovyert-ridgid-18-volt-x4-hyper-lithium-ion",
					"shurupovyert_ryobi_18_volt_one",
				),
			),
		),
	),
	"services" => array(
		"dostavka-i-sborka" => array(
			"RELATED_PRODUCTS" => array(
				"catalog" => array(
					"cleanforce_1800_psi_1_5_gpm_axial_cam_heavy_duty_electric_pressure_washer",
					"cleanforce_1400_psi_1_4_gpm_aluminum_axial_cam_electric_pressure_washer",
					"ridgid_fuego_18_volt_cordless_compact_drill_driver",
					"dewalt_15_amp_12_in_sliding_compound_miter_saw1",
					"dewalt_10_in_jobsite_table_saw_with_rolling_stand1",
					"ryobi_15_amp_10_in_table_saw",
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
