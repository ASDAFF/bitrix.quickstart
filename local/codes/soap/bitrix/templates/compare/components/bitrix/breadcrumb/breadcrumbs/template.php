<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//delayed function must return a string
if(empty($arResult))
	return "";
if ($_SERVER['SCRIPT_URL'] != '/registration/') {
$strReturn = '<div class="b-breadcrumb">';
}else{
$strReturn = '<div class="b-breadcrumb  m-breadcrumb__top">';
}

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
/*	if($arResult[$index]["LINK"] <> "")
		$strReturn .= '<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a>';
	else
		$strReturn .= '<span>'.$title.'</span>';
*/
	if($index==($itemSize-1))
		$strReturn .= '<span>'.$title.'</span>';
	else
		$strReturn .= '<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a> <span class="b-breadcrumb__sep"></span> ';
}

$strReturn .= '</div>';
return $strReturn;
?>