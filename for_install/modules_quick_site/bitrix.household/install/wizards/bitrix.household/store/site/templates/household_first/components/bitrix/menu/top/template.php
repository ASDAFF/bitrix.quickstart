<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
	return;

$lastSelectedItem = null;
$lastSelectedIndex = -1;

foreach($arResult as $itemIdex => $arItem)
{
	if (!$arItem["SELECTED"])
		continue;

	if ($lastSelectedItem == null || strlen($arItem["LINK"]) >= strlen($lastSelectedItem["LINK"]))
	{
		$lastSelectedItem = $arItem;
		$lastSelectedIndex = $itemIdex;
	}
}

?>

<div class="menu">
<p>
	<?$i=0;?>
	<?foreach($arResult as $itemIdex => $arItem):
		$i++;?>
		<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a><?if (count($arResult)!=$i) echo " | ";?>
	<?endforeach;?>
</p>
</div>


                    