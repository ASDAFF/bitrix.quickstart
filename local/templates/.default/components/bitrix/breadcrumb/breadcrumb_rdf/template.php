<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Eremchenko Alexey                #
#   Site: http://www.altasib.ru                 #
#   E-mail: info@altasib.ru                     #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '<ul xmlns:v="http://rdf.data-vocabulary.org/#" class="breadcrumb-navigation">';

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
	if($index > 0)
		$strReturn .= '<li><span>&nbsp;&gt;&nbsp;</span></li>';

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	
	if($arResult[$index]["LINK"] <> "")
	{	
		$strReturn .= '<li> <span typeof="v:Breadcrumb"><a href="'.$arResult[$index]["LINK"].'" rel="v:url" property="v:title">'.$title.'</a></span></li>';
	}
	else
	{
		$strReturn .= '<li> <span typeof="v:Breadcrumb">'.$title.'</span></li>';
	}
}

$strReturn .= '</ul>';
return $strReturn;
?>
