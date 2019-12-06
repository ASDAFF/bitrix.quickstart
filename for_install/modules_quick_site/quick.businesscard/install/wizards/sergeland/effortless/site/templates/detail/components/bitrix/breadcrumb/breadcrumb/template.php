<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";
	
$strReturn = '<ul class="breadcrumb">';

$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	
	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
		if($arResult[$index]["LINK"] == SITE_DIR)
			$strReturn .= '<li><i class="fa fa-home pr-10"></i><a href="'.$arResult[$index]["LINK"].'">'.$title.'</a></li>';
		else
			$strReturn .= '<li><a href="'.$arResult[$index]["LINK"].'">'.$title.'</a></li>';
	else
		$strReturn .= '<li class="active">'.$title.'</li>';
}

$strReturn .= '</ul>';

return $strReturn;
?>