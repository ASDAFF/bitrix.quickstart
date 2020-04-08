<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arResult["YEAR_FROM"] = date("Y");
$arResult["YEAR_TO"] = date("Y");
if (SITE_ID == 's1') {$lg = 'ru';} else {$lg = 'en';}
$res = CIBlockElement::GetList(Array("DATE_ACTIVE_FROM"=>"ASC"), Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y"), Array("DATE_ACTIVE_FROM"), Array("nTopCount" => 1), Array("ID","NAME", "DETAIL_PAGE_URL", "DATE_ACTIVE_FROM"));
if($arRes = $res->GetNext())
{
	$arResult["YEAR_FROM"] = ConvertDateTime($arRes["DATE_ACTIVE_FROM"], "YYYY", $lg);
}

?>