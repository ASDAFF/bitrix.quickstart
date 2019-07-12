<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '<div class="breadcrumb"><ul>';

for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
	if($index > 0)
		$strReturn .= '<li><span></span></li>';

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if ($index!=0)
	{
		if($arResult[$index]["LINK"] <> "")
			$strReturn .= '<li class="li1"><a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a></li>';
		else
			$strReturn .= '<li class="li1">'.$title.'</li>';
	}
	else 
	{
		if($arResult[$index]["LINK"] <> "")
			$strReturn .= '<li><div class="home"><a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a></div></li>';
	}
}

$strReturn .= '</ul></div>';
return $strReturn;
?>

										
		

