<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(CModule::IncludeModuleEx('bitrix.fashion')==3){
	echo GetMessage("TEST_END");
	return;
}
if(!isset($arParams['SECTION_CODE']))
	$arParams['SECTION_CODE'] = rtrim(str_replace(SITE_DIR . 'catalog/', '', $APPLICATION->GetCurDir()), '/');

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["IBLOCK_COLOR_ID"] = intval($arParams["IBLOCK_COLOR_ID"]);
$arParams["IBLOCK_SIZE_ID"] = intval($arParams["IBLOCK_SIZE_ID"]);

if(!isset($arParams['PRICE']))
	$arParams['PRICE'] = 'BASE';

if(strlen($arParams["CURRENT_PARAMS_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["CURRENT_PARAMS_NAME"]))
{
	$arrCurrent = array();
}
else
{
	global $$arParams["CURRENT_PARAMS_NAME"];
	$arrCurrent = ${$arParams["CURRENT_PARAMS_NAME"]};
	if(!is_array($arrCurrent))
		$arrCurrent = array();
}

$arResult['DATA_FILTER'] = $arResult['DATA_OFFERS_FILTER'] = $arResult['DATA_SYS_FILTER'] = array();

$obCache = new CPHPCache;
$life_time = 30*60;

if($obCache->InitCache($life_time, '')){
	$vars = $obCache->GetVars();

	$arResult['DATA_FILTER'] = $vars["DATA_FILTER"];
	$arResult['DATA_OFFERS_FILTER'] = $vars["DATA_OFFERS_FILTER"];
	$arResult['DATA_SYS_FILTER'] = $vars["DATA_SYS_FILTER"];
	$arResult['PRICE'] = $vars["PRICE"];
}else{	
	$arResult = CSiteFashionStore::FilterShow($arParams);
	
	$obCache->StartDataCache();
	$obCache->EndDataCache(array(
		"DATA_FILTER" => $arResult['DATA_FILTER'],
		"DATA_OFFERS_FILTER" => $arResult['DATA_OFFERS_FILTER'],
		"DATA_SYS_FILTER" => $arResult['DATA_SYS_FILTER'],
		"PRICE" => $arResult['PRICE']
	));
}

$arDisplay = '';
foreach($arrCurrent as $cur_id => $cur_val){
	if(strpos($cur_id, "price_")!==false){
		if(strpos($cur_id, "price_from")!==false){
			$arDisplay['o'.$cur_id] = array('NAME'=>GetMessage("DVS_PRICE"), 'VALUE'=>' > '.$cur_val);
		}elseif(strpos($cur_id, "price_to")!==false){
			$arDisplay['o'.$cur_id] = array('NAME'=>GetMessage("DVS_PRICE"), 'VALUE'=>' < '.$cur_val);
		}

		continue;
	}

	foreach($arResult as $part => $props){
		$l = '';
		if($part=='DATA_FILTER') $l = 'm';
		elseif($part=='DATA_OFFERS_FILTER'||$part=='DATA_SYS_FILTER') $l = 'o';

		if(isset($props[$cur_id])){
			if(isset($props[$cur_id]['VALUES'][$cur_val])){
				if(is_array($props[$cur_id]['VALUES'][$cur_val]))
					$val_name = $props[$cur_id]['VALUES'][$cur_val]['NAME'];
				else
					$val_name = $props[$cur_id]['VALUES'][$cur_val];

				$arDisplay[$l.$cur_id] = array('NAME'=>$props[$cur_id]['NAME'], 'VALUE' => $val_name);
			}
		}
	}
}

if(!empty($arDisplay))
	CSiteFashionStore::dvsSetCurFilter($arDisplay);

if($this->StartResultCache(false, array($arrCurrent)))
{
	$arResult['arrCurrent'] = $arrCurrent;
	$this->IncludeComponentTemplate();
}
?>