<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!$this->InitComponentTemplate())
		return;

	$template = &$this->GetTemplate();
	$resource_path = $template->GetFolder();

//$APPLICATION->AddHeadString('<script type="text/javascript" src="'.$resource_path.'/js/jquery.min.js"></script>');
$APPLICATION->AddHeadString('<script type="text/javascript" src="'.$resource_path.'/js/banner_v0.2.js"></script>');
$APPLICATION->AddHeadString('<link rel="stylesheet" href="'.$resource_path.'/js/transbanner.css"/>');



if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 300;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
 	$arParams["IBLOCK_TYPE"] = "news";
if($arParams["IBLOCK_TYPE"]=="-")
	$arParams["IBLOCK_TYPE"] = "";
if(!is_array($arParams["IBLOCKS"]))
	$arParams["IBLOCKS"] = array($arParams["IBLOCKS"]);
foreach($arParams["IBLOCKS"] as $k=>$v)
	if(!$v)
		unset($arParams["IBLOCKS"][$k]);

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if($arParams["SORT_ORDER1"]!="ASC")
	 $arParams["SORT_ORDER1"]="DESC";
if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
if($arParams["SORT_ORDER2"]!="DESC")
	 $arParams["SORT_ORDER2"]="ASC";
if(strlen($arParams["DELAY"])<=0)
	$arParams["DELAY"] = "5000";

$arParams["PIC_COUNT"] = intval($arParams["PIC_COUNT"]);
if($arParams["PIC_COUNT"]<=0)
	$arParams["PIC_COUNT"] = 20;

$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));

if($this->StartResultCache(false, $USER->GetGroups()))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	$arSelect = array(
		"PROPERTY_URL",
		"PROPERTY_SECTION",
		"PROPERTY_ELEMENT",
		"NAME", 
		"ID", 
		"PREVIEW_PICTURE",
		"SHOW_COUNTER"
	);
	$arFilter = array (
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"=> $arParams["IBLOCKS"],
		"ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
	);
	$arOrder = array(
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	);
	if(!array_key_exists("ID", $arOrder))
		$arOrder["ID"] = "DESC";
	$arResult=array(
		"DATA"=>array(),
	);
	$rsItems = CIBlockElement::GetList($arOrder, $arFilter, false, array("nTopCount"=>$arParams["PIC_COUNT"]), $arSelect);
	$i=0;
	while($arItem = $rsItems->GetNext())
	{
		//echo '<pre>'; print_r($arItem); echo '</pre>';
		
		$arResult["DATA"][$i]["image"]=CFile::GetPath($arItem['PREVIEW_PICTURE']);
		
		if($arItem['PROPERTY_URL_VALUE'])
		{	
			$arResult["DATA"][$i]["link"]=$arItem['PROPERTY_URL_VALUE'];
		}
		elseif($arItem['PROPERTY_SECTION_VALUE'])
		{
			$link = '/catalog/';
			$nav = CIBlockSection::GetNavChain($arParams["IBLOCKS"], $arItem['PROPERTY_SECTION_VALUE']);
			while ($arNav=$nav->GetNext())
			{
				if(trim($arNav['CODE']))
					$link .= $arNav['CODE'].'/';
				else
					$link .= $arNav['ID'].'/';
			}
			
			//echo '<pre>'; print_r($link); echo '</pre>';
			
			$arResult["DATA"][$i]["link"]= $link;
		}
		elseif($arItem['PROPERTY_ELEMENT_VALUE'])
		{
			$res = CIBlockElement::GetByID($arItem['PROPERTY_ELEMENT_VALUE']);
			$el = $res->GetNext();
			
			$link = '/catalog/';
			$nav = CIBlockSection::GetNavChain($arParams["IBLOCKS"], $el['IBLOCK_SECTION_ID']);
			while ($arNav=$nav->GetNext())
			{
				if($arNav['CODE'])
					$link .= $arNav['CODE'].'/';
				else
					$link .= $arNav['ID'].'/';
			}
			
			$link .= $el['ID'].'/';
			
			$arResult["DATA"][$i]["link"]= $link;
		}
		
		$i++;
	}
	//echo "<pre>"; print_r($arResult); echo "</pre>";
	$arResult['PATH'] = $arParams['PATH'];
	$arResult['PARAMS']['DELAY'] = $arParams['DELAY'];
	
	$this->SetResultCacheKeys(array());
	$this->IncludeComponentTemplate();
}
?>
