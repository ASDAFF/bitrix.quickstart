<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("fileman");
CMedialib::Init();

//$ar = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y', 'PARENT_ID' => 0)));
$res = CMedialib::GetCollectionTree();

function GetDepth($tree, $cur_dept, $return){
    foreach($tree as $tree_item ){
        $return[$tree_item['id']] = $cur_dept;
        if(sizeof($tree_item['child']) > 0){
            $dep = $cur_dept + 1 ;
            GetDepth($tree_item['child'], $dep, &$return);
        }
    }
    return $return;
}

$ar = CMedialibCollection::GetList(array(
    'arFilter' => array('ACTIVE' => 'Y', 'ML_TYPE' => 1),
//    'arOrder' => array('PARENT_ID' => 'asc')
));

$arDepth = GetDepth($res['arColTree'], 0, array());
$arCollList = array();
foreach($arDepth as $IdItem => $Depth){
    foreach($ar as $arCol){
        if($arCol['ID'] == $IdItem){
            $arCol['DEPTH'] = $Depth;
            $arCollList[] = $arCol;
        }
    }
}


$arCollection = array();
foreach($arCollList as $Collection){
    $arCollection[$Collection['ID']] = str_repeat('. ', $Collection['DEPTH']).' '.$Collection['NAME'];
}

$arCollectionMulti = array();




// echo "<pre>"; print_r($ar); echo '</pre>';
$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
		"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
		"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
//		"ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
//		"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
//		"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"ROOT_COLLECTIONS"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("EPIR_PARAM_COLL"),
			"TYPE" => "LIST",
			"VALUES" => $arCollection,
			"DEFAULT" => '',
			"MULTIPLE" => "Y",
		),
        "CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
        "SORT_BY" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_FSORT"),
            "TYPE" => "LIST",
            "DEFAULT" => "ID",
            "VALUES" => $arSortFields,
//                "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ASC",
            "VALUES" => $arSorts,
//            "ADDITIONAL_VALUES" => "Y",
        ),
        "COLLECTION_VARIABLE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("EPIR_PARAM_PEREM"),
            "TYPE"		=> "STRING",
            "DEFAULT"	=> "ID_COL"
        ),
        'INCLUDE_JQUERY' => array(
            "PARENT" => "BASE",
			"NAME" => GetMessage("EPIR_PARAM_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
        ),
        'INCLUDE_FANCYBOX' => array(
            "PARENT" => "BASE",
			"NAME" => GetMessage("EPIR_PARAM_FANCY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
        ),
        'INCLUDE_LAZY' => array(
            "PARENT" => "BASE",
			"NAME" => GetMessage("EPIR_PARAM_LAZY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
        ),
        'SHOW_TITLE' => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("EPIR_PARAM_SHOW_TITLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        'SET_HEADER' => array(
            "PARENT" => "BASE",
			"NAME" => GetMessage("EPIR_PARAM_SET_HEADER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
        ),
        'SECTION_SORT' => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("EPIR_PARAM_SET_DIR_SORT"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "",
        )
    )
);
