<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); ?>
<?='{"error":"';?>
<?
	// initialize
	COption::SetOptionString("cacheMaker","setTimout", $_REQUEST['arParams']['setTimout']);
	COption::SetOptionString("cacheMaker","setProductsCode", $_REQUEST['arParams']['setProductsCode']);
	COption::SetOptionString("cacheMaker","setComplectCode", $_REQUEST['arParams']['setComplectCode']);
	COption::SetOptionString("cacheMaker","srcProductsList", $_REQUEST['arParams']['srcProductsList']);
	COption::SetOptionString("cacheMaker","srcComplectList", $_REQUEST['arParams']['srcComplectList']);
	
	$_REQUEST['arParams']['nPageSize'] = 16;
	
	$fName = $_SERVER["DOCUMENT_ROOT"]."/upload/cacheMaker.txt";
	$fHand = @fopen($fName, 'r');
	$arParams = unserialize( @fgets($fHand) );
	
	if( serialize($arParams['last_session_params']) != serialize($_REQUEST['arParams']) )
		$arParams = array();
	@fclose($fHand);

	$arParams['last_session_params'] = $_REQUEST['arParams'];
	
	$arParams['MESSAGE'] = "";
	$arParams['URL'] = "";
	
	CModule::IncludeModule( "iblock" );
	
	// warm-up product list pages
	if($_REQUEST['arParams']['setProductsList'] == "Y")
	{
		if( 
			( (int)$arParams['PRODUCTS']['iNumPage'] < 1 )
			||
			( (int)$arParams['PRODUCTS']['ELEMENT_CTR'] == 0 )
		)
		{
			$arParams['PRODUCTS']['iNumPage'] = 0;
			$arParams['PRODUCTS']['ELEMENT_CTR'] = CIBlockElement::GetList(
				array(),
				array('IBLOCK_CODE' => $_REQUEST['arParams']['setProductsCode'], 'ACTIVE' => "Y"),
				array(),
				false,
				array('IBLOCK_ID','ID')
			);
		}	
		
		if(
			$arParams['PRODUCTS']['iNumPage']
			<=
			ceil($arParams['PRODUCTS']['ELEMENT_CTR']/$_REQUEST['arParams']['nPageSize'])
		)
		{
			$arParams['PRODUCTS']['iNumPage']++;
			
			$arParams['URL'] = "http://".$_SERVER['HTTP_HOST'].$_REQUEST['arParams']['srcProductsList']."?iNumPage=".$arParams['PRODUCTS']['iNumPage']."&nPageSize=".$_REQUEST['arParams']['nPageSize']."&orderRow=&clear_cache=Y";
			$arParams['MESSAGE'] = $arParams['PRODUCTS']['iNumPage']."/".(ceil($arParams['PRODUCTS']['ELEMENT_CTR']/$_REQUEST['arParams']['nPageSize']));
		}
	}
	
	// warm-up complect list pages
	if($_REQUEST['arParams']['setComplectList'] == "Y")
	{
		if(
			( (int)$arParams['COMPLECT']['iNumPage'] < 1 )
			||
			( (int)$arParams['COMPLECT']['ELEMENT_CTR'] == 0 )
		)
		{
			$arParams['COMPLECT']['iNumPage'] = 0;
			$arParams['COMPLECT']['ELEMENT_CTR'] = CIBlockElement::GetList(
				array(),
				array('IBLOCK_CODE' => $_REQUEST['arParams']['setComplectCode'], 'ACTIVE' => "Y"),
				array(),
				false,
				array('IBLOCK_ID','ID')
			);
		}
		
		if(
			empty($arParams['URL'])
			&&
			(
				$arParams['COMPLECT']['iNumPage']
				<=
				ceil($arParams['COMPLECT']['ELEMENT_CTR']/$_REQUEST['arParams']['nPageSize'])
			)
		)
		{
			$arParams['COMPLECT']['iNumPage']++;
			
			$arParams['URL'] = "http://".$_SERVER['HTTP_HOST'].$_REQUEST['arParams']['srcComplectList']."?iNumPage=".$arParams['COMPLECT']['iNumPage']."&nPageSize=".$_REQUEST['arParams']['nPageSize']."&orderRow=&clear_cache=Y";
			$arParams['MESSAGE'] = $arParams['COMPLECT']['iNumPage']."/".(ceil($arParams['COMPLECT']['ELEMENT_CTR']/$_REQUEST['arParams']['nPageSize']));
		}
	}
	
	// warm-up product detail pages
	if($_REQUEST['arParams']['setProductsDetail'] == "Y")
	{
		// get element count
		if( 
			( (int)$arParams['PRODUCTS_DETAIL']['iNumPage'] < 1 )
			||
			( (int)$arParams['PRODUCTS_DETAIL']['ELEMENT_CTR'] == 0 )
		)
		{
			$arParams['PRODUCTS_DETAIL']['iNumPage'] = 0;
			$arParams['PRODUCTS_DETAIL']['KEY'] = $_REQUEST['arParams']['nPageSize'];
			
			$arFilter = array('IBLOCK_CODE' => $_REQUEST['arParams']['setProductsCode'], 'ACTIVE' => "Y");
			if((int)$_REQUEST['arParams']['idProductsFrom'] > 0)
				$arFilter['>=ID'] = $_REQUEST['arParams']['idProductsFrom'];
			if((int)$_REQUEST['arParams']['idProductsTo'] > 0)
				$arFilter['<=ID'] = $_REQUEST['arParams']['idProductsTo'];
			
			$arParams['PRODUCTS_DETAIL']['ELEMENT_CTR'] = CIBlockElement::GetList(
				array(),
				$arFilter,
				array(),
				false,
				array('IBLOCK_ID','ID')
			);
		}
		
		if(
			empty($arParams['URL'])
			&&
			(
				$arParams['PRODUCTS_DETAIL']['iNumPage']
				<=
				ceil($arParams['PRODUCTS_DETAIL']['ELEMENT_CTR']/$_REQUEST['arParams']['nPageSize'])
			)
		)
		{
			// get new nPageSize element's id
			if($arParams['PRODUCTS_DETAIL']['KEY'] >= ($_REQUEST['arParams']['nPageSize']))
			{
				$arParams['PRODUCTS_DETAIL']['iNumPage']++;
				$arParams['PRODUCTS_DETAIL']['KEY'] = 0;
				
				$arFilter = array('IBLOCK_CODE' => $_REQUEST['arParams']['setProductsCode'], 'ACTIVE' => "Y");
				
				if((int)$_REQUEST['arParams']['idProductsFrom'] > 0)
					$arFilter['>=ID'] = $_REQUEST['arParams']['idProductsFrom'];
				if((int)$_REQUEST['arParams']['idProductsTo'] > 0)
					$arFilter['<=ID'] = $_REQUEST['arParams']['idProductsTo'];
				
				$rsElement = CIBlockElement::GetList(
					array(),
					$arFilter,
					false,
					array(
						'iNumPage'	=> $arParams['PRODUCTS_DETAIL']['iNumPage'],
						'nPageSize'	=> $_REQUEST['arParams']['nPageSize']
					),
					array('IBLOCK_ID','ID', 'NAME', 'DETAIL_PAGE_URL')
				);
				$arParams['PRODUCTS_DETAIL']['ELEMENT_LIST'] = array();
				$key = 0;
				while($arElement = $rsElement -> GetNext())
				{
					$arParams['PRODUCTS_DETAIL']['ELEMENT_LIST'][$key]['DETAIL_PAGE_URL'] = $arElement['DETAIL_PAGE_URL'];
					$arParams['PRODUCTS_DETAIL']['ELEMENT_LIST'][$key]['NAME'] = $arElement['NAME'];
					$key++;
				}
			}else
				$arParams['PRODUCTS_DETAIL']['KEY']++;
			
			$arParams['URL'] = "http://".$_SERVER['HTTP_HOST'].$arParams['PRODUCTS_DETAIL']['ELEMENT_LIST'][ $arParams['PRODUCTS_DETAIL']['KEY'] ]['DETAIL_PAGE_URL']."&clear_cache=Y";
			$arParams['MESSAGE'] = $arParams['PRODUCTS_DETAIL']['ELEMENT_LIST'][ $arParams['PRODUCTS_DETAIL']['KEY'] ]['NAME'];
			
		}
	}
	
	// warm-up product detail pages
	if($_REQUEST['arParams']['setComplectDetail'] == "Y")
	{
		// get element count
		if( 
			( (int)$arParams['COMPLECT_DETAIL']['iNumPage'] < 1 )
			||
			( (int)$arParams['COMPLECT_DETAIL']['ELEMENT_CTR'] == 0 )
		)
		{
			$arParams['COMPLECT_DETAIL']['iNumPage'] = 0;
			$arParams['COMPLECT_DETAIL']['KEY'] = $_REQUEST['arParams']['nPageSize'];
			
			$arFilter = array('IBLOCK_CODE' => $_REQUEST['arParams']['setComplectCode'], 'ACTIVE' => "Y");
			if((int)$_REQUEST['arParams']['idComplectFrom'] > 0)
				$arFilter['>=ID'] = $_REQUEST['arParams']['idComplectFrom'];
			if((int)$_REQUEST['arParams']['idComplectTo'] > 0)
				$arFilter['<=ID'] = $_REQUEST['arParams']['idComplectTo'];
			
			$arParams['COMPLECT_DETAIL']['ELEMENT_CTR'] = CIBlockElement::GetList(
				array(),
				$arFilter,
				array(),
				false,
				array('IBLOCK_ID','ID')
			);
		}
		
		if(
			empty($arParams['URL'])
			&&
			(
				$arParams['COMPLECT_DETAIL']['iNumPage']
				<=
				ceil($arParams['COMPLECT_DETAIL']['ELEMENT_CTR']/$_REQUEST['arParams']['nPageSize'])
			)
		)
		{
			// get new nPageSize element's id
			if($arParams['COMPLECT_DETAIL']['KEY'] >= ($_REQUEST['arParams']['nPageSize']))
			{
				$arParams['COMPLECT_DETAIL']['iNumPage']++;
				$arParams['COMPLECT_DETAIL']['KEY'] = 0;
				
				$arFilter = array('IBLOCK_CODE' => $_REQUEST['arParams']['setComplectCode'], 'ACTIVE' => "Y");
				
				if((int)$_REQUEST['arParams']['idComplectFrom'] > 0)
					$arFilter['>=ID'] = $_REQUEST['arParams']['idComplectFrom'];
				if((int)$_REQUEST['arParams']['idComplectTo'] > 0)
					$arFilter['<=ID'] = $_REQUEST['arParams']['idComplectTo'];
				
				$rsElement = CIBlockElement::GetList(
					array(),
					$arFilter,
					false,
					array(
						'iNumPage'	=> $arParams['COMPLECT_DETAIL']['iNumPage'],
						'nPageSize'	=> $_REQUEST['arParams']['nPageSize']
					),
					array('IBLOCK_ID','ID', 'NAME', 'DETAIL_PAGE_URL')
				);
				$arParams['COMPLECT_DETAIL']['ELEMENT_LIST'] = array();
				$key = 0;
				while($arElement = $rsElement -> GetNext())
				{
					$arParams['COMPLECT_DETAIL']['ELEMENT_LIST'][$key]['DETAIL_PAGE_URL'] = $arElement['DETAIL_PAGE_URL'];
					$arParams['COMPLECT_DETAIL']['ELEMENT_LIST'][$key]['NAME'] = $arElement['NAME'];
					$key++;
				}
			}else
				$arParams['COMPLECT_DETAIL']['KEY']++;
			
			$arParams['URL'] = "http://".$_SERVER['HTTP_HOST'].$arParams['COMPLECT_DETAIL']['ELEMENT_LIST'][ $arParams['COMPLECT_DETAIL']['KEY'] ]['COMPLECT_PAGE_URL']."&clear_cache=Y";
			$arParams['MESSAGE'] = $arParams['COMPLECT_DETAIL']['ELEMENT_LIST'][ $arParams['COMPLECT_DETAIL']['KEY'] ]['NAME'];
			
		}
	}
	
	// recalc progress
	$arParams['TIME_ELAPSED'] = "00:00:00";
	if(
		($arParams['PRODUCTS']['ELEMENT_CTR'] > 0)
		||
		($arParams['COMPLECT']['ELEMENT_CTR'] > 0)
		||
		($arParams['PRODUCTS_DETAIL']['ELEMENT_CTR'] > 0)
		||
		($arParams['COMPLECT_DETAIL']['ELEMENT_CTR'] > 0)
	)
	{
		$sumA = 0;
		$sumB = 0;
		if( $_REQUEST['arParams']['setProductsList'] == "Y" )
		{
			// current element products list
			$sumA += $arParams['PRODUCTS']['iNumPage'];
			// total element product list
			$sumB += ceil($arParams['PRODUCTS']['ELEMENT_CTR'] / $_REQUEST['arParams']['nPageSize']);
		}
		if( $_REQUEST['arParams']['setComplectList'] == "Y" )
		{
			// current element complect list
			$sumA += $arParams['COMPLECT']['iNumPage'];
			// total element complect list
			$sumB += ceil($arParams['COMPLECT']['ELEMENT_CTR'] / $_REQUEST['arParams']['nPageSize']);
		}
		if( $_REQUEST['arParams']['setProductsDetail'] == "Y" )
		{
			// current element products detail
			$sumA += ($arParams['PRODUCTS_DETAIL']['iNumPage']-1)
				* $_REQUEST['arParams']['nPageSize']
				+ $arParams['PRODUCTS_DETAIL']['KEY']+1;
			// total element products detail
			$sumB += $arParams['PRODUCTS_DETAIL']['ELEMENT_CTR'];
		}
		if( $_REQUEST['arParams']['setComplectDetail'] == "Y" )
		{
			// current element complect detail
			$sumA += ($arParams['COMPLECT_DETAIL']['iNumPage']-1)
				* $_REQUEST['arParams']['nPageSize']
				+ $arParams['COMPLECT_DETAIL']['KEY']+1;
			// total element complect detail
			$sumB += $arParams['COMPLECT_DETAIL']['ELEMENT_CTR'];
		}
		$arParams['PROGRESS'] = round(100 * (($sumA * 100) / ( $sumB )) ) / 100;
		$arParams['TIME_ELAPSED'] = gmdate("H:i:s", ($sumB-$sumA) * $_REQUEST['arParams']['setTimout']);
	}else $arParams['PROGRESS'] = 100;
	
	if( ($arParams['PROGRESS'] >= 100) || ($arParams['PROGRESS'] <= 0) )
	{
		$arParams['PROGRESS'] = 100;
		$arParams['MESSAGE'] = "";
	}
	
	//$arParams['MESSAGE'] = $arParams['MESSAGE'].", завершено ".$arParams['PROGRESS']."%;
	
	$fHand = fopen($fName, 'w+');
	fputs($fHand, serialize($arParams));
	fclose($fHand);
	
	if($arParams['PROGRESS'] >= 100) unlink($fName);
?>
<?='", "progress":"'.$arParams['PROGRESS'].'", "message":"'.$arParams['MESSAGE'].'","URL":"'.$arParams['URL'].'", "time":"'.$arParams['TIME_ELAPSED'].'"}';?>