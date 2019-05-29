<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if(!isset($arParams['CACHE_TIME'])){
	$arParams['CACHE_TIME'] = 3600;
}
function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
    $arrData = array();
    
    // if input is object, convert into array
    if (is_object($arrObjData)) {
        $arrObjData = get_object_vars($arrObjData);
    }
    
    if (is_array($arrObjData)) {
        foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
                $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
                continue;
            }
            $arrData[$index] = $value;
        }
    }
    return $arrData;
}

$minusTime = 86401; // 86400 = 24 hours
$time = time();
$today = $time;
$yesterdayDate = $time-$minusTime;

$XML_File_Name1	= 'http://www.cbr.ru/scripts/XML_daily.asp?date_req='.date("d/m/Y", $today);
$XML_File_Name2	= 'http://www.cbr.ru/scripts/XML_daily.asp?date_req='.date("d/m/Y", $yesterdayDate);

if($this->StartResultCache(3600)){
	// today
	if($buf1 = file_get_contents($XML_File_Name1)){
		$xmlObj1 = simplexml_load_string($buf1);
		if($xmlObj1->count == null){
			$this->AbortResultCache();
		}else{
			$someArr = objectsIntoArray($xmlObj1);
		}
	}else{
		$arResult["ITEMS"]["TODAY"] = false;
	}
	
	foreach($someArr["Valute"] as $key => $val)
	{
		if($val["CharCode"]=="USD")
		{
			$arResult["ITEMS"]["TODAY"]["USD"]["ID"] = $val["NumCode"];
			$arResult["ITEMS"]["TODAY"]["USD"]["DATE"] = date("d.m", $today);
			$arResult["ITEMS"]["TODAY"]["USD"]["CODE"] = $val["CharCode"];
			$arResult["ITEMS"]["TODAY"]["USD"]["NAME"] = $val["Name"];
			$arResult["ITEMS"]["TODAY"]["USD"]["VALUE"] = $val["Value"];
			$arResult["ITEMS"]["TODAY"]["USD"]["VALUE_ROUND"] = round(str_replace(',', '.', $val["Value"]), 2);
		} else if($val["CharCode"]=="EUR")
		{
			$arResult["ITEMS"]["TODAY"]["EUR"]["ID"] = $val["NumCode"];
			$arResult["ITEMS"]["TODAY"]["USD"]["DATE"] = date("d.m", $today);
			$arResult["ITEMS"]["TODAY"]["EUR"]["CODE"] = $val["CharCode"];
			$arResult["ITEMS"]["TODAY"]["EUR"]["NAME"] = $val["Name"];
			$arResult["ITEMS"]["TODAY"]["EUR"]["VALUE"] = $val["Value"];
			$arResult["ITEMS"]["TODAY"]["EUR"]["VALUE_ROUND"] = round(str_replace(',', '.', $val["Value"]), 2);
		}
	}
	// yesterday
	if($buf2 = file_get_contents($XML_File_Name2)){
		$xmlObj2 = simplexml_load_string($buf2);
		if($xmlObj2->count == null){
			$this->AbortResultCache();
		}else{
			$someArr2 = objectsIntoArray($xmlObj2);
		}
	}else{
		$arResult["ITEMS"]["TODAY"] = false;
	}
	foreach($someArr2["Valute"] as $key => $val)
	{
		if($val["CharCode"]=="USD")
		{
			$arResult["ITEMS"]["YESTERDAY"]["USD"]["ID"] = $val["NumCode"];
			$arResult["ITEMS"]["YESTERDAY"]["USD"]["DATE"] = date("d.m", $yesterdayDate);
			$arResult["ITEMS"]["YESTERDAY"]["USD"]["CODE"] = $val["CharCode"];
			$arResult["ITEMS"]["YESTERDAY"]["USD"]["NAME"] = $val["Name"];
			$arResult["ITEMS"]["YESTERDAY"]["USD"]["VALUE"] = $val["Value"];
			$arResult["ITEMS"]["YESTERDAY"]["USD"]["VALUE_ROUND"] = round(str_replace(',', '.', $val["Value"]), 2);
		} else if($val["CharCode"]=="EUR")
		{
			$arResult["ITEMS"]["YESTERDAY"]["EUR"]["ID"] = $val["NumCode"];
			$arResult["ITEMS"]["YESTERDAY"]["EUR"]["DATE"] = date("d.m", $yesterdayDate);
			$arResult["ITEMS"]["YESTERDAY"]["EUR"]["CODE"] = $val["CharCode"];
			$arResult["ITEMS"]["YESTERDAY"]["EUR"]["NAME"] = $val["Name"];
			$arResult["ITEMS"]["YESTERDAY"]["EUR"]["VALUE"] = $val["Value"];
			$arResult["ITEMS"]["YESTERDAY"]["EUR"]["VALUE_ROUND"] = round(str_replace(',', '.', $val["Value"]), 2);
		}
	}
	
	//echo"<pre>";print_r($someArr);echo"</pre>";

	$this->IncludeComponentTemplate();
}
?>

