<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");


$_REQUEST["q"] = 'Lorem ipsum dolor sit amet, consecte';


$arParams=array(
  "arrWHERE" => array(
    0 => "iblock_informations"
  ),
  "arrFILTER" => array(
    0 => "iblock_informations"
  ),
  "arrFILTER_iblock_informations" => array(
    0 => "7"
  )
);

/**
 * $arParams, 
 * $request
 */
function getSearchedElement( $arParams, $request ){
	if(!CModule::IncludeModule("search")){
		return false;
	}
	
	$arParams["SHOW_WHEN"] = $arParams["SHOW_WHEN"]=="Y";
	if(!is_array($arParams["arrWHERE"]))
		$arParams["arrWHERE"] = array();
	
	$arParams["PAGE_RESULT_COUNT"] = 50;
	
	
	if($arParams["DEFAULT_SORT"] !== "date")
		$arParams["DEFAULT_SORT"] = "rank";
	
	if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
		$arFILTERCustom = array();
	else
	{
		$arFILTERCustom = $GLOBALS[$arParams["FILTER_NAME"]];
		if(!is_array($arFILTERCustom))
			$arFILTERCustom = array();
	}
	
	$exFILTER = CSearchParameters::ConvertParamsToFilter($arParams, "arrFILTER");
	
	//options
	if(isset($_REQUEST["q"]))
		$q = trim($_REQUEST["q"]);
	else
		$q = false;
	
	if($q!==false)
	{
		if($arParams["USE_LANGUAGE_GUESS"] == "N" || isset($_REQUEST["spell"]))
		{
			$arResult["REQUEST"]["~QUERY"] = $q;
			$arResult["REQUEST"]["QUERY"] = htmlspecialcharsex($q);
		}
		else
		{
			$arLang = CSearchLanguage::GuessLanguage($q);
			if(is_array($arLang) && $arLang["from"] != $arLang["to"])
			{
				$arResult["REQUEST"]["~ORIGINAL_QUERY"] = $q;
				$arResult["REQUEST"]["ORIGINAL_QUERY"] = htmlspecialcharsex($q);
	
				$arResult["REQUEST"]["~QUERY"] = CSearchLanguage::ConvertKeyboardLayout($arResult["REQUEST"]["~ORIGINAL_QUERY"], $arLang["from"], $arLang["to"]);
				$arResult["REQUEST"]["QUERY"] = htmlspecialcharsex($arResult["REQUEST"]["~QUERY"]);
			}
			else
			{
				$arResult["REQUEST"]["~QUERY"] = $q;
				$arResult["REQUEST"]["QUERY"] = htmlspecialcharsex($q);
			}
		}
	
	}
	
	
	$arResult["URL"] = $APPLICATION->GetCurPage()
		."?q=".urlencode($q)
		.(isset($_REQUEST["spell"])? "&amp;spell=1": "")
		.($tags!==false? "&amp;tags=".urlencode($tags): "")
	;
	
	if(isset($arResult["REQUEST"]["~ORIGINAL_QUERY"]))
	{
		$arResult["ORIGINAL_QUERY_URL"] = $APPLICATION->GetCurPage()
			."?q=".urlencode($arResult["REQUEST"]["~ORIGINAL_QUERY"])
			."&amp;spell=1"
			."&amp;where=".urlencode($arResult["REQUEST"]["WHERE"])
			.($arResult["REQUEST"]["HOW"]=="d"? "&amp;how=d": "")
			.($arResult["REQUEST"]["FROM"]? '&amp;from='.urlencode($arResult["REQUEST"]["~FROM"]): "")
			.($arResult["REQUEST"]["TO"]? '&amp;to='.urlencode($arResult["REQUEST"]["~TO"]): "")
			.($tags!==false? "&amp;tags=".urlencode($tags): "")
		;
	}
	
	$templatePage = "";
	$arReturn = false;
	
		
	$arFilter = array(
		"SITE_ID" => SITE_ID,
		"QUERY" => $arResult["REQUEST"]["~QUERY"],
		"TAGS" => $arResult["REQUEST"]["~TAGS"],
	);
	$arFilter = array_merge($arFILTERCustom, $arFilter);
	
	if($from)
		$arFilter[">=DATE_CHANGE"] = $from;
	if($to)
		$arFilter["<=DATE_CHANGE"] = $to;
	
	$obSearch = new CSearch();
	
	//When restart option is set we will ignore error on query with only stop words
	$obSearch->SetOptions(array(
		"ERROR_ON_EMPTY_STEM" => $arParams["RESTART"] != "Y",
		"NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"] == "Y",
	));
	
	$obSearch->Search($arFilter, $aSort, $exFILTER);
	
	$arResult["ERROR_CODE"] = $obSearch->errorno;
	$arResult["ERROR_TEXT"] = $obSearch->error;
	
	$arResult["SEARCH"] = array();
	if($obSearch->errorno==0)
	{
		$obSearch->NavStart($arParams["PAGE_RESULT_COUNT"], false);
		$ar = $obSearch->GetNext();
		//Search restart
		if(!$ar && ($arParams["RESTART"] == "Y") && $obSearch->Query->bStemming)
		{
			$exFILTER["STEMMING"] = false;
			$obSearch = new CSearch();
			$obSearch->Search($arFilter, $aSort, $exFILTER);
	
			$arResult["ERROR_CODE"] = $obSearch->errorno;
			$arResult["ERROR_TEXT"] = $obSearch->error;
	
			if($obSearch->errorno == 0)
			{
				$obSearch->NavStart($arParams["PAGE_RESULT_COUNT"], false);
				$ar = $obSearch->GetNext();
			}
		}
	
		$arReturn = array();
		while($ar)
		{
			$arReturn[$ar["ID"]] = $ar["ITEM_ID"];
			$ar["CHAIN_PATH"] = $APPLICATION->GetNavChain($ar["URL"], 0, $folderPath."/chain_template.php", true, false);
			$ar["URL"] = htmlspecialcharsbx($ar["URL"]);
			$ar["TAGS"] = array();
			if (!empty($ar["~TAGS_FORMATED"]))
			{
				foreach ($ar["~TAGS_FORMATED"] as $name => $tag)
				{
					if($arParams["TAGS_INHERIT"] == "Y")
					{
						$arTags = $arResult["REQUEST"]["~TAGS_ARRAY"];
						$arTags[$tag] = $tag;
						$tags = implode("," , $arTags);
					}
					else
					{
						$tags = $tag;
					}
					$ar["TAGS"][] = array(
						"URL" => $APPLICATION->GetCurPageParam("tags=".urlencode($tags), array("tags")),
						"TAG_NAME" => htmlspecialcharsex($name),
					);
				}
			}
			$arResult["SEARCH"][]=$ar;
			$ar = $obSearch->GetNext();
		}
		
		
		return $arResult;
	}	
}//end function 


