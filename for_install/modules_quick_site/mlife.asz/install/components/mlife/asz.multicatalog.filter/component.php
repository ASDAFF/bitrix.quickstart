<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//use Bitrix\Highloadblock as HL;
//use Bitrix\Main\Entity;
global $DB;
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;
/** @global CCacheManager $CACHE_MANAGER */
global $CACHE_MANAGER;

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["SECTION_ID"] = intval($arParams["SECTION_ID"]);
$arParams["SECTION_CODE"] = trim($arParams["SECTION_CODE"]);

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])){
	global $arFilterMain;
}
else
{
	$arFilterMain = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arFilterMain))
		$arFilterMain = array();
}

$filterVar = array();
if(strlen($_REQUEST["set_filter"]) > 0 && is_array($arParams['PROPERTY_CODE']))
{
	foreach($arParams['PROPERTY_CODE'] as $propFilter){
		if(isset($_REQUEST["ft_".$propFilter]))
			$filterVar["PROPERTY_".$propFilter] = explode(",",htmlspecialcharsBack($_REQUEST["ft_".$propFilter]));
	}
}

if($_REQUEST['ajaxfilter']==1) {
	$APPLICATION->RestartBuffer();
}

if($this->StartResultCache(false, array($filterVar))){

	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult["PROP_DATA"] = array();
	$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
	while ($prop_fields = $properties->GetNext(false,false))
	{
	  $arResult["PROP_DATA"][$prop_fields["ID"]] = $prop_fields;
	}
	
	//формируем значения для фильтра
	$arResult["CUR_FILTER"] = array();
	foreach($filterVar as $key=>$val) {
		
		$propcode = str_replace("PROPERTY_","",$key);
		if($arResult["PROP_DATA"][$propcode]['PROPERTY_TYPE']=='S' || $arResult["PROP_DATA"][$propcode]['PROPERTY_TYPE'] == 'L'){
			$arResult["CUR_FILTER"]["PROPERTY_".$propcode.""] = $val;
		}else{
			$arResult["CUR_FILTER"][">=PROPERTY_".$propcode.""] = $val[0];
			$arResult["CUR_FILTER"]["<=PROPERTY_".$propcode.""] = $val[1];
		}
	}

	$arFilter = array(
			"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
			"ACTIVE"=>"Y",
			);
	if($arParams["SECTION_ID"]>0) {
		$arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
		$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	}
	elseif(strlen($arParams["SECTION_CODE"])>0) {
		$arFilter["SECTION_CODE"] = $arParams["SECTION_CODE"];
		$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	}
	if(isset($TOWN) && intval($TOWN)>0) {
		$arFilter["PROPERTY_TOWN"] = intval($TOWN);
	}
	
	$arResult["PROP_VALUES"] = array();
	$arResult["PROP_CUR_VALUES"] = array();
	foreach($arParams['PROPERTY_CODE'] as $param) {
		if(strlen($param)>0){
			
			if(isset($arResult["PROP_DATA"][$param]['PROPERTY_TYPE'])){
				if($arResult["PROP_DATA"][$param]['PROPERTY_TYPE']=="L" || $arResult["PROP_DATA"][$param]['PROPERTY_TYPE']=="S") {
					$arGroupBy = Array("PROPERTY_".$param);
					
					$res = CIBlockElement::GetList(array(), $arFilter, $arGroupBy, false, array());
					while($ob = $res->GetNextElement(false,false))
					{
						$arFields = $ob->GetFields();
						$arResult["PROP_VALUES"][$param][] = $arFields;
					}
					
					$activeFilter = $arResult["CUR_FILTER"];
					unset($activeFilter["PROPERTY_".$param.""]);
					
					$tempArFields = array();
					$res = CIBlockElement::GetList(array(), array_merge($activeFilter,$arFilter), $arGroupBy, false, array());
					while($ob = $res->GetNextElement(false,false))
					{
						$arFields = $ob->GetFields();
						$tempArFields[] = $arFields;
					}

					$i=0;
					foreach($tempArFields as $key=>$valcur) {
						while(isset($arResult["PROP_VALUES"][$param][$i])) {
							if($arResult["PROP_VALUES"][$param][$i]["PROPERTY_".$param."_VALUE"] != $valcur["PROPERTY_".$param."_VALUE"]){
								$i++;
							}else{
								$arResult["PROP_CUR_VALUES"][$param][$i] = $valcur;
								$i++;
								break;
							}
							
						}
					}

					//устанавливаем активность и ссылки
					foreach($arResult["PROP_CUR_VALUES"][$param] as &$prop) {
						$url = '';
						$urlParam = getUrlparamcur($param,$arResult["CUR_FILTER"],$prop["PROPERTY_".$param."_ENUM_ID"]);
						if(isset($urlParam[0]) && $urlParam[0]) {
							$url = '&ft_'.$param.'='.$urlParam[0];
						}
						$prop['LINK'] = $APPLICATION->GetCurPageParam('set_filter=1'.$url, array('PAGEN_1','ajaxcatalog','ajaxfilter','set_filter','ft_'.$param));
						$prop['LINK'] = urldecode($prop['LINK']);
						$prop['ACTIVE'] = (isset($urlParam[1]) && $urlParam[1]) ? "Y" : "N";
					
					}
				}elseif($arResult["PROP_DATA"][$param]['PROPERTY_TYPE']=="N") {
					
					if(isset($activeFilter[">=PROPERTY_".$param.""])) {
						$min = $activeFilter[">=PROPERTY_".$param.""];
					}
					if(isset($activeFilter[">=PROPERTY_".$param.""])) {
						$max = $activeFilter["<=PROPERTY_".$param.""];
					}
					
					$activeFilter = $arResult["CUR_FILTER"];
					unset($activeFilter[">=PROPERTY_".$param.""]);
					unset($activeFilter["<=PROPERTY_".$param.""]);
					$activeFilter[">PROPERTY_".$param.""] = 0;
					
					$tempArFields = array();
					$res = CIBlockElement::GetList(array("PROPERTY_".$param => "asc"), array_merge($activeFilter,$arFilter), false, false, array("PROPERTY_".$param));
					if($ob = $res->GetNextElement(false,false))
					{
						$arFields = $ob->GetFields();
						$arResult["PROP_VALUES"][$param][] = $arFields;
					}
					$res = CIBlockElement::GetList(array("PROPERTY_".$param => "desc"), array_merge($activeFilter,$arFilter), false, false, array("PROPERTY_".$param));
					if($ob = $res->GetNextElement(false,false))
					{
						$arFields = $ob->GetFields();
						$arResult["PROP_VALUES"][$param][] = $arFields;
					}
					
					$arResult["PROP_VALUES_MIN"][$param] = $min;
					$arResult["PROP_VALUES_MAX"][$param] = $max;
					
				}
			}
		}
	}
	
	$arResult['URL_RESET'] = $APPLICATION->GetCurPage();
	if(!isset($arResult['URL_SET'])) $arResult['URL_SET'] = $APPLICATION->GetCurPageParam("set_filter=1",array('PAGEN_1','ajaxcatalog','ajaxfilter','set_filter'));
	
	$this->SetResultCacheKeys(array(
		"CUR_FILTER"
	));
	$this->IncludeComponentTemplate();

}

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])){
	$arFilterMain = $arResult["CUR_FILTER"];
}
else
{
	$GLOBALS[$arParams["FILTER_NAME"]] = $arResult["CUR_FILTER"];
}

if($_REQUEST['ajaxfilter']==1) {
	die();
}

function getUrlparamcur($propcode,$arCur,$val) {
	
	$active = false;
	
	if(is_array($arCur["PROPERTY_".$propcode])){
		$url = implode(",",$arCur["PROPERTY_".$propcode]);
		if(!in_array($val,$arCur["PROPERTY_".$propcode])) {
			$url .= ','.$val;
		}else{
			$active = true;
		}
		if($active) {
			$url = preg_replace("#(?:^(?:".$val.")$)|(?:(.*)(?:,".$val.")$)|(?:(?:^".$val.",)(.*))|(?:(.*)(?:,".$val.")(,.*))#","$1$2",$url);
		}
		$url = urlencode($url);
		return array($url,$active);
	}else{
		return array($val,$active);
	}
	
}
?>