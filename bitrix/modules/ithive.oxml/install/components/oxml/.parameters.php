<?
if(!CModule::IncludeModule("iblock"))
	return;
	
	require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
	$arOptions = unserialize(COption::GetOptionString('ithive.oxml', 'options'));
	
	$arParams['OPTIONS'] = $arParams['OPTIONS'] ? $arParams['OPTIONS'] : $arOptions;
	
	$arParams['PRICE_TYPE'] = $arParams['PRICE_TYPE'] ? $arParams['PRICE_TYPE'] : $arOptions['site']['price_type'];
	
	$arParams['IBLOCKS'] = $arParams['IBLOCKS'] ? $arParams['IBLOCKS'] : $arOptions['iblocks'];
	$arParams['SECTIONS'] = $arParams['SECTIONS'] ? $arParams['SECTIONS'] : $arOptions['sections_export'];
	$arParams['PROPERS'] = $arParams['PROPERS'] ? $arParams['PROPERS'] : $arOptions['property_export'];
	$arParams['SKUPROP'] = $arParams['SKUPROP'] ? $arParams['SKUPROP'] : $arOptions['skuprops_export'];
	$arParams['MORE_PHOTO'] = $arParams['MORE_PHOTO'] ? $arParams['MORE_PHOTO'] : $arOptions['more_photo'];
		
	$dbResult = CIblock::GetList(
		Array("SORT"=>"ASC"),
		Array("TYPE"=>"catalog", "ID" => $arParams['IBLOCKS']),
		false
	);
	
	while($arRes = $dbResult->Fetch()) {
		$arIblockId[$arRes['ID']] = $arRes['NAME'];
	}
	
	function path($id){
		$nav = CIBlockSection::GetNavChain(false, $id);
		while($arNav = $nav->GetNext())	$path .= " / ".$arNav["NAME"];
		return $path;
	}
		
	$arIBlockSection[0] = GetMessage('ALL');
	foreach($arParams['IBLOCKS'] as $iblock) {
		$arFilter = Array("IBLOCK_ID" => $iblock);
		$count = CIBlockSection::GetCount($arFilter);
		if ($count < 1) {
		} else {
			$rsIBlockSection = CIBlockSection::GetList(Array("sort" => "asc"), Array("IBLOCK_ID" => $iblock, "ACTIVE"=>"Y", "INCLUDE_SUBSECTIONS" => "Y"), true);	
			while($arr = $rsIBlockSection->Fetch())
			{
				$arIBlockSection[$arr["ID"]] .= '['.$arr['IBLOCK_TYPE_ID'].'] '.path($arr['ID']);
			}
		}
	}	
	natsort($arIBlockSection);
	
	$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("IBLOCK_ID" => $arParams['IBLOCKS'], "ACTIVE"=>"Y"));
	
	$arIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("IBLOCK_ID" => $arParams['IBLOCKS'], "ACTIVE"=>"Y"));
	
	while ($arIb = $rsIBlock->Fetch()) {
		$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arIb['ID'], "USER_TYPE" => "SKU"));
		while($arProperty  =  $dbProperty->Fetch()) $arSKUProps['PROPERTY_'.$arProperty['CODE']] = 'PROPERTY_'.$arProperty['CODE'];
	}

	$arProps[0] = GetMessage('ALL');
	while($arr = $arIBlock->Fetch())
	{
		$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arr['ID']));
		while($arProperty = $dbProperty->Fetch()) {
			if ($arProperty['CODE'] == 'CML2_LINK') {
				$arSKUProps['PROPERTY_'.$arProperty['CODE']] = 'PROPERTY_'.$arProperty['CODE'];
				continue;
			}
			if ($arProperty["PROPERTY_TYPE"] == "F") continue;
			
			$arProps[$arr['CODE'].'_'.$arProperty['CODE']] = "[{$arr['CODE']}] [{$arProperty['CODE']}] {$arProperty['NAME']}";
		}
		
		$dbMore = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arr["ID"], "PROPERTY_TYPE" => "F"));
		while($arMore = $dbMore->Fetch()) $arProp[$arMore['CODE']] = "{$arMore['CODE']}";
	}
		
	natsort($arProps);
	
	CModule::IncludeModule("catalog");
	$dbPriceType = CCatalogGroup::GetList(
		array("SORT" => "ASC")
	);
	while($arPriceType = $dbPriceType->Fetch()) {
		$arPrices[$arPriceType['ID']] = '['.$arPriceType['NAME'].'] '.$arPriceType['NAME_LANG'];
	}

$arComponentParameters = array(
	"GROUPS" => array(
		"SITE" => array(
			"NAME" => GetMessage("SITE_GROUP_NAME")
		),
		"SKU" => array(
			"NAME" => GetMessage("SKU_GROUP_NAME")
		),
	),
	
	"PARAMETERS" => array(
				
		"IBLOCKS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE_LIST"),
			"TYPE" => "LIST",			
			"VALUES" => $arIblockId,
			"DEFAULT" => $arParams['IBLOCKS'],
			"MULTIPLE" => "Y",
            "REFRESH" => "Y",
		),
		
		"SECTIONS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_SECTION"),
			"TYPE" => "LIST",			
			"VALUES" => $arIBlockSection,
			"MULTIPLE" => "Y",
			"DEFAULT" => $arParams['SECTIONS'],
			"SIZE" => 10,
		),
		
		"PROPERS" => array (
			"PARENT" => "BASE",
			"NAME" => GetMessage("SECTION_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES"	=> $arProps,
			"DEFAULT"	=> $arParams['PROPERS']
		),
		
		"MORE_PHOTO" => array (
			"PARENT" => "BASE",
			"NAME" => GetMessage("MORE_PHOTO"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES"	=> $arProp,
			"DEFAULT"	=> $arParams['MORE_PHOTO']
		),
				
		"PRICE_TYPE" => array (
			"PARENT" => "SITE",
			"NAME" => GetMessage("PRICE_TYPE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES"	=> $arPrices,
			"DEFAULT"	=> $arParams['PRICE_TYPE']
		),
		
		
		"SKUPROPS" => array (
			"PARENT" => "SKU",
			"NAME" => GetMessage("SKU_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arSKUProps,
			"DEFAULT" => $arParams['SKUPROPS']
		),
		
		
		"CACHE_TIME"  =>  Array("DEFAULT" => 3600),
	),
);
?>