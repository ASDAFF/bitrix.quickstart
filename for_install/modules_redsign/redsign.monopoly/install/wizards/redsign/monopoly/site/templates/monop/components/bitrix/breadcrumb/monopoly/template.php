<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";
	
$strReturn = '<ul id="breadcrumbs" class="list-unstyled clearfix" itemscope itemtype="http://schema.org/BreadcrumbList">';

$strReturn .= '<li class="main" itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">';
$strReturn .= '<a itemprop="item" href="'.SITE_DIR.'"><i title="" itemprop="name"></i></a>';
$strReturn .= '</li>';
$strReturn .= '<li><span>/</span></li>';

$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++) {
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	
	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1) {
		$strReturn .= '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">';
		$strReturn .= '<a itemprop="item" href="'.$arResult[$index]["LINK"].'" title="'.$title.'"><span itemprop="name">'.$title.'</span></a></li>';
		$strReturn .= '<li> / </li>';
	} else {
		$strReturn .= '<li itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">';
		$strReturn .= '<span itemprop="name">'.$title.'</span>';
		$strReturn .= '</li>';
	}
}

$strReturn .= '</ul>';

return $strReturn;