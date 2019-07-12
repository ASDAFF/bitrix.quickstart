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

$arParams["D_PROP_119"] = "MODE4";
$arParams["D_PROP_121"] = "MODE4";
$arParams["D_PROP_122"] = "MODE1";

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

//кеширование параметров свойств, для инфоблока
$obCache = \Bitrix\Main\Data\Cache::createInstance();
$cache_time = 86400;
$cache_id = 'prop_ib.'.$arParams["IBLOCK_ID"];
$cache_dir = "/mlife/mlife.porta.filter/prop/";

if( $obCache->initCache($cache_time,$cache_id,$cache_dir) )
{
	$vars = $obCache->GetVars();
}
elseif( $obCache->startDataCache()  )
{
	if (defined('BX_COMP_MANAGED_CACHE')){
	$CACHE_MANAGER->StartTagCache($cache_dir);
	$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]); 
	$CACHE_MANAGER->EndTagCache();
	}
	
	$arResult["PROP_DATA"] = array();
	$arPropsL = array();
	$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
	while ($prop_fields = $properties->GetNext(false,false))
	{
		if($prop_fields["PROPERTY_TYPE"]=="L" && in_array($prop_fields["ID"], $arParams['PROPERTY_CODE'])){
			$arPropsL[] = $prop_fields["ID"];
		}
		$arResult["PROP_DATA"][$prop_fields["ID"]] = $prop_fields;
		$arResult["PROP_DATA_LINK"][$prop_fields["CODE"]] = $prop_fields["ID"];
	}

	if(!empty($arPropsL)){
		$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], /*"PROPERTY_ID"=>$arPropsL*/));
		while($enum_fields = $property_enums->GetNext())
		{
			$arResult["PROP_DATA"][$enum_fields["PROPERTY_ID"]]["VALUES"][$enum_fields["XML_ID"]] = $enum_fields["ID"];
			$arResult["PROP_DATA"][$enum_fields["PROPERTY_ID"]]["VALUES_ID"][$enum_fields["ID"]] = $enum_fields["XML_ID"];
		}
	}
	$vars = $arResult;
	
	$obCache->endDataCache($vars);
}
$arResult["PROP_DATA"] = $vars["PROP_DATA"];
$arResult["PROP_DATA_LINK"] = $vars["PROP_DATA_LINK"];

$arResult["CURPAGE"] = $APPLICATION->GetCurPage(false);

$stat404 = false;
$filterVar = array();

if($arParams["FILTER_ID"]) {
	$arResult["CURPAGE"] = preg_replace("/(.*)\/".MlifeAszMulticatalogFilterComponent::$cacheTemplateUrl."(.*)/is","$1/",$arResult["CURPAGE"]);
	
	$paramFilter = explode("/",$arParams["FILTER_ID"]);

	$filterVarTmp = array();
	foreach($paramFilter as $val){
		if($val) {
			preg_match("/([^-]+)-(.*)/is",$val,$tmp);
			unset($tmp[0]); $tmp = array_values($tmp);
			
			if(count($tmp)==2){
				$tempCode = mb_strtoupper($tmp[0]);
				if(strpos($tmp[1],'-or-') === false){
					if(isset($arResult["PROP_DATA"][$arResult["PROP_DATA_LINK"][$tempCode]]["CODE"])){
						$tmp[0] = $arResult["PROP_DATA"][$arResult["PROP_DATA_LINK"][$tempCode]]["ID"];
						if($arResult["PROP_DATA"][$arResult["PROP_DATA_LINK"][$tempCode]]["PROPERTY_TYPE"]=="N"){
							if($tmp[1]){
								$filterVarTmp["PROPERTY_".$tmp[0]] = explode(",",$tmp[1]);
							}
						}else{
							$tmp[1] = $arResult["PROP_DATA"][$arResult["PROP_DATA_LINK"][$tempCode]]["VALUES"][$tmp[1]];
							if($tmp[1]){
								$filterVarTmp["PROPERTY_".$tmp[0]] = array($tmp[1]);
							}
						}
					}
				}else{
					if(isset($arResult["PROP_DATA"][$arResult["PROP_DATA_LINK"][$tempCode]]["CODE"])){
						$tmp[0] = $arResult["PROP_DATA"][$arResult["PROP_DATA_LINK"][$tempCode]]["ID"];
						$tempAr = explode('-or-',$tmp[1]);
							
						if($arResult["PROP_DATA"][$arResult["PROP_DATA_LINK"][$tempCode]]["PROPERTY_TYPE"]=="N"){
							$filterVarTmp["PROPERTY_".$tmp[0]] = $tempAr;
						}else{
							
							$newVal = array();
							foreach($tempAr as $val) {
								if(isset($arResult["PROP_DATA"][$tmp[0]]["VALUES"][$val])){
									$newVal[] = $arResult["PROP_DATA"][$tmp[0]]["VALUES"][$val];
								}
							}
							if(!empty($newVal)){
								$filterVarTmp["PROPERTY_".$tmp[0]] = $newVal;
							}
						}
					}
				}
			}
		}
	}
	
	$arSortFilter = array();
	foreach($arParams['PROPERTY_CODE'] as $propFilter){
		if(is_array($filterVarTmp["PROPERTY_".$propFilter])) {
			$filterVar["PROPERTY_".$propFilter] = $filterVarTmp["PROPERTY_".$propFilter];
		}else{
			$stat404 = true;
		}
		$arSortFilter[] = mb_strtolower($arResult["PROP_DATA"][$propFilter]["CODE"]);
	}
	
	//сортировка параметров в урле и установка основного ключа FILTER_ID
	$canonikalUrl = MlifeAszMulticatalogFilterComponent::getCanonikalUrl($arResult["CURPAGE"],$arParams["FILTER_ID"],$arSortFilter);
	if($canonikalUrl && $canonikalUrl!=$APPLICATION->GetCurPage(false)){
		$APPLICATION->AddViewContent('canonical','<link rel="canonical" href="http://'.$_SERVER["HTTP_HOST"].$canonikalUrl.'"/>');
	}
}

if($this->StartResultCache(false, array($filterVar))){

	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	//echo '<p>filterVar<br/></p><pre>';print_r($filterVar);echo'</pre><br/><br/>';
	
	//формируем значения для фильтра
	$arResult["CUR_FILTER"] = array();
	foreach($filterVar as $key=>$val) {
		$propcode = str_replace("PROPERTY_","",$key);
		if($arResult["PROP_DATA"][$propcode]['PROPERTY_TYPE']=='S' || $arResult["PROP_DATA"][$propcode]['PROPERTY_TYPE'] == 'L'){
			$arResult["CUR_FILTER"]["PROPERTY_".$propcode.""] = $val;
		}elseif($arResult["PROP_DATA"][$propcode]['PROPERTY_TYPE']=='N'){
			$arResult["CUR_FILTER"][">=PROPERTY_".$propcode.""] = $val[0];
			$arResult["CUR_FILTER"]["<=PROPERTY_".$propcode.""] = $val[1];
		}
	}
	
	//echo '<p>CUR_FILTER<br/></p><pre>';print_r($arResult["CUR_FILTER"]);echo'</pre><br/><br/>';
	
	$arFilter = array(
			"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
			"ACTIVE"=>"Y",
			);
	if($arParams["SECTION_ID"]>0) {
		$arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
		$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	}
	elseif(strlen($arParams["SECTION_CODE"])>0) {
		//$arFilter["SECTION_CODE"] = $arParams["SECTION_CODE"];
		
		$rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),array("IBLOCK_ID"=>$arParams["IBLOCK_ID"],"CODE"=>$arParams["SECTION_CODE"]), false, array("ID"));
		if ($arSect = $rsSect->GetNext()){
			$arFilter["SECTION_ID"] = $arSect["ID"];
		}
		
		$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	}
	
	$arResult['AR_ACTIVE_URL'] = MlifeAszMulticatalogFilterComponent::getstartUrlParamArray($filterVar,false);
	//print_r($arResult['AR_ACTIVE_URL']);
	
	$arResult["PROP_VALUES"] = array();
	$arResult["PROP_CUR_VALUES"] = array();
	$arResult["PROPN_CUR_URL"] = array();
	$arResult["PROP_VALUES_MIN"] = array();
	$arResult["PROP_VALUES_MAX"] = array();
	$arResult["PROP_TEMPLATE"] = array();
	$arResult["PROP_TEMPLATE_IDS"] = array();
	$arResult["PROP_TEMPLATE_CAT"] = false;
	if($arFilter["SECTION_ID"]>0){
		$arResult["PROP_TEMPLATE_CAT"] = $arFilter["SECTION_ID"];
	}
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
					$mode = $arParams["D_PROP_".$param];
					foreach($arResult["PROP_CUR_VALUES"][$param] as &$prop) {
						if($mode=="MODE4"){
							$url = '';
							$urlParams = MlifeAszMulticatalogFilterComponent::getActiveUrlforValueMode4($arResult['AR_ACTIVE_URL'],$param,$prop["PROPERTY_".$param."_ENUM_ID"],$arResult["PROP_DATA"]);
							if(isset($urlParams[0]) && $urlParams[0]) {
								$url = ($urlParams[0]==MlifeAszMulticatalogFilterComponent::$cacheTemplateUrl) ? "" :  $urlParams[0].'/';
								$url = str_replace("//","/",$url);
								$prop['LINKSEF'] = urldecode($arResult["CURPAGE"].$url);
							}
							$prop['ACTIVE'] = (isset($urlParams[1]) && $urlParams[1]) ? "Y" : "N";
							if($prop['ACTIVE']=="Y") {
								$arResult["PROP_TEMPLATE"]["this.VALUE".$param][] = $prop['PROPERTY_'.$param.'_VALUE'];
								$arResult["PROP_TEMPLATE_IDS"][$param] = $param;
							}
						}elseif($mode=="MODE5"){
							$url = '';
							$urlParams = MlifeAszMulticatalogFilterComponent::getActiveUrlforValueMode5($arResult['AR_ACTIVE_URL'],$param,$prop["PROPERTY_".$param."_ENUM_ID"],$arResult["PROP_DATA"]);
							if(isset($urlParams[0]) && $urlParams[0]) {
								$url = ($urlParams[0]==MlifeAszMulticatalogFilterComponent::$cacheTemplateUrl) ? "" :  $urlParams[0].'/';
								$url = str_replace("//","/",$url);
								$prop['LINKSEF'] = urldecode($arResult["CURPAGE"].$url);
							}
							$prop['ACTIVE'] = (isset($urlParams[1]) && $urlParams[1]) ? "Y" : "N";
							if($prop['ACTIVE']=="Y") {
								$arResult["PROP_TEMPLATE"]["this.VALUE".$param][] = $prop['PROPERTY_'.$param.'_VALUE'];
								$arResult["PROP_TEMPLATE_IDS"][$param] = $param;
							}
						}
					}
					
				}elseif($arResult["PROP_DATA"][$param]['PROPERTY_TYPE']=="N") {
					$activeFilter = $arResult["CUR_FILTER"];
					
					$min = $max = false;
					if(isset($activeFilter[">=PROPERTY_".$param.""])) {
						$min = $activeFilter[">=PROPERTY_".$param.""];
					}
					if(isset($activeFilter[">=PROPERTY_".$param.""])) {
						$max = $activeFilter["<=PROPERTY_".$param.""];
					}
					
					unset($activeFilter[">=PROPERTY_".$param.""]);
					unset($activeFilter["<=PROPERTY_".$param.""]);
					$activeFilter[">PROPERTY_".$param.""] = 0;
					
					$tempArFields = array();
					$res = CIBlockElement::GetList(array("PROPERTY_".$param => "asc"), array_merge($activeFilter,$arFilter), false, array("nPageSize"=>1), array("PROPERTY_".$param));
					if($ob = $res->GetNextElement(false,false))
					{
						$arFields = $ob->GetFields();
						$arResult["PROP_VALUES"][$param][] = $arFields;
					}
					$res = CIBlockElement::GetList(array("PROPERTY_".$param => "desc"), array_merge($activeFilter,$arFilter), false, array("nPageSize"=>1), array("PROPERTY_".$param));
					if($ob = $res->GetNextElement(false,false))
					{
						$arFields = $ob->GetFields();
						$arResult["PROP_VALUES"][$param][] = $arFields;
					}
					
					if($min){
						$arResult["PROP_VALUES_MIN"][$param] = $min;
					}
					if($max) {
						$arResult["PROP_VALUES_MAX"][$param] = $max;
					}
					if($min && $max){
						$arResult["PROP_TEMPLATE"]["this.VALUE".$param][] = $min;
						$arResult["PROP_TEMPLATE"]["this.VALUE".$param][] = $max;
						$arResult["PROP_TEMPLATE_IDS"][$param] = $param;
					}
					
					//пока поддержка только чисел с диапозоном
					$arResult["PROPN_CUR_URL"][$param] = MlifeAszMulticatalogFilterComponent::makeUrl($arResult['AR_ACTIVE_URL'], $arResult["PROP_DATA"]);
				}
			}
			
		}
	}
	
	$main_query = new \Bitrix\Main\Entity\Query(\Mlife\Asz\MetafiltercatTable::getEntity());
	$main_query->setSelect(array(
		"TEMPLATE_TITLE"=>"MAIN.TEMPLATE_TITLE", 
		"TEMPLATE_KEY"=>"MAIN.TEMPLATE_KEY", 
		"TEMPLATE_DESC"=>"MAIN.TEMPLATE_DESC",
		"TEMPLATE_NAME"=>"MAIN.TEMPLATE_NAME",
		"TEMPLATE_TEXT"=>"MAIN.TEMPLATE_TEXT",
		));
	$main_query->setOrder($global_query['order']);
	$main_query->registerRuntimeField("PROPSID", array(
		'data_type' => '\Mlife\Asz\MetafilterpropTable',
		'reference'=> array('=this.ID' => 'ref.ID'),
	));
	$main_query->registerRuntimeField("MAIN", array(
		'data_type' => '\Mlife\Asz\MetafilterTable',
		'reference'=> array('=this.ID' => 'ref.ID'),
	));
	$filterTemplate = array();
	if($arResult["PROP_TEMPLATE_CAT"]){
		$filterTemplate["CATID"] = $arResult["PROP_TEMPLATE_CAT"];
	}
	if(!empty($arResult["PROP_TEMPLATE_IDS"])) {
		$filterTemplate["PROPSID.PROPID"] = array_merge(array("LOGIC" => "AND"),$arResult["PROP_TEMPLATE_IDS"]);
	}
	$main_query->setFilter($filterTemplate);
	$main_query->setOrder(array("MAIN.SORT"=>"DESC"));
	$resT = $main_query->setLimit(1)->exec();
	$arResult["SEO_TEMPLATE"] = array();
	if($templateAr = $resT->Fetch()){
		foreach($templateAr as $key=>$tmp){
			$arResult["SEO_TEMPLATE"][$key] = \Mlife\Asz\Functions::formatTemplateFilter($tmp,$arResult["PROP_TEMPLATE"]);
		}
	}
	
	
	$arResult['URL_RESET'] = $arResult["CURPAGE"];
	if(!isset($arResult['URL_SET'])) $arResult['URL_SET'] = $APPLICATION->GetCurPageParam("set_filter=1",array('PAGEN_1','ajaxcatalog','ajaxfilter','set_filter'));
	
	$this->SetResultCacheKeys(array(
		"CUR_FILTER",
		"CURPAGE",
		"SEO_TEMPLATE"
	));
	//echo'<pre>';print_r($arResult["SEO_TEMPLATE"]);echo'</pre>';
	$this->IncludeComponentTemplate();

}

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])){
	$arFilterMain = $arResult["CUR_FILTER"];
}else{
	$GLOBALS[$arParams["FILTER_NAME"]] = $arResult["CUR_FILTER"];
}


if($_REQUEST['ajaxfilter']==1) {
	die();
}

global $SEO_TEMPLATE;
$SEO_TEMPLATE = $arResult['SEO_TEMPLATE'];
?>