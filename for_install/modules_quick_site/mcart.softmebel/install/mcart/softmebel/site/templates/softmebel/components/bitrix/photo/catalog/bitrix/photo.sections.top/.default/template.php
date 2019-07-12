<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="photo-sections-top" width="100%">
<?foreach($arResult["SECTIONS"] as $arSection):?>
	<h3><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?></a></h3>
	<table cellpadding="0" cellspacing="0" border="0" class="data-table" width="100%">
		<?foreach($arSection["ROWS"] as $arItems):?>
			<tr class="head-row" valign="top">
			<?foreach($arItems as $arItem):?>
				<?if(is_array($arItem)):?>
					<td align="center" width="<?=$arResult["TD_WIDTH"]?>">
						&nbsp;
						<?if($arResult["USER_HAVE_ACCESS"]):?>
							<?if(is_array($arItem["PICTURE"])):?>
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arItem["PICTURE"]["SRC"]?>"
                                                                width="<?=$arItem["PICTURE"]["WIDTH"]/2 ?>" height="<?=$arItem["PICTURE"]["HEIGHT"]/2 ?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a><br />
							<?endif?>
						<?else:?>
							<?if(is_array($arItem["PICTURE"])):?>
								<img border="0" src="<?=$arItem["PICTURE"]["SRC"]?>"
                                                                width="<?=$arItem["PICTURE"]["WIDTH"]/2 ?>" height="<?=$arItem["PICTURE"]["HEIGHT"]/2 ?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /><br />
							<?endif?>
						<?endif?>
					</td>
				<?else:?>
					<td width="<?=$arResult["TD_WIDTH"]?>" rowspan="<?=$arResult["nRowsPerItem"]?>">
						&nbsp;
					</td>
				<?endif;?>
			<?endforeach?>
			</tr>
			<tr class="data-row">
			<?foreach($arItems as $arItem):?>
				<?if(is_array($arItem)):?>
					<th align="center"valign="top" width="<?=$arResult["TD_WIDTH"]?>" class="data-cell">
						&nbsp;
						<?if($arResult["USER_HAVE_ACCESS"]):?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?><?if($arParams["USE_RATING"] && $arItem["PROPERTIES"]["rating"]["VALUE"]) echo "(".$arItem["PROPERTIES"]["rating"]["VALUE"].")"?></a><br />
						<?else:?>
							<?=$arItem["NAME"]?><?if($arParams["USE_RATING"] && $arItem["PROPERTIES"]["rating"]["VALUE"]) echo "(".$arItem["PROPERTIES"]["rating"]["VALUE"].")"?><br />
						<?endif?>
					</th>
				<?endif;?>
			<?endforeach?>
			</tr>
			<?if($arResult["bDisplayFields"]):?>
			<tr class="data-row">
			<?foreach($arItems as $arItem):?>
				<?if(is_array($arItem)):?>
					<th align="center" valign="top" width="<?=$arResult["TD_WIDTH"]?>" class="data-cell">
						<?foreach($arParams["FIELD_CODE"] as $code):?>
							<small><?=GetMessage("IBLOCK_FIELD_".$code)?>&nbsp;:&nbsp;<?=$arItem[$code]?></small><br />
						<?endforeach?>
						<?foreach($arItem["DISPLAY_PROPERTIES"] as $arProperty):?>
							<small><?=$arProperty["NAME"]?>:&nbsp;<?
								if(is_array($arProperty["DISPLAY_VALUE"]))
									echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
								else
									echo $arProperty["DISPLAY_VALUE"];?></small><br />
						<?endforeach?>
					</th>
				<?endif;?>
			<?endforeach?>
			</tr>
			<?endif;?>
		<?endforeach?>
	</table>
	<hr /><br />
<?endforeach;?>
</div>
