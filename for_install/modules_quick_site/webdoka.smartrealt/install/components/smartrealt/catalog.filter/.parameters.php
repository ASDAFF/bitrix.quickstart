<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true )die();

if(!CModule::IncludeModule("iblock"))
    return;

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array());
while($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arComponentParameters = array(
	'PARAMETERS'	=> array(
		"CACHE_TIME"	=> array(
			"DEFAULT"	=> 3600
		)
	)
); 
?>
