<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
	return;
rsort($arResult,SORT_NUMERIC);
//mprint($arResult);
?>

<ul class="toppanelMenu">
<?
foreach($arResult as $itemIdex => $arItem){
	$class = '';
	$classstr = '';
	if($arItem["ITEM_INDEX"]==0) $class .= 'start';
	if($arItem["SELECTED"]) {
		if($class!='') $class .= ' ';
		$class .= 'active';
	}
	if($class) $classstr = ' class="'.$class.'"';
	echo '<li'.$classstr.'><a href="'.$arItem["LINK"].'">'.$arItem["TEXT"].'</a></li>';
}
?>
</ul>