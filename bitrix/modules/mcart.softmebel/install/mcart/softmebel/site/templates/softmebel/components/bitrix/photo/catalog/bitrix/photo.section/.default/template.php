<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$exclude_properties=array('PRICE');
$exclude_fields=array('PREVIEW_TEXT');
?>
<div class="photo-section" width="100%">
<? if(!empty($arResult['DESCRIPTION'])) echo '<p>'.$arResult['DESCRIPTION'].'</p>'; ?>
<? if($arParams["DISPLAY_TOP_PAGER"]) echo $arResult["NAV_STRING"].'<br />'; ?>
<table cellpadding="0" cellspacing="0" border="0" class="data-table" width="100%">
	<?foreach($arResult["ROWS"] as $arItems):?>
		<tr class="head-row" valign="top">
		<?foreach($arItems as $arItem){
			if(is_array($arItem)){?>
				<td align="center" width="<?=$arResult["TD_WIDTH"]?>">
					&nbsp;
					<?if($arResult["USER_HAVE_ACCESS"]):?>
						<?if(is_array($arItem["PICTURE"])):?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arItem["PICTURE"]["SRC"]?>"
                                 width="<?=$arItem["PICTURE"]["WIDTH"]/2 ?>" height="<?=$arItem["PICTURE"]["HEIGHT"]/2 ?>" 
                                 alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a><br />
						<?endif?>
					<?else:?>
						<?if(is_array($arItem["PICTURE"])):?>
							<img border="0" src="<?=$arItem["PICTURE"]["SRC"]?>"
                                 width="<?=$arItem["PICTURE"]["WIDTH"]/2 ?>" height="<?=$arItem["PICTURE"]["HEIGHT"]/2 ?>" 
                                 alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /><br />
						<?endif?>
					<?endif?>
				</td>
			<?}else{?>
				<td width="<?=$arResult["TD_WIDTH"]?>" rowspan="<?=$arResult["nRowsPerItem"]?>">
					&nbsp;
				</td>
			<?}
		}?>
		</tr>
		<tr class="data-row">
		<?foreach($arItems as $arItem){?>			
			<?if(is_array($arItem)){			
				if(!empty($arItem['DISPLAY_PROPERTIES']['PRICE']['DISPLAY_VALUE'])){
					$arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE'] = preg_replace('/\s/', '', $arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE']);
					$arItem['DISPLAY_PROPERTIES']['PRICE']['DISPLAY_VALUE'] =
						number_format($arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE'], 2, ',', ' ').$arParams["CURRENCY_CODE"];
				}
				?>
				<th align="center" valign="top" width="<?=$arResult["TD_WIDTH"]?>" class="data-cell">
					&nbsp;					
					<div class="descr_goods">
						<div class="name">							
							<?if($arResult["USER_HAVE_ACCESS"]):?>
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?><?
									if($arParams["USE_RATING"] && $arItem["PROPERTIES"]["rating"]["VALUE"]) 
									echo "(".$arItem["PROPERTIES"]["rating"]["VALUE"].")"?></a>
							<?else:?>
								<?=$arItem["NAME"]?><?if($arParams["USE_RATING"] && $arItem["PROPERTIES"]["rating"]["VALUE"]) 
									echo "(".$arItem["PROPERTIES"]["rating"]["VALUE"].")"?>
							<?endif?>
						</div>
						<div class="descr">
							<p><?=$arItem["PREVIEW_TEXT"]?></p>
						</div>
						<div class="price">
							<table width="200" cellspacing="0" cellpadding="0" border="0">
								<tbody>
									<tr>
										<td width="135" height="45" bgcolor="#00608A">
											<p align="center"><b><font face="Verdana" size="1" color="white"></font>
											<font face="Verdana" size="4" color="white">
												<?=$arItem['DISPLAY_PROPERTIES']['PRICE']['DISPLAY_VALUE']?></font></b></p>
										</td>
										<td width="30" height="45"><font color="white"><img width="30" height="45" border="0" 
										src="<?=SITE_TEMPLATE_PATH?>/components/bitrix/photo/catalog/images/price_u.gif"></font></td> 
										<td width="72" valign="top" height="45">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</th>
			<?}?>
			
		<?}?>
		</tr>
		<?if($arResult["bDisplayFields"]):?>
			<tr class="data-row">
			<?foreach($arItems as $arItem){
				if(is_array($arItem)){?>
					<th align="center" valign="top" width="<?=$arResult["TD_WIDTH"]?>" class="data-cell">
						<?foreach($arParams["FIELD_CODE"] as $code){
							if(in_array($code, $exclude_fields)) continue; ?>
							<small><?=GetMessage("IBLOCK_FIELD_".$code)?>&nbsp;:&nbsp;<?=$arItem[$code]?></small><br />
						<?}?>
						<?foreach($arItem["DISPLAY_PROPERTIES"] as $arProperty){						
							if(in_array($arProperty['CODE'], $exclude_properties)) continue; ?>
							<small><?=$arProperty["NAME"]?>:&nbsp;<?
								if(is_array($arProperty["DISPLAY_VALUE"]))
									echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
								else
									echo $arProperty["DISPLAY_VALUE"];?></small><br />
						<?}?>
					</th><?
				}
			}?>
			</tr>
		<?endif;?>
		<tr><td colspan="<?=$arResult["nRowsPerItem"]?>"><div class="hsplit">&nbsp;</div></td></tr>
	<?endforeach?>
</table>
<? if($arParams["DISPLAY_BOTTOM_PAGER"]) echo '<br />'.$arResult["NAV_STRING"]; ?>
</div>
