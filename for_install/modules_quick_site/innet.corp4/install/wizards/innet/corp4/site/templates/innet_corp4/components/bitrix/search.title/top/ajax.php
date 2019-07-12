<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(!empty($arResult["CATEGORIES"])){?>
	<table class="title-search-result">
		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory) {?>
			<?foreach($arCategory["ITEMS"] as $i => $arItem) {?>
                <tr>
                    <?if ($category_id === "all") {?>
                        <td class="title-search-all"><a href="<?=$arItem["URL"]?>"><?=$arItem["NAME"]?></td>
                    <?} else {?>
                        <td class="title-search-more"><a href="<?=$arItem["URL"]?>"><?=$arItem["NAME"]?></td>
                    <?}?>
                </tr>
			<?}?>
		<?}?>
	</table>
	
	<div class="title-search-fader"></div>
<?}?>