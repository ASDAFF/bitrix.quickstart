<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

if(CModule::IncludeModule("iblock"))
{

	$IBLOCK_ID = COption::GetOptionString("tireos.landing", "IB_TOPMENU", "0");
	
	$arOrder = Array("SORT"=>"ASC");
	$arSelect = Array("ID", "NAME", "IBLOCK_ID", "DETAIL_PAGE_URL");
	$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y");
	$res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
	
	while($ob = $res->GetNextElement())
	{
		$arFields = $ob->GetFields();
		$arProps = $ob->GetProperties();
	
		$aMenuLinksExt[] = Array(
					$arFields['NAME'],
					$arProps["LINK"]["VALUE"],
					Array(),
					Array(
						"TYPE" => $arProps["TYPE"]["VALUE_XML_ID"],
						"SPECIAL_LINK" => $arProps["LINK"]["VALUE"]
					),
					""
					);
		
	}
    
}
		
 $aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);

?>