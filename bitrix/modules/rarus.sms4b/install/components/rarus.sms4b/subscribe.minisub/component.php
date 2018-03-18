<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

require_once "functions.php";

//initialize subscribe module
if (!CModule::IncludeModule("subscribe"))
{
	ShowMessage(GetMessage('subscribe_error'));
	return;
}

if ($arParams["SHOW_POST_SUB"] == "Y")
{
	$arResult["SHOW_POST_SUB"] = true;
}
if ($arParams["SHOW_RSS_SUB"] == "Y")
{
	$arResult["SHOW_RSS_SUB"] = true;
}
if ($arParams["SHOW_SMS_SUB"] == "Y")
{
	$arResult["SHOW_SMS_SUB"] = true;
}

//address, for editing post subscribe
if(!isset($arParams["PAGE_POST"]) || strlen($arParams["PAGE_POST"])<=0)
{
	$arParams["PAGE_POST"] = COption::GetOptionString("subscribe", "subscribe_section")."subscr_edit.php";
}
//address, for editing sms subscribe
if(!isset($arParams["PAGE_SMS"]) || strlen($arParams["PAGE_SMS"])<=0)
{
	$arParams["PAGE_SMS"] = COption::GetOptionString("subscribe", "subscribe_section")."subscr_edit.php";
}

$arResult["SUBSCRB_EDIT_POST"] = htmlspecialchars(str_replace("#SITE_DIR#", LANG_DIR, $arParams["PAGE_POST"]));
$arResult["SUBSCRB_EDIT_SMS"] = htmlspecialchars(str_replace("#SITE_DIR#", LANG_DIR, $arParams["PAGE_SMS"]));
$arResult["FEED_NAME"] = htmlspecialchars($arParams["FEED_NAME"]);
$arResult["URL_FOR_FEEDBURNER"] = htmlspecialchars($arParams["URL_FOR_FEEDBURNER"]);  
$arResult["URL_FOR_RSS"] = $arResult["URL_FOR_FEEDBURNER"].$arResult["FEED_NAME"];

//number of subscribers
if ($subscr = CSubscription::GetList(array("ID"=>"ASC"),array("CONFIRMED"=>"Y", "ACTIVE"=>"Y")))
{
	$i = 0;
	while ($index = $subscr->Fetch())
	{
		if (!preg_match("/@phone.sms/",$index["EMAIL"]))
		{
			$i++;
		}
	}
	$arResult["POST_SUBSCRIBERS"] = $i;
	$arResult["ALL_SUBSCRIBES"] = $subscr->SelectedRowsCount();
	$arResult["SMS_SUBSCRIBERS"] = $arResult["ALL_SUBSCRIBES"] - $i;	
}
else
{
	$arResult["POST_SUBSCRIBERS"] = 0;	
}

//creating object
$obCache = new CPHPCache; 
//time of caching - 1 day
$life_time = 86400; 

$cache_id  = $arResult["FEED_NAME"];

if ($obCache->InitCache($life_time, $cache_id, "/"))
{
	$vars = $obCache->GetVars();
	$arResult["RSS_SUBSCRIBERS"] = $vars["RSS_SUBSCRIBERS_COUNT"];		
}
else
{
	//feed API/getting number of rss subscribers 
	$xml_url = "http://api.feedburner.com/awareness/1.0/GetFeedData?uri=".$arResult["FEED_NAME"];

	$reader = new XMLReader();
	$reader->open($xml_url);

	while ($reader->read()) 
	{
		if ($reader->name == "entry")
		{
			$circulation=$reader->getAttribute("circulation");
		}
		if ($reader->name == "entry") 
		{
			$hits=$reader->getAttribute("hits");
		}
	}
	$reader->close();
}

if($obCache->StartDataCache())
{
	(!empty($circulation))?$arResult["RSS_SUBSCRIBERS"]=$circulation:$arResult["RSS_SUBSCRIBERS"] = 0;
	$obCache->EndDataCache(array('RSS_SUBSCRIBERS_COUNT'=>$arResult["RSS_SUBSCRIBERS"]));
}

$this->IncludeComponentTemplate();
?>