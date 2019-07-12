<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die(); IncludeModuleLangFile(__FILE__);?>
<?
//if(($APPLICATION -> GetCurPage() != '/manager/'))
//if( ERROR_404 != "Y" )
if(CModule::IncludeModule("currency"))
{
	$arCurrency = CCurrencyLang::GetCurrencyFormat(
		CCurrency::GetBaseCurrency(), 
 		LANGUAGE_ID
	);
	$arParams['CURRENCY_FORMAT'] = $arCurrency;
	switch( $arCurrency['THOUSANDS_VARIANT'] )
	{
		case "N" : $arParams['CURRENCY_FORMAT']['THOUSANDS_SEP'] = ""; break;
		case "D" : $arParams['CURRENCY_FORMAT']['THOUSANDS_SEP'] = "."; break;
		case "C" : $arParams['CURRENCY_FORMAT']['THOUSANDS_SEP'] = ","; break;
		case "S" : $arParams['CURRENCY_FORMAT']['THOUSANDS_SEP'] = " "; break;
		case "B" : $arParams['CURRENCY_FORMAT']['THOUSANDS_SEP'] = " "; break;
		default: $arParams['CURRENCY_FORMAT']['THOUSANDS_SEP'] = $arCurrency['THOUSANDS_SEP']; break;
	}
	if( mb_strtolower(LANG_CHARSET) != "utf-8" )
		foreach($arParams['CURRENCY_FORMAT'] as $key => $val)
			$arParams['CURRENCY_FORMAT'][ $key ] = iconv(LANG_CHARSET, "UTF-8", $val);
		
}
if(CModule::IncludeModule("iblock"))
{

	// Initialize the initial parameters
	
	$rsIBlock = CIBlock::GetList(
		array(),
		array('CODE' => $arParams['CATALOG_IBLOCK_CODE'],'SITE_ID' => SITE_ID),
		false
	);
	while ($arIBlock = $rsIBlock -> Fetch()) {
		$arParams['CATALOG_IBLOCK_ID'] = $arIBlock['ID'];
		break;
	}
	
	$rsIBlock = CIBlock::GetList(
		array(),
		array('CODE' => $arParams['TRADEOF_IBLOCK_CODE'],'SITE_ID' => SITE_ID),
		false
	);
	while ($arIBlock = $rsIBlock -> Fetch()) {
		$arParams['TRADEOF_IBLOCK_ID'] = $arIBlock['ID'];
		break;
	}
	
	$arParams['CURRENT_SECTION_ID'] = $_REQUEST['secid'];
	// get maximun depth sections
	$rsSection = CIBlockSection::GetTreeList(
		array(
			'IBLOCK_ID'		=> $arParams['CATALOG_IBLOCK_ID'],
			'GLOBAL_ACTIVE'	=> "Y",
		)
	);
	$arResult['MAX_DEPTH_LEVEL'] = 0;
	while($arSection = $rsSection -> Fetch())
	{
		if( $arResult['MAX_DEPTH_LEVEL'] < $arSection['DEPTH_LEVEL'] )
			$arResult['MAX_DEPTH_LEVEL'] = $arSection['DEPTH_LEVEL'];
	}
	
	// analysis of the POST / GET request to the catalog
	if( isset($_REQUEST['arFilter']) )
	foreach($_REQUEST['arFilter'] as $val)
		foreach($val as $subkey => $subval)
			$arResult['arFilterRequest'][($subkey)][] = $subval;
	if( isset($_REQUEST['arOffer']) )
	foreach($_REQUEST['arOffer'] as $val)
		foreach($val as $subkey => $subval)
			$arResult['arOfferRequest'][($subkey)][] = $subval;
	
	// Remember SECTION_CODE of current section in the catalog, also getting SECTION_ID
	
	if(!empty($_REQUEST['secid']))
	{
		$arResult['CURRENT_SECTION_CODE'] = $_REQUEST['secid'];
		$arFilter['SECTION_CODE'] = $arResult['CURRENT_SECTION_CODE'];
		$arFilter = array(
			'IBLOCK_ID'				=> $arParams['CATALOG_IBLOCK_ID'],
			'GLOBAL_ACTIVE'			=> "Y",
			'CODE'					=> $arResult['CURRENT_SECTION_CODE'],
			'ELEMENT_SUBSECTIONS'	=> "N"
		);
		$rsSection = CIBlockSection::GetList(
			array('SORT'	=> "ASC"),
			$arFilter,
			true
		);
		if($arSection = $rsSection -> Fetch())
			$arResult['CURRENT_SECTION_ID'] = $arSection['ID'];
	}else $arResult['CURRENT_SECTION_ID'] = false;
	
	// parsing the search query
	if( !empty($_REQUEST['q']) )
	{
        $search = new Novagroup_Classes_General_Search($_REQUEST['q']);
        $search->setUseStatistic(true);
        $arResult['arElementsSearch'] = $search -> searchByIblock($arParams['CATALOG_IBLOCK_TYPE'], $arParams['CATALOG_IBLOCK_ID']) -> getPrepareArray();
		if ( empty( $arResult['arElementsSearch'] ) ) $arResult['arElementsSearch'][0] = -1;
	}
	
	// If the output filter has slider with prices that will prepare the structure for arResult
	if($arParams['SHOW_PRICE_SLIDER'] == "Y")
	{
		$arResult['ELEMENT'][ $arParams['PRICE_SORT_ORDER'] ] = array(
			'CODE'				=> 'CATALOG_PRICE_1',
			'NAME'				=> GetMessage('PRICE'),
			'PROPERTY_TYPE'		=> "N",
			'LINK_IBLOCK_ID'	=> 0,
			'LIST_TYPE'			=> "L",
			'ITEM'				=> array()
		);
		// choose the max/min price
		$arFilter = array(
			'ACTIVE'				=> "Y",
			'IBLOCK_ID'				=> $arParams['CATALOG_IBLOCK_ID']
		);
		if(!empty($arResult['CURRENT_SECTION_CODE']))
		{
			$arFilter['SECTION_CODE'] = $arResult['CURRENT_SECTION_CODE'];
			$arFilter['INCLUDE_SUBSECTIONS'] = "Y";
		}
		$arSubQuery = array(
			'IBLOCK_ID'			=> $arParams['TRADEOF_IBLOCK_ID'],
			'ACTIVE'			=> "Y",
			">CATALOG_QUANTITY" => 0,
			">CATALOG_PRICE_1"	=> 0
		);
		$arFilter['ID'] = CIBlockElement::SubQuery(
			'PROPERTY_CML2_LINK',
			$arSubQuery
		);
		
		$rsElement = CIBlockElement::GetList(
			array(),
			$arFilter,
			false,
			false,
			array('IBLOCK_ID', 'ID')
		);
		$arID = array();
		while($arElement = $rsElement -> Fetch())
			$arID[ $arElement['ID'] ] = $arElement['ID'];
		
		if( count( $arID) < 2 ) $arParams['SHOW_PRICE_SLIDER'] = "N";
		// max price
		$rsElement = CIBlockElement::GetList(
			array('CATALOG_PRICE_1' => "DESC"),
			array(
				'IBLOCK_ID' => $arParams['TRADEOF_IBLOCK_ID'],
				'PROPERTY_CML2_LINK' => $arID,
				">CATALOG_PRICE_1"	=> 0
			),
			false,
			array('nTopCount' => 1),
			array('IBLOCK_ID', 'ID', 'CATALOG_GROUP_1')
		);
		if($arElement = $rsElement -> Fetch())
			$maxPrice = (int)$arElement['CATALOG_PRICE_1'];
		// min price
		$rsElement = CIBlockElement::GetList(
			array('CATALOG_PRICE_1' => "ASC"),
			array(
				'IBLOCK_ID' => $arParams['TRADEOF_IBLOCK_ID'],
				'PROPERTY_CML2_LINK' => $arID,
				">CATALOG_PRICE_1"	=> 0
			),
			false,
			array('nTopCount' => 1),
			array('IBLOCK_ID', 'ID', 'CATALOG_GROUP_1')
		);
		if($arElement = $rsElement -> Fetch())
			$price = (int)$arElement['CATALOG_PRICE_1'];
		
		if( $maxPrice == $price )
		{
			$price = $maxPrice - round($maxPrice/10);
			$arParams['SHOW_PRICE_SLIDER'] = "N";
		}
		$PriceScale = round(($maxPrice - $price)/10);
		
		// will form a scale for prices and set the starting positions of sliders 
		$arResult['SCALE']['CATALOG_PRICE_1']['MIN'] = 0;
		$arResult['SCALE']['CATALOG_PRICE_1']['MAX'] = 0;
		if($PriceScale > 0)
		while( $price < $maxPrice )
		{
			$arResult['SCALE']['CATALOG_PRICE_1']['DATA'][] = $price;
			if(isset($arResult['arFilterRequest']))
			{
                if(isset($arResult['arFilterRequest']['minCATALOG_PRICE_1']) and is_array($arResult['arFilterRequest']['minCATALOG_PRICE_1']))
                {
                    if($price == end($arResult['arFilterRequest']['minCATALOG_PRICE_1']))
                        $arResult['SCALE']['CATALOG_PRICE_1']['MIN'] = count($arResult['SCALE']['CATALOG_PRICE_1']['DATA']) - 1;
                }
                if(isset($arResult['arFilterRequest']['maxCATALOG_PRICE_1']) and is_array($arResult['arFilterRequest']['maxCATALOG_PRICE_1']))
                {
                    if($price == end($arResult['arFilterRequest']['maxCATALOG_PRICE_1']))
                        $arResult['SCALE']['CATALOG_PRICE_1']['MAX'] = count($arResult['SCALE']['CATALOG_PRICE_1']['DATA']) - 1;
                }
			}
			
			$price += $PriceScale;
		}
        if(is_array($arResult['SCALE']['CATALOG_PRICE_1']['DATA']) && count($arResult['SCALE']['CATALOG_PRICE_1']['DATA'])>0)
        {
            $count = count($arResult['SCALE']['CATALOG_PRICE_1']['DATA'])-1;
            unset($arResult['SCALE']['CATALOG_PRICE_1']['DATA'][$count] );
            $arResult['SCALE']['CATALOG_PRICE_1']['DATA'][$count] = $maxPrice;
        }
		
		if( $arResult['SCALE']['CATALOG_PRICE_1']['MAX'] == 0 )
			$arResult['SCALE']['CATALOG_PRICE_1']['MAX'] = count($arResult['SCALE']['CATALOG_PRICE_1']['DATA']) - 1;
	}
	
	// Select the properties involved in the selection of the catalog
	// Show marked daw in a smart filter
	$arProps = array();
	$IBlocks = array();
	if( $arParams['CATALOG_IBLOCK_CODE'] != "")
		$IBlocks[ $arParams['CATALOG_IBLOCK_ID'] ] = $arParams['CATALOG_IBLOCK_CODE'];
	if( $arParams['TRADEOF_IBLOCK_CODE'] != "")
		$IBlocks[ $arParams['TRADEOF_IBLOCK_ID'] ] = $arParams['TRADEOF_IBLOCK_CODE'];
	$arSectionID = array();
	foreach($IBlocks as $key => $val)
	{
		foreach(CIBlockSectionPropertyLink::GetArray($key, $arResult['CURRENT_SECTION_ID']) as $PID => $arLink)
		{
			$minAmount = 0;
			$maxAmount = 0;
			if($arLink["SMART_FILTER"] !== "Y")
				continue;
			$rsProperty = CIBlockProperty::GetByID($PID);
			$arProperty = $rsProperty->Fetch();
			if($arProperty)
			{
				$ID = $arProperty['LINK_IBLOCK_ID'];
				$arProps[] = mb_strtoupper("PROPERTY_".$arProperty['CODE']);
				
				// create Buffer for desired properties,
				// As some can not get in our sample is not always the buffer goes to $ arResult
				
				$arBuffer['ID']				= $arProperty['ID'];
				$arBuffer['CODE']			= mb_strtoupper($arProperty['CODE']);
				$arBuffer['NAME']			= $arProperty['NAME'];
				$arBuffer['PROPERTY_TYPE']	= $arProperty['PROPERTY_TYPE'];
				$arBuffer['LINK_IBLOCK_ID']	= $arProperty['LINK_IBLOCK_ID'];
				$arBuffer['LIST_TYPE']		= $arProperty['LIST_TYPE'];
				$arBuffer['IBLOCK_CODE']	= $val;
				
				if(isset($arResult['arFilterRequest']))
				{
					if( array_key_exists('min'.$arBuffer['CODE'], $arResult['arFilterRequest']))
						$minAmount = current( $arResult['arFilterRequest']['min'.$arBuffer['CODE']] );
					
					if( array_key_exists('max'.$arBuffer['CODE'], $arResult['arFilterRequest']))
						$maxAmount = current($arResult['arFilterRequest']['max'.$arBuffer['CODE']]);
				}
				// If the property is revealing string or a number
				// Group the values on it and extend filter this data
				if(
					($arProperty['PROPERTY_TYPE'] == "S")
					||
					($arProperty['PROPERTY_TYPE'] == "N")
				)
				{
					$arResult['ELEMENT'][ $arProperty['SORT'] ] = $arBuffer;
					$arFilter = array(
						'IBLOCK_ID'	=> $arParams['CATALOG_IBLOCK_ID'],
						'ACTIVE'	=> "Y"
					);
					$rsElement = CIBlockElement::GetList(
						array('PROPERTY_'.$arProperty['CODE'] => "ASC"),
						$arFilter,
						array('PROPERTY_'.$arProperty['CODE']),
						false,
						false
					);
					$KEY = 0;
					$arScale = array();
					$arResult['SCALE'][ $arBuffer['CODE'] ]['MIN'] = 0;
					$arResult['SCALE'][ $arBuffer['CODE'] ]['MAX'] = 0;
					while($arElement = $rsElement -> Fetch())
					{
						if( (int)$arElement['PROPERTY_'.mb_strtoupper($arProperty['CODE']).'_VALUE'] != 0 )
						{
							$arResult['ELEMENT'][ $arProperty['SORT'] ]['ITEM'][$KEY]['ID']
							 = $arElement['PROPERTY_'.mb_strtoupper($arProperty['CODE']).'_VALUE'];
							
							$arResult['ELEMENT'][ $arProperty['SORT'] ]['ITEM'][$KEY]['NAME']
							 = $arResult['ELEMENT'][ $arProperty['SORT'] ]['ITEM'][$KEY]['ID'];
							
							$arScale[] = $arResult['ELEMENT'][ $arProperty['SORT'] ]['ITEM'][$KEY]['ID'];
							
							if($minAmount == $arResult['ELEMENT'][ $arProperty['SORT'] ]['ITEM'][$KEY]['ID'])
								$arResult['SCALE'][ $arBuffer['CODE'] ]['MIN'] = count($arScale) - 1;
							if($maxAmount == $arResult['ELEMENT'][ $arProperty['SORT'] ]['ITEM'][$KEY]['ID'])
								$arResult['SCALE'][ $arBuffer['CODE'] ]['MAX'] = count($arScale) - 1;
							$KEY++;
						}
					}
					if($arResult['SCALE'][ $arBuffer['CODE'] ]['MAX'] == 0)
						$arResult['SCALE'][ $arBuffer['CODE'] ]['MAX'] = count($arScale) - 1;
					$arResult['SCALE'][ $arBuffer['CODE'] ]['DATA'] = $arScale;
				}
				//  If the property is revealing reference handbook supplement the filter elements 
				if($arProperty['PROPERTY_TYPE'] == "E")
				{
					$arResult['ELEMENT'][ $arProperty['SORT'] ] = $arBuffer;
					$rsElement = CIBlockElement::GetList(
						array('SORT' => "ASC"),
						array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ACTIVE' => "Y"),
						false,
						false,
						array('ID','NAME', 'IBLOCK_SECTION_ID')
					);
					while($arElement = $rsElement -> Fetch())
					{
						$arResult['ELEMENT'][ $arProperty['SORT'] ]['ITEM'][] = $arElement;
						$arSectionID[ $arElement['IBLOCK_SECTION_ID'] ] = $arElement['IBLOCK_SECTION_ID'];
					}
				}
				// Single features (such svostva list with a flag such as special offers)
				// Combine into one block
				
				if( 
					($arProperty['PROPERTY_TYPE'] == "L")
					&&
					($arProperty['LIST_TYPE'] == "C")
				)
				{
					$rsEnum = CIBlockProperty::GetPropertyEnum(
						$arProperty['CODE'],
						array(),
						array()
					);
					while($arEnum = $rsEnum -> Fetch())
					{
						/*?><div style="width:800px !important;"><? deb($arEnum);?></div><?*/
						$KEY++;
						$arResult['ELEMENT'][ $arParams['SPECIAL_SORT_ORDER'] ]['ITEM'][ $KEY ]['ID'] = $arEnum['VALUE'];
						$arResult['ELEMENT'][ $arParams['SPECIAL_SORT_ORDER'] ]['ITEM'][ $KEY ]['CODE'] = $arBuffer['CODE'];
						$arResult['ELEMENT'][ $arParams['SPECIAL_SORT_ORDER'] ]['ITEM'][ $KEY ]['NAME'] = $arBuffer['NAME'];
						$arResult['ELEMENT'][ $arParams['SPECIAL_SORT_ORDER'] ]['CODE'] = "MISC";
						$arResult['ELEMENT'][ $arParams['SPECIAL_SORT_ORDER'] ]['PROPERTY_TYPE'] = "M";
						break; // we use only first element
					}
				}
			}
		}
	}
	
	//
	if($arParams['SHOW_SECTION'] == "Y")
	{
		$arResult['ELEMENT'][ $arParams['SECTION_SORT_ORDER'] ] = array(
			//'ID'				=> 0,
			'CODE'				=> 'SECTION_CODE',
			'NAME'				=> GetMessage('CATEGORIES'),
			'PROPERTY_TYPE'		=> "SECTION",
			'IBLOCK_CODE'		=> $arParams['CATALOG_IBLOCK_CODE'],
			'LINK_IBLOCK_ID'	=> 0,
			'LIST_TYPE'			=> "L",
			'ITEM'				=> array()
		);
		$arFilter = array(
			'IBLOCK_ID'		=> $arParams['CATALOG_IBLOCK_ID'],
			'GLOBAL_ACTIVE'	=> "Y",
			'SECTION_ID'	=> $arResult['CURRENT_SECTION_ID'],
		);
		$rsSection = CIBlockSection::GetList(
			array('SORT'	=> "ASC"),
			$arFilter,
			true
		);
		$KEY = 0;
		while($arSection = $rsSection -> Fetch())
		{
			if($arSection['ELEMENT_CNT'] > 0)
			{
				$arResult['ELEMENT'][ $arParams['SECTION_SORT_ORDER'] ]['ITEM'][$KEY]['ID'] = $arSection['ID'];
				$arResult['ELEMENT'][ $arParams['SECTION_SORT_ORDER'] ]['ITEM'][$KEY]['NAME'] = $arSection['NAME'];
				$arResult['ELEMENT'][ $arParams['SECTION_SORT_ORDER'] ]['ITEM'][$KEY]['CODE'] = $arSection['CODE'];
				$KEY++;
			}
		}
	}
	
	if(!empty($arSectionID))
	{
		$arFilter = array(
			//'IBLOCK_ID'		=> 8,
			'GLOBAL_ACTIVE'	=> "Y",
			'ID'	=> $arSectionID,
		);
		$rsSection = CIBlockSection::GetList(
			array('SORT'	=> "ASC"),
			$arFilter,
			true
		);
		while($arSection = $rsSection -> Fetch())
			if($arSection['ELEMENT_CNT'] > 0)
				$arResult['SECTION'][ $arSection['ID'] ] = $arSection['NAME'];
	}
	
	ksort($arResult['ELEMENT']);
/*?><div style="width:800px !important;"><? deb($arResult);?></div><?*/

        $this -> IncludeComponentTemplate();


	
	
}
?>