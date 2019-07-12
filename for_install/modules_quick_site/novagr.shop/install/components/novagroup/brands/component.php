<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

// the number of output brands
if (isset($arParams["COUNT_RECORDS"])) {
	$arParams["COUNT_RECORDS"] = intval($arParams["COUNT_RECORDS"]);
} else {	
	$arParams["COUNT_RECORDS"] = false;
}


if( CModule::IncludeModule("iblock") )
{
	if (!$arParams['SORT_FIELD']) $arParams['SORT_FIELD'] = "SORT";
	
	if (!$arParams['SORT_BY']) $arParams['SORT_BY'] = "ASC";
	
	$arParams['AJAX'] = $_REQUEST['AJAX'];
	
	$arParams['nPageSize'] = (int)$_REQUEST['nPageSize'];
	if($arParams['nPageSize'] == 0) $arParams['nPageSize'] = 5;
	
	$arResult['ORDABC'] = (int)$_REQUEST['abc'];
	$arResult['BRAND_ID'] = (int)$_REQUEST['id'];
	$arResult['BRAND_CODE'] = $_REQUEST['elmid'];
	$arResult['LETTER'] = mb_substr($_REQUEST['let'], 0, 1);
	
	$arFilter = array(
		'ACTIVE' => "Y",
		'IBLOCK_CODE' => $arParams['BRANDS_IBLOCK_CODE']
	);
	
	$lat = range('A', 'Z');
	$rus = array(GetMessage("NOVAGR_SHOP_A"),GetMessage("NOVAGR_SHOP_B"),GetMessage("NOVAGR_SHOP_V"),GetMessage("NOVAGR_SHOP_G"),GetMessage("NOVAGR_SHOP_D"),GetMessage("NOVAGR_SHOP_E"),GetMessage("NOVAGR_SHOP_J"),GetMessage("NOVAGR_SHOP_Z"),GetMessage("NOVAGR_SHOP_I"),GetMessage("NOVAGR_SHOP_K"),GetMessage("NOVAGR_SHOP_L"),GetMessage("NOVAGR_SHOP_M"),GetMessage("NOVAGR_SHOP_N"),GetMessage("NOVAGR_SHOP_O"),GetMessage("NOVAGR_SHOP_P"),GetMessage("NOVAGR_SHOP_R"),GetMessage("NOVAGR_SHOP_S"),GetMessage("NOVAGR_SHOP_T"),GetMessage("NOVAGR_SHOP_U"),GetMessage("NOVAGR_SHOP_F"),GetMessage("NOVAGR_SHOP_H"),GetMessage("NOVAGR_SHOP_C"),GetMessage("NOVAGR_SHOP_C1"),GetMessage("NOVAGR_SHOP_S1"),GetMessage("NOVAGR_SHOP_S2"),GetMessage("NOVAGR_SHOP_E1"),GetMessage("NOVAGR_SHOP_U1"),GetMessage("NOVAGR_SHOP_A1"));
	$arSelect = array('NAME', 'CODE');

	$rsElement = CIBlockElement::GetList(array('NAME' => 'ASC'), $arFilter, false, false, $arSelect);
	$arResult['LAT'] = array();
	$arResult['RUS'] = array();
	while ($data = $rsElement -> Fetch())
	{

		$let = mb_substr($data['NAME'], 0, 1);
		if( in_array($let, $lat) && !in_array($let, $arResult['LAT']) )
			$arResult['LAT'][] = $let;
		if( in_array($rus, $lat) && !in_array($let, $arResult['RUS']) )
			$arResult['RUS'][] = $let;
		
	}
		
	if(!empty($arResult['LETTER']))
	{
		$arFilter['NAME'] = $arResult['LETTER'].'%';
		$APPLICATION -> AddChainItem(GetMessage("SIMBOL_LABEL")." '".$arResult['LETTER']."'", "");
	}
	
	if( !empty($arResult['BRAND_CODE']) )
		$arFilter['CODE'] = $arResult['BRAND_CODE'];
	
	$arSelect = array(
		'ID',
		'NAME',
		'CODE',
		'PREVIEW_PICTURE',
		'DETAIL_TEXT',
	);
	
	
	
	if($arResult['ORDABC'] > 0) $arNavStartParams = false;
	else {
		
		$arNavStartParams = array(
			"nPageSize" => $arParams['nPageSize'],
			//"iNumPage" => $iNumPage,
			"bShowAll" => false
		);
	
	}
	//  If the specified number of valid entries that do not need a navigation
	if ($arParams["COUNT_RECORDS"] > 0 ) {
		$arNavStartParams = false;
	}	
	
	$rsElement = CIBlockElement::GetList(
		array($arParams['SORT_FIELD'] => $arParams['SORT_BY']),
		$arFilter,
		false,
		$arNavStartParams,
		$arSelect
	);
	while($data = $rsElement -> GetNext())
	{
		$arResult['BRANDS'][$data['ID']] = $data;
		$PREVIEW_PICTURE_ID[$data['PREVIEW_PICTURE']] = $data['PREVIEW_PICTURE'];
	}
	
	$arResult["NAV_STRING"] = $rsElement -> GetPageNavStringEx($navComponentObject, "", "bootstrap");
	
	if( ($arResult['BRAND_ID'] == 0) && ($arResult['ORDABC'] == 0))
	{
		$arFilter = array(
			'ACTIVE' => "Y",
			'IBLOCK_CODE' => $arParams['BRANDS_IBLOCK_CODE'],
			'PROPERTY_TOP_VALUE' => "Y",
		);
		$arSelect = array(
			'ID',
			'NAME',
			'PREVIEW_PICTURE',
			'DETAIL_TEXT',
			'PROPERTY_TOP',
			'CODE'
		);
		$arNavStartParams = array('nTopCount' => 1);
		$rsElement = CIBlockElement::GetList(array('RAND' => ''), $arFilter, false, $arNavStartParams, $arSelect);
		if($data = $rsElement -> GetNext())
		{
			$arResult['TOP'] = $data;
			$PREVIEW_PICTURE_ID[$data['PREVIEW_PICTURE']] = $data['PREVIEW_PICTURE'];
		}
	}
	
	if($arResult['ORDABC'] == 0)
	{
		$arFilter = "";
		foreach($PREVIEW_PICTURE_ID as $subval) $arFilter .= $subval.",";
		$rsFile = CFile::GetList(false, array('@ID' => $arFilter));
		while($sub_data = $rsFile -> GetNext())
		{
			$PREVIEW_PICTURE_SRC[$sub_data['ID']]
				= "/upload/".$sub_data['SUBDIR']."/".$sub_data['FILE_NAME'];
		}
		foreach($PREVIEW_PICTURE_ID as $key => $val)
			$arResult['PREVIEW_PICTURE'][$key] = $PREVIEW_PICTURE_SRC[$val];
	}
}
if( !empty($arResult['BRAND_CODE']) )
	foreach($arResult['BRANDS'] as $val)
		$APPLICATION -> AddChainItem($val['NAME'], "");
$this->IncludeComponentTemplate();
?>