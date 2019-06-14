<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form name="<?echo $arResult['FILTER_NAME']."_form"?>" action="<?echo $arResult['FORM_ACTION']?>" method="get" class="ts-form ts-filter">
	<?foreach($arResult['ITEMS'] as $arItem):
		if(array_key_exists("HIDDEN", $arItem)):
			echo $arItem['INPUT'];
		endif;
	endforeach;?>
	<div class="ts-container">
		<?if(!empty($arParams['FILTER_TITLE'])):?>
			<h3><?=$arParams['FILTER_TITLE'];?></h3>
		<?endif;?>
		<table>
			<tfoot>
			<tr>
				<td  style="text-align: <?=$arParams['BUTTON_ALIGN'];?>">
					<input type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" />&nbsp;&nbsp;&nbsp;<input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" />
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?foreach($arResult['ITEMS'] as $arItem):?>
				<?if(!array_key_exists("HIDDEN", $arItem)):?>
					<tr>
						<td><?=$arItem['INPUT']?></td>
					</tr>
				<?endif?>
			<?endforeach;?>
			</tbody>
		</table>
	</div>
</form>