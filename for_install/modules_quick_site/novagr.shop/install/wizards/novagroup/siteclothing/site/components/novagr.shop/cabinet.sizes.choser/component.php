<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();


$arResult = array();

CModule::IncludeModule("iblock");


if (empty($arParams['AJAX']))
	$arParams['AJAX'] = ( $_REQUEST['AJAX'] == "Y" ? "Y" : "N" );

if (empty($arParams['AJAX']))
	$arParams['AJAX'] = ( $_REQUEST['AJAX'] == "Y" ? "Y" : "N" );

// показывать ли поле PREVIEW_PICTURE
if ($arParams['SHOW_PREVIEW_PICTURE'] != "Y") $arParams['SHOW_PREVIEW_PICTURE'] = "N";
// множественный выбор в классификаторе
if ($arParams['MULTIPLE_CHOICE'] != "Y") $arParams['MULTIPLE_CHOICE'] = "N";

// название переменной js в которой хранитс€ массив с значени€ми дл€ данного классификатора
// используетс€ дл€ работы списка с множ. выбором
if (empty($arParams['JS_ARRAY_FOR_VALUES'])) $arParams['JS_ARRAY_FOR_VALUES'] = "";

// параметры дл€ навигации
if (!empty($_REQUEST['iblid'])) $arParams['IBLOCK_ID']	= (int)$_REQUEST['iblid'];
if (empty($arParams['BUTTON_TITLE'])) $arParams['BUTTON_TITLE'] = "¬ыбрать";
//if (!empty($_REQUEST['elmid'])) $arParams['ELEMENT_ID'] = (int)$_REQUEST['elmid'];

if (empty($arParams['IBLOCK_ID'])) $arParams['IBLOCK_ID'] = 0;

if (!empty($_REQUEST['iNumPage'])) $arParams['iNumPage'] = (int)$_REQUEST['iNumPage'];
if($arParams['iNumPage'] < 1) $arParams['iNumPage'] = 1;

if (!empty($_REQUEST['nPageSize'])) $arParams['nPageSize'] = (int)$_REQUEST['nPageSize'];
if($arParams['nPageSize'] == 0) $arParams['nPageSize'] = 7	;

if (empty($arParams["STD_SIZE_IBLOCK_CODE"]) && empty($arParams['IBLOCK_ID'])) return;

if (!empty($_REQUEST['secid'])) {
	
	if (is_array($_REQUEST['secid'])) {
		$arParams['SELECTED_SECTION'] = $_REQUEST['secid'];
	} else {
		$arParams['SELECTED_SECTION'] = explode(",",$_REQUEST['secid']);
	}
}
if (empty($arParams['SELECTED_SECTION'])) $arParams['SELECTED_SECTION'] = 0;


$filterSections = array();
if (!empty($arParams["STD_SIZE_IBLOCK_CODE"])) $filterSections["IBLOCK_CODE"] = $arParams["STD_SIZE_IBLOCK_CODE"];
if (!empty($arParams["IBLOCK_ID"])) $filterSections["IBLOCK_ID"] = $arParams["IBLOCK_ID"];


if ( $this -> StartResultCache(false) ) {
//if (1==1) {

	$ar_result = CIBlockSection::GetList(Array("SORT"=>""), $filterSections,false, Array("ID", "NAME"));
	
	// если нет выбранной секции то выбираем первую
	
	while ($res=$ar_result->GetNext()){
		
		// если нет выбранной секции то выбираем первую
		/*if (empty($arParams['SELECTED_SECTION'])) {
			$arParams['SELECTED_SECTION'] = array();
			$arParams['SELECTED_SECTION'][] =  $res["ID"];
			// дл€ навигации
			$_REQUEST['secid'] = $res["ID"];
		}*/
		$arResult["SECTIONS"][$res["ID"]]["NAME"] = $res["NAME"];
		
	}
	
	
	// дл€ каждой секции получаем все элменты - нужно дл€ того, чтобы не показывать те секции
	// в которых выбраны все размеры
	$arFilter = array("ACTIVE" => "Y" );
	if (!empty($arParams["STD_SIZE_IBLOCK_CODE"])) $arFilter["IBLOCK_CODE"] = $arParams["STD_SIZE_IBLOCK_CODE"];
	if (!empty($arParams["IBLOCK_ID"])) $arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
	
	// массив ID которые нужно исключить из выборки
	//deb($_REQUEST);
	$arrExcludeIDS = array();
	if (!empty($_REQUEST["currentValues"])) {
		$arrExcludeIDS = explode(",", $_REQUEST["currentValues"]);
		
	}

	$res = CIBlockElement::GetList(
			Array("SORT"=>"ASC"),
			$arFilter, false, $arNavStartParams,
			array("ID", "NAME", "SORT", "IBLOCK_SECTION_ID")
	);
	
	while ($ob = $res->Fetch()) {
		
		$arResult["SECTIONS"][$ob["IBLOCK_SECTION_ID"]]["ELEMS"][] = $ob["ID"];
	}
	foreach ($arResult["SECTIONS"] as $key => $value) {
		$arResult["SECTIONS"][$key]["VISIBLE"] = false;
		foreach ($value["ELEMS"] as $sizeID) {
			
			if (!in_array($sizeID, $arrExcludeIDS)) { 
				$arResult["SECTIONS"][$key]["VISIBLE"] = true;
			} 
		}	
	}
	//deb($arParams);
	// если текуща€ секци€ - невидима - то берем за текущую первую видимую секцию
	if (is_array($arParams['SELECTED_SECTION']))
	foreach ($arParams['SELECTED_SECTION'] as $key => $sectionID) {
		//deb($sectionID);
		if ($arResult["SECTIONS"][$sectionID]["VISIBLE"] == false) {
			unset($arParams['SELECTED_SECTION'][$key]);
		}
	}

	if (empty($arParams['SELECTED_SECTION'])) {
		
		// если нет выбранной секции то выбираем первую
		foreach ($arResult["SECTIONS"] as $key => $value) {
			
			if ($value["VISIBLE"] == true) {
				
				$arParams['SELECTED_SECTION'] = array();
				$arParams['SELECTED_SECTION'][] =  $key;
				// дл€ навигации
				$_REQUEST['secid'] = $key;
				break;
			}
		}
	}
	
	$arResult["HTML_NEW_SIZE"] = '';
	
	if ($_REQUEST["offersIblockID"]) {
		$res = CIBlock::GetProperties($_REQUEST["offersIblockID"], Array());
		while($res_arr = $res->Fetch()) {
			if (startsWith($res_arr["CODE"], "REAL_")) {
				//deb($res_arr);
				$arResult["HTML_NEW_SIZE"] .= '<span> <label>' . $res_arr["NAME"] . '</label>
				<input type="text" title="выбрать" value="" size="5" name="' .$res_arr["CODE"] .'[###]"></span>';
			}
			
		}
	}	
	
	$arNavStartParams = array( 'nPageSize' => $arParams['nPageSize'], 'iNumPage' => $arParams['iNumPage']);
	if (empty($arParams['SELECTED_SECTION'])) $arParams['SELECTED_SECTION'] = -1;
	$arFilter = array(
		"ACTIVE" => "Y" ,
		"SECTION_ID" => $arParams['SELECTED_SECTION']	
	);
	if (!empty($arParams["STD_SIZE_IBLOCK_CODE"])) $arFilter["IBLOCK_CODE"] = $arParams["STD_SIZE_IBLOCK_CODE"];
	if (!empty($arParams["IBLOCK_ID"])) $arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
	
	if (count($arrExcludeIDS)) {
		$arFilter["!ID"] = $arrExcludeIDS;
	}
	//deb($arFilter);
	//deb($arNavStartParams);
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, $arNavStartParams);
	
	
	//deb($res->SelectedRowsCount());
	
	while ($ob = $res->Fetch()) {
		//deb($ob["ID"]. " NAME ".$ob["NAME"] . " SORT " . $ob["SORT"] . " " .$ob["IBLOCK_SECTION_ID"]);
		$arResult["CLASSIFICATOR"][] = $ob;	
		$arParams["STD_SIZE_IBLOCK_ID"] = $ob["IBLOCK_ID"];
	}	
	// передаем инфоблок в шаблон навигации
	$res->add_anchor = $arParams["STD_SIZE_IBLOCK_ID"];
	$arResult["NAV_STRING"] = $res -> GetPageNavStringEx($navComponentObject, "", "choose_classificator");
	$this->IncludeComponentTemplate();
}	
?>