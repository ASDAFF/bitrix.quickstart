<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

//delayed function must return a string
if (empty($arResult)) {
	return "";
}
	
$strReturn = '<ul id="breadcrumbs" class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';

$num_items = count($arResult);

for ($index = 0, $itemSize = $num_items; $index < $itemSize; $index++) {
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	
	if ($arResult[$index]['LINK'] <> '' && $arResult[$index]['LINK'] != $APPLICATION->GetCurPage()) {
		$strReturn .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">'.
            '<a itemprop="item" href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.
                '<span itemprop="name">'.$title.'</span></a>'.
            '<meta itemprop="position" content="'.($index + 1).'">'.
        '</li>';
	} else {
		$strReturn .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">'.
            '<span itemprop="name">'.$title.'</span>'.
            '<meta itemprop="position" content="'.($index + 1).'">'.
		'</li>';
	}
}

$strReturn .= '</ul>';

return $strReturn;
