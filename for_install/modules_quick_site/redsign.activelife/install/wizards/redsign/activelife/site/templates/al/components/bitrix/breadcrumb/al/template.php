<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

if(empty($arResult))
	return '';

$strReturn = '';

$strReturn .= '<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';
$itemSize = count($arResult);
for ($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]['TITLE']);

	if ($arResult[$index]['LINK'] <> '' && $arResult[$index]['LINK'] != $APPLICATION->GetCurPage()) {
		$strReturn .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">

                <a itemprop="item" href="'.$arResult[$index]['LINK'].'" title="'.$title.'">
                    <span itemprop="name">'.$title.'</span>
                </a>
                <meta itemprop="position" content="'.($index + 1).'">
            </li>';
	} else {
		$strReturn .= '<li class="active" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <span itemprop="name">'.$title.'</span>
                <meta itemprop="position" content="'.($index + 1).'">
            </li>';
	}
}

$strReturn .= '</ol>';

return $strReturn;
