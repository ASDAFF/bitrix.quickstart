<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$componentpath = $component->GetPath();
__IncludeLang($_SERVER['DOCUMENT_ROOT'].$componentpath."/lang/".LANGUAGE_ID."/.parameters.php");
?>
<div class="catalog-user">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<table cellpadding="0" cellspacing="0" border="0">
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
		<tr>
		<?endif;?>

		<td valign="top" width="<?=round(100/$arParams["LINE_ELEMENT_COUNT"])?>%">
			<table cellpadding="0" cellspacing="2" border="0">
				<tr>
					<td>
					<? foreach ($arElement["DISPLAY_FIELDS"] as $fid=>$fvalue) { ?>
						<? if ($fvalue) { ?>
							<?=getMessage("CP_PXUL_USER_FIELD_".$fid)?>:&nbsp; <?=$fvalue?><br>
						<?}?>
					<?}?>					
					<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty){?>
						<?if($arProperty["DISPLAY_VALUE"]) { ?>
							<?=$arProperty["NAME"]?>:&nbsp; <?=$arProperty["DISPLAY_VALUE"]?><br>
						<?}?>
					<?}?>
					<? if ($arParams['USE_FORUM'] == "Y") { ?>
						<? foreach ($arElement["DISPLAY_FORUM"] as $fid=>$fvalue) { ?>
							<? if ($fvalue) { ?>
								<?=getMessage("CP_PXUL_FORUM_".$fid)?>:&nbsp; <?=$fvalue?><br>
							<?}?>
						<?}?>
					<?}?>
					</td>
				</tr>
			</table>
		</td>
		<?$cell++;
		if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
			</tr>
		<?endif?>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
			<?while(($cell++)%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
				<td>&nbsp;</td>
			<?endwhile;?>
			</tr>
		<?endif?>
</table>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>