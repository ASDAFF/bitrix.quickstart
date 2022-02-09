<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
 
if(empty($arResult))
     return '<div class="b-breadcrumb">
                 <a class="b-breadcrumb__home" href="/"></a>
                 <span class="b-breadcrumb__separator"></span>
                 <span class="b-breadcrumb__text">Добро пожаловать в Интернет-магазин</span>
            </div>';

$strReturn = '<div class="b-breadcrumb"><a href="/" class="b-breadcrumb__home"></a>
    <span class="b-breadcrumb__separator"></span>';

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
	if($index > 0)
		$strReturn .= '<span class="b-breadcrumb__separator"></span>';

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if($arResult[$index]["LINK"] <> "")
                $strReturn .= "<a class=\"b-breadcrumb__link\" href=\"{$arResult[$index]["LINK"]}\">{$title}</a>";
	else
		$strReturn .= "<span class=\"b-breadcrumb__text\">{$title}</span>";
}

$strReturn .= '</div>';
return $strReturn;
 
