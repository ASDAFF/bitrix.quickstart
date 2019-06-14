<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form name="<?echo $arResult['FILTER_NAME']."_form"?>" action="<?echo $arResult['FORM_ACTION']?>" method="get" class="ts-form ts-filter">
	<?foreach($arResult['ITEMS'] as $arItem):
		if(array_key_exists("HIDDEN", $arItem)):
			echo $arItem['INPUT'];
		endif;
	endforeach;?>
	<table>
		<?if(!empty($arParams['FILTER_TITLE'])):?>
		<thead>
		<tr>
			<td colspan="2">
				<h3><?=$arParams['FILTER_TITLE'];?></h3>
			</td>
		</tr>
		</thead>
		<?endif;?>
		<tfoot>
		<tr>
			<td colspan="2"  style="text-align: <?=$arParams['BUTTON_ALIGN'];?>">
				<input type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" />&nbsp;&nbsp;<input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" />
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?foreach($arResult['ITEMS'] as $arItem):?>
			<?if(!array_key_exists("HIDDEN", $arItem)):?>
				<tr>
					<td><span class="ts-name" style="width: <?=$arParams['NAME_WIDTH'];?>px;"><?=$arItem['NAME']?>:</span></td>
					<td><?=$arItem['INPUT']?></td>
				</tr>
			<?endif?>
		<?endforeach;?>
		</tbody>
	</table>
</form>