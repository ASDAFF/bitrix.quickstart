<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (count($arResult) == 0){
	if ($_SERVER['PHP_SELF'] != '/index.php')
		$strReturn .= ' - Салон красоты';
	return $strReturn;
}

$strReturn .= ' - ' . $arResult[count($arResult) - 1]["TITLE"];

for($index = count($arResult) - 2; $index >= 0; $index--)
	$strReturn .= ' - ' . htmlspecialcharsex($arResult[$index]["TITLE"]);

	
return $strReturn;
?>
