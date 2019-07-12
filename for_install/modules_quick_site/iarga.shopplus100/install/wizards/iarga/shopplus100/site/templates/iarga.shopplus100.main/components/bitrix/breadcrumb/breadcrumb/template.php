<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(sizeof($arResult) < 2)
	return "";

$strReturn = '<div class="bread-crumbs">';

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
	if($index > 0)
		$strReturn .= '<i></i>';

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if($index+1 < $itemSize)
		$strReturn .= '<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a>';
	else
		$strReturn .= '<span>'.$title.'</span>';
}

$strReturn .= '</div><!--bread-crumbs-end-->';
return $strReturn;
?>