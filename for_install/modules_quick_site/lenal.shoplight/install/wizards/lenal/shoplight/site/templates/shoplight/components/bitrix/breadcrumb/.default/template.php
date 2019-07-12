<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string

__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/'.basename(__FILE__));

$curPage = $GLOBALS['APPLICATION']->GetCurPage($get_index_page=false);

if ($curPage != SITE_DIR)
{
	if (empty($arResult) || (!empty($arResult[count($arResult)-1]['LINK']) && $curPage != urldecode($arResult[count($arResult)-1]['LINK'])))
		$arResult[] = array('TITLE' =>  htmlspecialcharsback($GLOBALS['APPLICATION']->GetTitle(false, true)), 'LINK' => $curPage);
}

if(empty($arResult))
	return "";
	
$strReturn = '
<ul class="b-breadcrumbs">
<li class="b-breadcrumbs__item"><a class="b-breadcrumbs__item-link" href="'.SITE_DIR.'">'.GetMessage('BREADCRUMB_MAIN').'</a></li><li class="b-breadcrumbs__separator">/</li>';

$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	
	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
		$strReturn .= '<li class="b-breadcrumbs__item"><a class="b-breadcrumbs__item-link" href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a></li><li class="b-breadcrumbs__separator">/</li>';
	else
		$strReturn .= '<li class="b-breadcrumbs__item"><span>'.$title.'</span></li>';
}

$strReturn .= '</ul>';

return $strReturn;
?>