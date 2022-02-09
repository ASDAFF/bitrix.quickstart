<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
 
if($_REQUEST['sort']){
    $sort = $_REQUEST['sort'];
    if(in_array($sort, array('name', 'price', 'new')))
        $_SESSION['CATALOG_SORT'] = $sort;
}
if(!$_SESSION['CATALOG_SORT'])
        $_SESSION['CATALOG_SORT'] = 'name'; // по умолчанию
$arParams["ELEMENT_SORT_FIELD"] = $_SESSION['CATALOG_SORT'];
 

if($_REQUEST['order']){
    $order = $_REQUEST['order']; 
    if(in_array($order, array('asc', 'desc')))
        $_SESSION['CATALOG_ORDER'] = $order;
} 
if(!$_SESSION['CATALOG_ORDER']) 
        $_SESSION['CATALOG_ORDER'] = 'acs'; 
$arParams["ELEMENT_SORT_ORDER"] = $_SESSION['CATALOG_ORDER'];
  
if($_REQUEST['view'])
    $_SESSION['CATALOG_SECTION_TEMPLATE'] = $_REQUEST['view'];
 
if(!$_SESSION['CATALOG_SECTION_TEMPLATE'] ||
        !in_array($_SESSION['CATALOG_SECTION_TEMPLATE'], array('grid', 'list', 'table')))
   $_SESSION['CATALOG_SECTION_TEMPLATE'] = 'grid';
$arParams['CATALOG_SECTION_TEMPLATE'] = $_SESSION['CATALOG_SECTION_TEMPLATE'];

if($_REQUEST['cnt']){
    $_REQUEST['cnt'] = intval($_REQUEST['cnt']);
    if(in_array($_REQUEST['cnt'], array(15,30,45))){
        $_SESSION['CATALOG_CNT'] = $_REQUEST['cnt'];
    }
}

if(!$_SESSION['CATALOG_CNT'])
    $_SESSION['CATALOG_CNT'] = 15;

CUtil::InitJSCore(array('popup'));

if($arParams["USE_FILTER"]=="Y")
{
	if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
		$arParams["FILTER_NAME"] = "arrFilter";
}
else
	$arParams["FILTER_NAME"] = "";

$arDefaultUrlTemplates404 = array(
	"sections" => "",
	"section" => "#SECTION_ID#/",
	"element" => "#SECTION_ID#/#ELEMENT_ID#/",
	"compare" => "compare.php?action=COMPARE",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
	"SECTION_ID",
	"SECTION_CODE",
	"ELEMENT_ID",
	"ELEMENT_CODE",
	"action",
);

if($arParams["SEF_MODE"] == "Y")
{
	$arVariables = array();

	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

	$componentPage = CComponentEngine::ParseComponentPath(
		$arParams["SEF_FOLDER"],
		$arUrlTemplates,
		$arVariables
	);
 
	if(!$componentPage && isset($_REQUEST["q"]))
		$componentPage = "search";

	$b404 = false;
	if(!$componentPage)
	{
		$componentPage = "sections";
		$b404 = true;
	}

	if(
		$componentPage == "section"
		&& isset($arVariables["SECTION_ID"])
		&& intval($arVariables["SECTION_ID"])."" !== $arVariables["SECTION_ID"]
	)
		$b404 = true;

	if($b404 && $arParams["SET_STATUS_404"]==="Y")
	{
		$folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
		if ($folder404 != "/")
			$folder404 = "/".trim($folder404, "/ \t\n\r\0\x0B")."/";
		if (substr($folder404, -1) == "/")
			$folder404 .= "index.php";

			if($folder404 != $APPLICATION->GetCurPage(true))
			CHTTP::SetStatus("404 Not Found");
	}

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
	$arResult = array(
		"FOLDER" => $arParams["SEF_FOLDER"],
		"URL_TEMPLATES" => $arUrlTemplates,
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
     
     
}
else
{
	$arVariables = array();

	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);
 
	$componentPage = "";

	$arCompareCommands = array(
		"COMPARE",
		"DELETE_FEATURE",
		"ADD_FEATURE",
		"DELETE_FROM_COMPARE_RESULT",
		"ADD_TO_COMPARE_RESULT",
		"COMPARE_BUY",
		"COMPARE_ADD2BASKET",
	);

	if(isset($arVariables["action"]) && in_array($arVariables["action"], $arCompareCommands))
		$componentPage = "compare";
	elseif(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
		$componentPage = "element";
	elseif(isset($arVariables["ELEMENT_CODE"]) && strlen($arVariables["ELEMENT_CODE"]) > 0)
		$componentPage = "element";
	elseif(isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
		$componentPage = "section";
	elseif(isset($arVariables["SECTION_CODE"]) && strlen($arVariables["SECTION_CODE"]) > 0)
		$componentPage = "section";
	elseif(isset($_REQUEST["q"]))
		$componentPage = "search";
	else 
            $componentPage = "sections";

     
	$arResult = array(
		"FOLDER" => "",
		"URL_TEMPLATES" => Array(
			"section" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#",
			"element" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#"."&".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#",
			"compare" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["action"]."=COMPARE",
		),
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
}
 
if($arVariables["SECTION_CODE"]) {
    
    if($_SESSION["CATALOG_COMPARE_LIST"]['SECTION_CODE'] != $arVariables["SECTION_CODE"])
        unset($_SESSION["CATALOG_COMPARE_LIST"]['ITEMS']);
     
    $_SESSION["CATALOG_COMPARE_LIST"]['SECTION_CODE'] = $arVariables["SECTION_CODE"];
}
 
if($arResult['VARIABLES']['IBLOCK_CODE'] && 
        !$arResult['VARIABLES']['IBLOCK_ID'] 
        && !$arResult['IBLOCK_ID']){     
 
   CModule::IncludeModule('iblock');  
    
   $res = CIBlock::GetList(Array(), Array("CODE"=>$arResult['VARIABLES']['IBLOCK_CODE'] ), true);
   
   if($ar_res = $res->Fetch()) 
        $arResult['IBLOCK_ID'] = $arResult['VARIABLES']['IBLOCK_ID'] = $ar_res['ID'];
 
}
 



if($arVariables["SECTION_CODE"] && !$arParams['IBLOCK_ID']){
    
  CModule::IncludeModule('iblock');  
    
  $db_list = CIBlockSection::GetList(Array(), array('CODE'=>$arVariables["SECTION_CODE"]), true);
 
  if($ar_result = $db_list->GetNext()){
      $arParams['IBLOCK_ID']  =  $ar_result['IBLOCK_ID'];
      $arParams['SECTION_ID']  =  $ar_result['ID']; 
      }
 
}

if ($arParams['IBLOCK_ID']) {
    CModule::IncludeModule('iblock');
    $res = CIBlock::GetByID($arParams['IBLOCK_ID']);
    if ($ar_res = $res->GetNext())
        $APPLICATION->AddChainItem($ar_res['NAME'], "/catalog/" . $ar_res['CODE'] . '/');
}

$this->IncludeComponentTemplate($componentPage);
 