<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
	return;
?>
<?foreach($arResult["ALL_ITEMS_ID"] as $itemIdLevel_1=>$arItemsLevel_2):?> <!-- first level-->
<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_1]["LINK"]?>" class="menu-separator"><?=$arResult["ALL_ITEMS"][$itemIdLevel_1]["TEXT"]?></a>
	<?if (is_array($arItemsLevel_2) && !empty($arItemsLevel_2)):?>
		<?foreach($arItemsLevel_2 as $itemIdLevel_2=>$arItemsLevel_3):?> <!-- second level-->
		<div class="menu-section">
			<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>" class="menu-separator"><?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?></a>
			<?if (is_array($arItemsLevel_3) && !empty($arItemsLevel_3)):?>
				<?foreach($arItemsLevel_3 as $itemIdLevel_3):?> <!-- third level-->
					<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["LINK"]?>" class="menu-item"><?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["TEXT"]?></a>
				<?endforeach?>
			<?endif?>
		</div>
		<?endforeach?>
	<?endif?>
<?endforeach?>

<!--<a href="" class="menu-separator-user">
	<span class="menu-item-avatar" style=""></span>
</a>
-->

