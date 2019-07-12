<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(empty($arResult))
    return "";

$strReturn = '<div class="breadcrumbs">';

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
    if($index > 0)
        $strReturn .= '<span class="sep"></span>';

    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    if($arResult[$index]["LINK"] <> "")
        $strReturn .= '<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a>';
    else
        $strReturn .= '<span class="current">'.$title.'</span>';
}

$strReturn .= '</div>';
return $strReturn;
?>
