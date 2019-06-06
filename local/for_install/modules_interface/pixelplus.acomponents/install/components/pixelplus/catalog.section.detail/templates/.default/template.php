<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$componentpath = $component->GetPath();
__IncludeLang($_SERVER['DOCUMENT_ROOT'].$componentpath."/lang/".LANGUAGE_ID."/.parameters.php");
?>
<div class="catalog-section-detail">
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<th><?=getMessage('CT_PXSD_PICTURE')?></th>
		<th><?=getMessage('CT_PXSD_FIELDS')?></th>
		<th><?=getMessage('CT_PXSD_FIELDS_EX')?></th>
	</tr>
	<tr>
		<td>
			<? if ($arResult['DISPLAY_FIELDS']['DETAIL_PICTURE']) { ?>
				<?=$arResult['DISPLAY_FIELDS']['DETAIL_PICTURE']?>
			<?} elseif ($arResult['DISPLAY_FIELDS']['PICTURE']) { ?>
				<?=$arResult['DISPLAY_FIELDS']['PICTURE']?>
			<?}?>
		</td>
		<td>
			<? foreach ($arResult["DISPLAY_FIELDS"] as $fid=>$fvalue) { ?>
				<?if ($fid == "PICTURE" || $fid == "DESCRIPTION") continue;?>
				<? if ($fvalue) { ?>
					<?=getMessage("CP_PXSD_SECTION_FIELD_".$fid)?>:&nbsp; <?=$fvalue?><br>
				<?}?>
			<?}?>
		</td>
		<td>
			<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty){?>
				<?if($arProperty["DISPLAY_VALUE"]) { ?>
					<?=$arProperty["NAME"]?>:&nbsp; <?=$arProperty["DISPLAY_VALUE"]?><br>
				<?}?>
			<?}?>
		</td>
	</tr>
</table>
<?if (in_array("DESCRIPTION",$arParams["SECTION_F_FIELDS"])) { ?>
<br><br>
<?if($arResult["NAV_RESULT"]):?>
	<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
	<?echo $arResult["NAV_TEXT"];?>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
<?elseif(strlen($arResult["DESCRIPTION"])>0):?>
	<?echo $arResult["DESCRIPTION"];?>
<?endif?>
<?}?>
</div>
