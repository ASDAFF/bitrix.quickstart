<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("iblock");

//echo "<pre>"; print_r($arParams); echo "</pre>";
//echo "<pre>"; print_r($arResult); echo "</pre>";

$code=array();
foreach ($arParams["IBLOCK_IDS"] as $key => $el)
{
	$res = CIBlock::GetByID($el);
	if($ar_res = $res->GetNext())
	    $code[]=str_replace("_".SITE_ID,"", $ar_res['CODE']);
}


foreach ($arResult as $key => $el)
{
	foreach ($code as $k => $e)
		if (strpos($el["LINK"], $e)!==false) unset($arResult[$key]);
}

?>