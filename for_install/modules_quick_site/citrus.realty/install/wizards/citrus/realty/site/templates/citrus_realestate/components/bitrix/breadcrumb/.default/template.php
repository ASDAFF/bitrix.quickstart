<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";
	
$strReturn = '<div class="bx_breadcrumbs nav-new"><ul>';

$num_items = count($arResult);
foreach ($arResult as $idx=>$item)
{
	if ($idx)
		$strReturn .= '<span class="nav-slash">\</span>';
	$title = htmlspecialcharsex($item["TITLE"]);
	
	if ($item["LINK"] <> "" && $index != $itemSize-1)
		$strReturn .= '<a href="'.$item["LINK"].'" title="'.$title.'">'.$title.'</a>';
	else
		$strReturn .= '<a>'.$title.'</a>';
}

$strReturn .= '</div>';

return $strReturn;
?>