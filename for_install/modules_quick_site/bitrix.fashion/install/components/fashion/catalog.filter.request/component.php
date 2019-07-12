<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

if(strlen($arParams["FILTER_NAME"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arParams["FILTER_NAME"] = "arrFilter";
$FILTER_NAME = $arParams["FILTER_NAME"];

global $$FILTER_NAME;
$$FILTER_NAME = array();

if(strlen($arParams["CURRENT_PARAMS_NAME"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["CURRENT_PARAMS_NAME"]))
	$arParams["CURRENT_PARAMS_NAME"] = "arrCurrent";
$CURRENT_PARAMS_NAME = $arParams["CURRENT_PARAMS_NAME"];

global $$CURRENT_PARAMS_NAME;
$$CURRENT_PARAMS_NAME = array();

$arGETPropsModels = $arGETPropsOffers = array();
foreach($_GET as $code => $value){
	if(substr($code, 0, 1)=='m'){
		$pc = ltrim($code, 'm');
		$arGETPropsModels[$pc] = $value;
	}
	if(substr($code, 0, 1)=='o'){
		$pc = ltrim($code, 'o');
		$arGETPropsOffers[$pc] = $value;
	}
}

if(!empty($arGETPropsModels)||!empty($arGETPropsOffers)){
	CModule::IncludeModule('iblock');
	
	${$CURRENT_PARAMS_NAME} = $arGETPropsModels+$arGETPropsOffers;

	if(!empty($arGETPropsModels)){
		${$FILTER_NAME}['PROPERTY'] = $arGETPropsModels;
	}

	if(!empty($arGETPropsOffers)){
		$arOffersIBlock = CIBlockPriceTools::GetOffersIBlock($arParams["IBLOCK_ID"]);

		$arSubFilter = array("IBLOCK_ID" => $arOffersIBlock["OFFERS_IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
		$arPriceFilter = array();
		foreach($arGETPropsOffers as $prop_id => $value_id){
			if(strpos($prop_id, 'price_')!==false){
				$arPriceInfo = explode(":", $prop_id);
				if(count($arPriceInfo)==2){
					if($arPriceInfo[0] == 'price_from')
						$arPriceFilter['>=CATALOG_PRICE_'.$arPriceInfo[1]] = $value_id;
					else
						$arPriceFilter['<=CATALOG_PRICE_'.$arPriceInfo[1]] = $value_id;
				}
			}else{
				$arSubFilter['PROPERTY_'.$prop_id] = $value_id;
			}
		}
		${$FILTER_NAME}["=ID"] = CIBlockElement::SubQuery("PROPERTY_".$arOffersIBlock['OFFERS_PROPERTY_ID'], $arSubFilter);

		if(!empty($arPriceFilter))
		{
			$arSubFilter = $arPriceFilter;
			$arSubFilter["IBLOCK_ID"] = $arOffersIBlock["OFFERS_IBLOCK_ID"];
			$arSubFilter["ACTIVE_DATE"] = "Y";
			$arSubFilter["ACTIVE"] = "Y";
			${$FILTER_NAME}[] = array(
				"LOGIC" => "OR",
				array($arPriceFilter),
				"=ID" => CIBlockElement::SubQuery("PROPERTY_".$arOffersIBlock["OFFERS_PROPERTY_ID"], $arSubFilter),
			);
		}
	}
}

//print '<pre>';
//print_r(${$FILTER_NAME});
//print '</pre>';
?>