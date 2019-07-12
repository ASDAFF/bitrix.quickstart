<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul data-role="listview" data-inset="true">
<?
$bMainList = true;
if(IntVal($arResult["SECTION"]) > 0 && !empty($arResult["SECTIONS"][$arResult["SECTION"]["ID"]]))
{
	$bMainList = false;
	$sections = $arResult["SECTIONS"][$arResult["SECTION"]["ID"]]["CHILDREN"];
}
else
	$sections = $arResult["SECTIONS"];

foreach($sections as $arSection):
	$bHasPicture = is_array($arSection['PICTURE_PREVIEW']);
	$bHasChildren = is_array($arSection['CHILDREN']) && count($arSection['CHILDREN']) > 0;
?>
	<li>
		<?if ($bHasPicture):?>
			<img src="<?=$arSection['PICTURE_PREVIEW']['SRC']?>"/>
		<?endif;?>
		<?if (!$bMainList):?>
			<a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?></a>
		<?else:?>
			<h3><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?></a></h3>
		<?endif;?>
		<?if ($arSection['DESCRIPTION']):?>
			<p><?=$arSection['DESCRIPTION_TYPE'] == 'text' ? $arSection['DESCRIPTION'] : $arSection['~DESCRIPTION']?></p>
		<?endif;?>
		<span class="ui-li-count"><?=$arSection["ELEMENT_CNT"]?></span>
	</li>
<?endforeach;?>
</ul>