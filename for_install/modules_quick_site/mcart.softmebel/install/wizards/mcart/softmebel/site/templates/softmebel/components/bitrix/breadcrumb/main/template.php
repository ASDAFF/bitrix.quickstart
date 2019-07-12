<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult)){ return ""; }
/* <img src="<?=SITE_TEMPLATE_PATH?>/images/bullet.gif" align-middle style="margin-bottom: 3px;"> */
$strReturn = '<div style="font-size: 9pt; padding-top: 0px; padding-bottom: 0px; color:#5982AC; ">';

$last_i=(count($arResult)-1);
for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++){
	if($index > 0){
		$strReturn .= ' <img src="'.SITE_TEMPLATE_PATH.'/images/bullet.gif" style="margin-bottom: 3px; vertical-align: middle;"> ';
	}	
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if($arResult[$index]["LINK"] <> "" && $index<$last_i)
		$strReturn .= '<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a>';
	else
		$strReturn .= $title;
}

$strReturn .= '</div>';
return $strReturn;
?>
