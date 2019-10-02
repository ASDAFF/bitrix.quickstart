<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-compare-list">
<?if(count($arResult)>0):?>
	<form action="<?=$arParams["COMPARE_URL"]?>" method="get">
	<table class="table table-compare" cellspacing="0" cellpadding="0" border="0">
		<thead>
		<tr>
			<th colspan="2">Сравниваемые товары</td>
		</tr>
		</thead>
		<?foreach($arResult as $arElement):?>
		<tr>
			<td><input type="hidden" name="ID[]" value="<?=$arElement["ID"]?>" /><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></td>
			<td><noindex><a href="<?=$arElement["DELETE_URL"]?>" rel="nofollow"><?=GetMessage("CATALOG_DELETE")?></a></noindex></td>
		</tr>
		<?endforeach?>
	</table>
	<?if(count($arResult)>=2):?>
		<input type="submit" class="btn btn-blue" value="<?=GetMessage("CATALOG_COMPARE")?>" />
		<input type="hidden" name="action" value="COMPARE" />
		<input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
	<?endif;?>
	</form>
<?endif;?>
</div>