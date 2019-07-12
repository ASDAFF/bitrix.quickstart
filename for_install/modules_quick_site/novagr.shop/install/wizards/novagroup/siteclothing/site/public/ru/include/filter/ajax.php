<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

	CModule::IncludeModule("iblock");
	CModule::IncludeModule("catalog");
	//setlocale(LC_NUMERIC,'C');
	
	function callback($buffer)
	{
		/*
		* инициализация начальных параметров
		*/
		
		/*
		$arFilter =array();
		foreach($_REQUEST['arFilter'] as $val)
			foreach($val as $subkey => $subval)
				$arParams['arFilterRequest'][$subkey][] = $subval;
		
		foreach($_REQUEST['arOffer'] as $val)
			foreach($val as $subkey => $subval)
				$arParams['arOfferRequest'][$subkey][] = $subval;
		
		if( !empty($arParams['arFilterRequest']['minCATALOG_PRICE_1']) )
		{
			$arParams['arOfferRequest']['>=CATALOG_PRICE_1'] = $arParams['arFilterRequest']['minCATALOG_PRICE_1'][0];
			unset( $arParams['arFilterRequest']['minCATALOG_PRICE_1'] );
		}
		if( !empty($arParams['arFilterRequest']['maxCATALOG_PRICE_1']) )
		{
			$arParams['arOfferRequest']['<=CATALOG_PRICE_1'] = $arParams['arFilterRequest']['maxCATALOG_PRICE_1'][0];
			unset( $arParams['arFilterRequest']['maxCATALOG_PRICE_1'] );
		}
		// вычисление текущих секций/подсекций в каталоге, содержащие элементы
		$arFilter = $arParams['arFilterRequest'];
		$arFilter['IBLOCK_ID'] = $_REQUEST['CATALOG_IBLOCK_ID'];
		$arSubFilter = $arFilter;
		unset($arSubFilter['SECTION_ID']);
		$rsElement = CIBlockElement::GetList(
			array(),
			$arSubFilter,
			array('IBLOCK_SECTION_ID'),
			false,
			false
		);
		while( $arElement = $rsElement -> Fetch() )
		{
			$rsSection = CIBlockSection::GetNavChain($_REQUEST['CATALOG_IBLOCK_ID'], $arElement['IBLOCK_SECTION_ID']);
			while($arSection = $rsSection -> Fetch())
				$arResult['SECTION']['AVAIL'][ $arSection['ID'] ] = $arSection['ID'];
			if($arElement['CNT'] > 0)
				$arResult['SECTION']['AVAIL'][ $arElement['IBLOCK_SECTION_ID'] ] = $arElement['IBLOCK_SECTION_ID'];
		}
		// группировка по свойствам каталога
		if(
			//!empty($arFilter['SECTION_ID']) &&
			!empty($_REQUEST['CUR_SECTION_CODE']) )
			$arFilter['SECTION_CODE'] = $_REQUEST['CUR_SECTION_CODE'];
		$arProps = array();
		foreach(CIBlockSectionPropertyLink::GetArray($_REQUEST['CATALOG_IBLOCK_ID'], false) as $PID => $arLink)
		{
			if($arLink["SMART_FILTER"] !== "Y")
				continue;
			$rsProperty = CIBlockProperty::GetByID($PID);
			$arProperty = $rsProperty->Fetch();
			if($arProperty)
			{
				$ID = $arProperty['LINK_IBLOCK_ID'];
				$arProps[] = mb_strtoupper("PROPERTY_".$arProperty['CODE']);
			}
		}
		$arFilter['ACTIVE'] = "Y";
		$arFilter['INCLUDE_SUBSECTIONS'] = "Y";
		$arFilter['IBLOCK_ID'] = $_REQUEST['CATALOG_IBLOCK_ID'];
		foreach($arProps as $val)
		{
			$arSubFilter = $arFilter;
			unset($arSubFilter[$val]);
			$rsElement = CIBlockElement::GetList(
				array(),
				$arSubFilter,
				array($val),
				false,
				false
			);
			while( $arElement = $rsElement -> Fetch() )
			{
				if($arElement['CNT'] > 0)
					$arResult['FILTER']['AVAIL'][ $arElement[$val.'_VALUE'] ] = $arElement[$val.'_VALUE'];
			}
		}
		// группировка по свойсвам торговых предложений
		if( isset($arParams['arFilterRequest']) )
			$arFilter = $arParams['arFilterRequest'];
		
		$arFilter['ACTIVE'] = "Y";
		$arFilter['INCLUDE_SUBSECTIONS'] = "Y";
		$arFilter['IBLOCK_ID'] = $_REQUEST['CATALOG_IBLOCK_ID'];
		if( 
			//!empty($arFilter['SECTION_ID']) &&
			!empty($_REQUEST['CUR_SECTION_CODE']) )
			$arFilter['SECTION_CODE'] = $_REQUEST['CUR_SECTION_CODE'];
		$arParams['arOfferRequest']['ACTIVE'] = "Y";
		$arParams['arOfferRequest'][">CATALOG_QUANTITY"] = 0;
		if( !empty($arParams['arOfferRequest']) )
		{
			$arSubQuery = $arParams['arOfferRequest'];
			$arFilter['ID'] = CIBlockElement::SubQuery(
				'PROPERTY_CML2_LINK',
				$arSubQuery
			);
		}
		$arSelect = array(
			'ID',
			'NAME'
		);
		$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
		$arElementId = array();
		while($arElement = $rsElement -> Fetch())
		{
			$arElementId[] = $arElement['ID'];
		}
		$arProps = array();
		foreach(CIBlockSectionPropertyLink::GetArray($_REQUEST['OFFERS_IBLOCK_ID'], false) as $PID => $arLink)
		{
			if($arLink["SMART_FILTER"] !== "Y")
				continue;
			$rsProperty = CIBlockProperty::GetByID($PID);
			$arProperty = $rsProperty->Fetch();
			if($arProperty)
			{
				$ID = $arProperty['LINK_IBLOCK_ID'];
				$arProps[] = mb_strtoupper("PROPERTY_".$arProperty['CODE']);
			}
		}
		$arFilter = array(
			'IBLOCK_ID'				=> (int)$_REQUEST['OFFERS_IBLOCK_ID'],
			'PROPERTY_CML2_LINK'	=> $arElementId,
			'ACTIVE'				=> "Y",
		);
		$arSelect = array_merge(array('ID','NAME', 'PROPERTY_CML2_LINK'), $arProps);
		$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
		while($arElement = $rsElement -> Fetch())
		{
			foreach($arProps as $val)
				$arResult['FILTER']['AVAIL'][ $arElement[$val.'_VALUE'] ] = $arElement[ $val.'_VALUE' ];
		}
		*/
		$arElementsSearch = $_REQUEST['arElementsSearch'];
		
		// сформируем фильтр для каталога
		foreach($_REQUEST['arFilter'] as $val)
			foreach($val as $subkey => $subval)
				$arParams['arFilterRequest'][$subkey][] = $subval;
		// сформируем фильтр для торговых предложений
		foreach($_REQUEST['arOffer'] as $val)
			foreach($val as $subkey => $subval)
				$arParams['arOfferRequest'][$subkey][] = $subval;
		// определим ценовой диапазон для торговых предложений
		if( !empty($arParams['arFilterRequest']['minCATALOG_PRICE_1']) )
		{
			$arParams['arOfferRequest']['>=CATALOG_PRICE_1'] = $arParams['arFilterRequest']['minCATALOG_PRICE_1'][0];
			unset( $arParams['arFilterRequest']['minCATALOG_PRICE_1'] );
		}
		if( !empty($arParams['arFilterRequest']['maxCATALOG_PRICE_1']) )
		{
			$arParams['arOfferRequest']['<=CATALOG_PRICE_1'] = $arParams['arFilterRequest']['maxCATALOG_PRICE_1'][0];
			unset( $arParams['arFilterRequest']['maxCATALOG_PRICE_1'] );
		}
		// сформируем фильтр по каталогу
		if( !empty($arParams['arFilterRequest']) )
			$arFilter = $arParams['arFilterRequest'];
		// опредлим текущую секцию в каталоге и запишем в фильтр каталога
		if(!empty($_REQUEST['secid']))
		{
			$arResult['CUR_SECTION_CODE'] = $_REQUEST['secid'];
			$arFilter['SECTION_CODE'] = $arResult['CUR_SECTION_CODE'];
		}
		$arFilter['ACTIVE'] = "Y";
		$arFilter['IBLOCK_ID'] = $_REQUEST['CATALOG_IBLOCK_ID'];
		$arFilter['INCLUDE_SUBSECTIONS'] = "Y";
		// сформируем фильтр для торговых предложений
		if ( !empty($arElementsSearch) )
			$arParams['arOfferRequest']['PROPERTY_CML2_LINK']	= $arElementsSearch;

		if( !empty($arParams['arOfferRequest']) )
		{
            $arParams['arOfferRequest']['IBLOCK_ID'] = (int)$_REQUEST['OFFERS_IBLOCK_ID']; 
            $arParams['arOfferRequest']['ACTIVE'] = "Y";
            $arParams['arOfferRequest'][">CATALOG_QUANTITY"] = 0;
			$arSubQuery = $arParams['arOfferRequest'];
			$arFilter['ID'] = CIBlockElement::SubQuery(
				'PROPERTY_CML2_LINK',
				$arSubQuery
			);
		}
		// зададим поля для выборки
		$arSelect = array(
			'IBLOCK_ID',
			'ID',
		);
		// выберем элементы входящие в фильтр и запомним их ID
		$rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
		$arElementId = array(); // массив для ID элементов
		while($arElement = $rsElement -> Fetch())
			$arElementId[] = $arElement['ID'];
		
		if( count($arElementId) > 0 )
		{
			// выберем свойтсва каталога с галкой умный фильтр
			$arProps = array();
			foreach(CIBlockSectionPropertyLink::GetArray($_REQUEST['CATALOG_IBLOCK_ID'], false) as $PID => $arLink)
			{
				if($arLink['SMART_FILTER'] !== "Y")
					continue;
				$rsProperty = CIBlockProperty::GetByID($PID);
				$arProperty = $rsProperty->Fetch();
				if($arProperty)
				{
					$ID = $arProperty['LINK_IBLOCK_ID'];
					$arProps[] = mb_strtoupper("PROPERTY_".$arProperty['CODE']);
				}
			}
			// выберем элементы каталога, поочерёдно сгруппировав по свойствам каталога и занесём их значения в активные свойства нового состояния фильтра
			$arFilter = array(
				'IBLOCK_ID'	=> $_REQUEST['CATALOG_IBLOCK_ID'],
				'ID'		=> $arElementId
			);
			foreach($arProps as $val)
			{
				$arSubFilter = $arFilter;
				unset($arSubFilter[$val]);
				$rsElement = CIBlockElement::GetList(
					array(),
					$arSubFilter,
					array($val),
					false,
					false
				);
				while( $arElement = $rsElement -> Fetch() )
				{
					if($arElement['CNT'] > 0)
						$arResult['FILTER']['AVAIL'][ $arElement[$val.'_VALUE'] ] = $arElement[$val.'_VALUE'];
				}
			}
			// выберем торговые предложения с учётом выбранных ранее ID элементов и свойствами фильтра, относящиеся к торговым предложениям, сгруппируем их по данным свойствам и занесём их значения в активныйе свойства нового состояния фильтра
			$arProps = array();
			foreach(CIBlockSectionPropertyLink::GetArray($_REQUEST['OFFERS_IBLOCK_ID'], false) as $PID => $arLink)
			{
				if($arLink['SMART_FILTER'] !== "Y")
					continue;
				$rsProperty = CIBlockProperty::GetByID($PID);
				$arProperty = $rsProperty->Fetch();
				if($arProperty)
				{
					$ID = $arProperty['LINK_IBLOCK_ID'];
					$arProps[] = mb_strtoupper("PROPERTY_".$arProperty['CODE']);
				}
			}
			$arFilter = array(
				'IBLOCK_ID'				=> $_REQUEST['OFFERS_IBLOCK_ID'],
				'PROPERTY_CML2_LINK'	=> $arElementId
			);
			foreach($arProps as $val)
			{
				$arSubFilter = $arFilter;
				unset($arSubFilter[$val]);
				$rsElement = CIBlockElement::GetList(
					array(),
					$arSubFilter,
					array($val),
					false,
					false
				);
				while( $arElement = $rsElement -> Fetch() )
				{
					if($arElement['CNT'] > 0)
						$arResult['FILTER']['AVAIL'][ $arElement[$val.'_VALUE'] ] = $arElement[$val.'_VALUE'];
				}
			}
		}
		
		// состаляем массив с новым состоянием фильтра
		$arFilterState = array();
		$key = 0;
		foreach($_REQUEST['arFilterValue'] as $val)
		{
			$arFilterState[$key] = 0;
			if(in_array($val, $arResult['FILTER']['AVAIL']))
			{
				$arFilterState[$key] = 1;
			}
			$key++;
		}
		$start = strpos($buffer, "<!--start_html-->");
		$buffer = substr($buffer, $start+17);
		$end = strpos($buffer, "<!--end_html-->");
		$buffer = substr($buffer, 0, $end);
		// фикс для акций
		$arFilterState[0] = 1;
		$arFilterState[1] = 1;
		$arFilterState[2] = 1;
		return '{"workarea":"'.str_replace(array("\\","\r","\n","\t",'"'),array("&#092;","","","","'"), $buffer).'","arFilterState":'.json_encode($arFilterState).',"arState":'.json_encode($arElementsSearch).'}';
	}
	
	ob_start("callback");
	include($_SERVER['DOCUMENT_ROOT'].SITE_DIR."include/catalog/inc.collections.php");
	/*
$APPLICATION->IncludeComponent(
	"novagr.shop:catalog.list",
	"",
	Array(
		"CATALOG_IBLOCK_ID"		=> $_REQUEST['CATALOG_IBLOCK_ID'],//"3",
		"OFFERS_IBLOCK_ID"		=> $_REQUEST['OFFERS_IBLOCK_ID'],//"4",
		"ROOT_PATH"				=> $_REQUEST['ROOT_PATH'],//"/demo/",
        "nPageSize"             => $_REQUEST['nPageSize'],
		"CACHE_TYPE"			=> "A",
		"CACHE_TIME"			=> "2592000"
	)
);*/
	ob_end_flush();
?>