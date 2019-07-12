<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">

<?
foreach($arResult["ITEMS"] as $arItem)
	if(array_key_exists("HIDDEN", $arItem))
		echo $arItem["INPUT"];
?>

<div class="catalog-item-filter<?if ($arResult['IS_FILTERED']):?> filter-active<?endif;?>">
	<div class="catalog-item-filter-title"><span><a href="#" id="catalog_item_toogle_filter"><?=($arResult['IS_FILTERED'] ? GetMessage("IBLOCK_FILTER_TITLE_ACTIVE") : GetMessage("IBLOCK_FILTER_TITLE"))?></a></span></div>
	<div class="catalog-item-filter-body" id="catalog_item_filter_body">
		<b class="r1"></b>
		<div class="catalog-item-filter-body-inner">
			<table cellspacing="0" class="catalog-item-filter" id="catalog_item_filter_table">
			<tbody>
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?if(!array_key_exists("HIDDEN", $arItem)):?>
						<tr>
							<td class="field-name"><?=$arItem["NAME"]?>:</td>
							<td class="field-control"><span class="filter-<?=$arItem["TYPE"]?>"><?=$arItem["INPUT"]?></span></td>
						</tr>
					<?endif?>
				<?endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<td class="field-name">&nbsp;</td>
					<td class="field-control"><input type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" /><input type="hidden" name="set_filter" value="Y" />&nbsp;<input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" /></td>
				</tr>
			</tfoot>
			</table>
		</div>
		<b class="r1"></b>
	</div>
</div>

</form>

<script type="text/javascript">
	$(function () {
		$("#catalog_item_toogle_filter").click(function() {
			$("#catalog_item_filter_body").slideToggle();
		});
	});
</script>