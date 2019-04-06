<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
		"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
		"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
		"ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
		"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);

$arPrice = array();
if(CModule::IncludeModule("catalog")){
	$rsPrice=CCatalogGroup::GetList(array("SORT" => "ASC"), array(), false, false, array('ID', 'NAME'));
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["ID"]] = $arr["NAME"]; 
}

$arPropertyFields_Data = array('ID'=>'ID', 'NAME'=>GetMessage("NAME")); 

$arPropertyFont = array(
		'Arial'=>'Arial',
		'Calibri'=>'Calibri',
		'Times New Roman'=>'Times New Roman',
		'Tahoma'=>'Tahoma',
		'Courier New'=>'Courier New'
);

$arPropertyFontSize = array(
		'8'=>'8',
		'9'=>'9',
		'10'=>'10',
		'11'=>'11',
		'12'=>'12',
		'14'=>'14',
		'16'=>'16',
		'18'=>'18',
		'20'=>'20',
		'22'=>'22'
);


$arFormated = array(
		'xlsx'=>'[xlsx] Excel 2007',
		'xls'=>'[xls] Excel 2005',
		'csv'=>'[csv] CSV',
		'htm'=>'[htm] HTML'/*  ,
		'pdf'=>'[pdf] PDF', */
);

$arSort = array(
		'ASC'=>GetMessage("ASC"),
		'DESC'=>GetMessage("DESC")
);

$arCurrency = array(
		GetMessage("RUB")=>GetMessage("RUB"),
		GetMessage("USD")=>GetMessage("USD"),
		GetMessage("EURO")=>GetMessage("EURO"),
		'iblock'=>GetMessage("IBLOCK_CUR"),
		'main'=>GetMessage("MAIN_CUR")
);

if(ini_get('mbstring.func_overload')!=0)unset($arFormated['xls']);
if(!extension_loaded('xmlwriter')) unset($arFormated['xlsx']);

$arProperty_Data = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = $arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_Data[$arr["CODE"]] = $arr["NAME"];
	}
}

$arComponentParameters = array(
	"GROUPS" => array(
		"FORMATED" => array(
			"NAME" => GetMessage("FORMATED_PHR"),
			"SORT" => 252,
		),
		"FILE" => array(
			"NAME" => GetMessage("FILE_PHR"),
			"SORT" => 500,
		),
		"FORMATED" => array(
			"NAME" => GetMessage("FORMATED_PHR"),
			"SORT" => 250,
		),
		"SORT_ELEMENT" => array(
			"NAME" => GetMessage("SORT_ELEMENT_PHR"),
			"SORT" => 250,
		),
		"SORT_SECTION" => array(
			"NAME" => GetMessage("SORT_SECTION_PHR"),
			"SORT" => 251,
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_PRICE_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_PRICE_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["ID"]}',
			"REFRESH" => "Y",
		),
		"FIELD_CODE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_FIELD_PRICE_LIST"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPropertyFields_Data,
			"REFRESH" => "Y",
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_PROPERTY_PRICE_LIST"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Data,
			"REFRESH" => "Y",
		),
		"CHECK_DATES" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_CHECK_DATES_PRICE_LIST"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"CHECK_STOCK" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_CHECK_STOCK_LIST"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PRICE_CODE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"VALUES" => $arPrice,
			"DEFAULT" => "1",
		),
		"CURRENCY" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CURRENCY"),
				"TYPE" => "LIST",
				"VALUES" => $arCurrency,
				"DEFAULT" => "iblock",
				"REFRESH" => "Y",
		),
		"FORMATED_FILE" => array(
			"PARENT" => "FORMATED",
			"NAME" => GetMessage("FORMATED_FILE"),
			"TYPE" => "LIST",
			"VALUES" => $arFormated,
			"DEFAULT" => 'xlsx',
			"REFRESH" => "Y",
		),
		"FORMATED_FILE_NAME" => array(
			"PARENT" => "FILE",
			"NAME" => GetMessage("FORMATED_FILE_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => 'price',
		),
		"FORMATED_FILE_DIR" => array(
			"PARENT" => "FILE",
			"NAME" => GetMessage("FORMATED_FILE_DIR"),
			"TYPE" => "STRING",
			"DEFAULT" => '/upload/',
		),
		"CHECK_SECTION" => array(
			"PARENT" => "SORT_SECTION",
			"NAME" => GetMessage("CHECK_SECTION_MSS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		),
		"HEADER" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("HEADER_MSS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"NAME_COLS" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("NAME_COLS_MSS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"NULL" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("NULL"),
			"TYPE" => "STRING",
			"DEFAULT" => "-",
		),
		"MULTI_SEPARATOR" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MULTI_SEPARATOR"),
			"TYPE" => "STRING",
			"DEFAULT" => ",",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>0),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		
	),
);
$myElementSort=array();
if (count($arCurrentValues['FIELD_CODE']) || count($arCurrentValues['PROPERTY_CODE'])){
	
	for($i=0;$i<count($arCurrentValues['FIELD_CODE']);$i++)
		$myElementSort[]=$arPropertyFields_Data[$arCurrentValues['FIELD_CODE'][$i]];
	
	for($i=0;$i<count($arCurrentValues['PROPERTY_CODE']);$i++)
		$myElementSort[]=$arProperty_Data[$arCurrentValues['PROPERTY_CODE'][$i]];
	
	if(CModule::IncludeModule("catalog")){
		$myElementSort[]=GetMessage("PRICE_SORT");
		$myElementSort[]=GetMessage("CURRENCY_SORT");
	}
	if($arCurrentValues['COLS_SECTION']=='Y' && $arCurrentValues['CHECK_PARENT']!='Y'){
		$myElementSort[]=GetMessage("SECTION_ID");
		$myElementSort[]=GetMessage("SECTION_NAME");
	}
	$max=0;
	if($arCurrentValues['COLS_SECTION']=='Y' && $arCurrentValues['CHECK_PARENT']=='Y'){
		$db_list = CIBlockSection::GetList(Array(), array('IBLOCK_ID'=>$arCurrentValues['IBLOCK_ID']), true, array('ID', 'DEPTH_LEVEL'));
		while($ar_result = $db_list->GetNext())
			$max=($ar_result['DEPTH_LEVEL']>$max)?$ar_result['DEPTH_LEVEL']:$max;
		
		for($i=1; $i<=$max; $i++){
			$myElementSort[]=GetMessage("SECTION_ID")." #".$i;
			$myElementSort[]=GetMessage("SECTION_NAME")." #".$i;
		}
	}
	
	
	$arComponentParameters['PARAMETERS']['ELEMENT_SORT_BY'] = array(
			"PARENT" => "SORT_ELEMENT",
			"NAME" => GetMessage("ELEMENT_SORT_BY"),
			"TYPE" => "LIST",
			"VALUES" => $myElementSort,
			"DEFAULT" => 'ID',
	);
	
	$arComponentParameters['PARAMETERS']['ELEMENT_SORT'] = array(
			"PARENT" => "SORT_ELEMENT",
			"NAME" => GetMessage("ELEMENT_SORT"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"DEFAULT" => 'ASC',
	);
}

if ($arCurrentValues['FORMATED_FILE']!='csv'){
	$arComponentParameters['PARAMETERS']['COLOR'] = array(		
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("COLOR"),
		"TYPE" => "COLORPICKER",
		"DEFAULT" => '#C0C0C0',
	);
	$arComponentParameters['PARAMETERS']['FONT'] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("FONT"),
		"TYPE" => "LIST",
		"VALUES" => $arPropertyFont,
		"DEFAULT" => 'Arial',
	);
	$arComponentParameters['PARAMETERS']['FONT_SIZE'] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("FONT_SIZE"),
		"TYPE" => "LIST",
		"VALUES" => $arPropertyFontSize,
		"DEFAULT" => '10',
	);
	$arComponentParameters['PARAMETERS']['FONT_COLOR'] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("FONT_COLOR"),
			"TYPE" => "COLORPICKER",
			"DEFAULT" => '#000000',
	);
}

if ($arCurrentValues['FORMATED_FILE']=='csv'){
	$arComponentParameters['PARAMETERS']['CSV_SEPARATOR'] = array(
			"PARENT" => "FORMATED",
			'NAME' => GetMessage("CSV_SEPARATOR"),
			'TYPE' => 'STRING',
			"DEFAULT" => ';',
	);
}

if ($arCurrentValues['CURRENCY']=='main'){
	$arComponentParameters['PARAMETERS']['MAIN_CURRENCY'] = array(
			"PARENT" => "DATA_SOURCE",
			'NAME' => GetMessage("MAIN_CURRENCY"),
			'TYPE' => 'STRING',
			"DEFAULT" => GetMessage("RUB"),
	);
}

if ($arCurrentValues['CHECK_SECTION']=='Y'){
	$arComponentParameters['PARAMETERS']['COLS_SECTION'] = array(
			"PARENT" => "SORT_SECTION",
			"NAME" => GetMessage("COLS_SECTION_MSS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	$arComponentParameters['PARAMETERS']['CHECK_PARENT'] = array(
			"PARENT" => "SORT_SECTION",
			"NAME" => GetMessage("CHECK_PARRENT_MSS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['COLS_SECTION']!='Y'){
		
	$arComponentParameters['PARAMETERS']['SECTION_SORT_BY'] = array(
			"PARENT" => "SORT_SECTION",
			'NAME' => GetMessage("SECTION_SORT_BY"),
			'TYPE' => 'LIST',
			"VALUES" => $arPropertyFields_Data,
			"DEFAULT" => 'NAME',
	);
	$arComponentParameters['PARAMETERS']['SECTION_SORT'] = array(
			"PARENT" => "SORT_SECTION",
			'NAME' => GetMessage("SECTION_SORT"),
			'TYPE' => 'LIST',
			"VALUES" => $arSort,
			"DEFAULT" => 'ASC',
	);
	
	}
}

if(!CModule::IncludeModule("catalog")){
	unset($arComponentParameters['PARAMETERS']['PRICE_CODE']);
	unset($arComponentParameters['PARAMETERS']['CHECK_STOCK']);
	unset($arComponentParameters['PARAMETERS']['CURRENCY']);
}

?>
