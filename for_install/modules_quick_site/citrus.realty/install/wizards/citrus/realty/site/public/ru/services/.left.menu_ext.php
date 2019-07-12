<?
$aMenuLinks = array_merge(
	$aMenuLinks,
	$GLOBALS['APPLICATION']->IncludeComponent("citrus:realty.iblock.menu", "", array("IBLOCK_ID" => "services"))
);
