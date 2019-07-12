<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
    return "";

$strReturn = '<ul class="breadcrumb">';
$arLinks = array();
for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
    $active = ($index == (count($arResult)-1)) ? 'active' : '';
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    if($arResult[$index]["LINK"] == "" || $active=='active')
        $arLinks[] = '<li class="'.$active.'">'.$title.'</li>';
    else
        $arLinks[] = '<li class="'.$active.'"><a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a></li>';
}
$strReturn .= implode('<li><span class="divider">&gt;</span></li>', $arLinks);
$strReturn .= '</ul>';
return $strReturn;
?>
