<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(count($arResult["ITEMS"])>0):?>
<div class="quickbuy">
	<div class="quickbuy-title myriad-pro"><?=GetMessage("BLOCK_TITLE")?></div>
	<div class="quickbuy-body">
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
		<div class="quickbuy-item<?if($key>0):?> quickbuy-item-leftborder<?endif;?>">
			<div class="quickbuy-item-name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
			<div class="quickbuy-item-image">
				<?if($arItem["PREVIEW_PICTURE"]["SRC"]!=""):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["TRUE_SIZE"][0]?>" height="<?=$arItem["PREVIEW_PICTURE"]["TRUE_SIZE"][1]?>" border="" alt="" />
					</a>
				<?elseif($arItem["DETAIL_PICTURE"]["SRC"]!=""):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arItem["DETAIL_PICTURE"]["TRUE_SIZE"][0]?>" height="<?=$arItem["DETAIL_PICTURE"]["TRUE_SIZE"][1]?>" border="" alt="" />
					</a>
				<?endif;?>
			</div>
			<div class="quickbuy-item-price">
				<span class="quickbuy-item-price-old"><?=$arItem["PRICES"][$arParams["PRICE_CODE"]]["PRINT_VALUE_NOVAT"]?></span> &nbsp; 
				<span class="quickbuy-item-price-new"><?=$arItem["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_VALUE"]?></span>
			</div>
			<div class="quickbuy-item-discount"><?=GetMessage("DISCOUNT_VALUE")?> <?=$arItem["QUICKBUY"]["DISCOUNT_FORMATED"]?></div>
			<div class="quickbuy-item-timer">
				<?if($arItem["QUICKBUY"]["TIMER"]["DAYS"]>0):?>
					<table class="quickbuy-item-timer-table" border="0" cellpadding="0" cellspacing="0">
						<tr class="quickbuy-t-title">
							<td style="padding-bottom:4px;" colspan="6"><span class=""><?=GetMessage("TIME_LIMIT")?></td>
							<td style="padding-bottom:4px;"><?=GetMessage("TO_ACTION_END_MORE")?></td>
						<tr class="quickbuy-t-time">
							<td class="quickbuy-ptop quickbuy-pleft quickbuy-bt quickbuy-bl"><span class="quickbuy-js-d"><?=$arItem["QUICKBUY"]["TIMER"]["DAYS"]?></span></td>
							<td class="quickbuy-ptop quickbuy-bt"><span class="quickbuy-time-dvoetochie">:</span></td>
							<td class="quickbuy-ptop quickbuy-bt"><span class="quickbuy-js-h"><?=$arItem["QUICKBUY"]["TIMER"]["HOUR"]?></span></td>
							<td class="quickbuy-ptop quickbuy-bt"><span class="quickbuy-time-dvoetochie">:</span></td>
							<td class="quickbuy-ptop quickbuy-pright quickbuy-bt quickbuy-br"><span class="quickbuy-js-m"><?=$arItem["QUICKBUY"]["TIMER"]["MINUTE"]?></span></td>
							<?if($arParams['QUANTITY_TRACE']!="N"):?>
								<td>&nbsp;</td>
								<td class="quickbuy-ptop quickbuy-pright quickbuy-pleft quickbuy-bt quickbuy-bl quickbuy-br"><span class="quickbuy-quantity"><?=$arItem["QUICKBUY"]["QUANTITY"]?></span></td>
							<?endif;?>
						</tr>
						<tr class="quickbuy-t-names">
							<td class="quickbuy-pbot quickbuy-pleft quickbuy-bb quickbuy-bl"><span class="quickbuy-js-d-mess"><?=QBGEndWord($arItem["QUICKBUY"]["TIMER"]["DAYS"],GetMessage("WORD_END_D_1"),GetMessage("WORD_END_D_2"),GetMessage("WORD_END_D_3"))?></span></td>
							<td class="quickbuy-pbot quickbuy-bb">&nbsp;</td>
							<td class="quickbuy-pbot quickbuy-bb"><span class="quickbuy-js-h-mess"><?=GetMessage("TIME_LIMIT_H")?></td>
							<td class="quickbuy-pbot quickbuy-bb">&nbsp;</td>
							<td class="quickbuy-pbot quickbuy-pright quickbuy-bb quickbuy-br"><span class="quickbuy-js-m-mess"><?=GetMessage("TIME_LIMIT_M")?></span></td>
							<?if($arParams['QUANTITY_TRACE']!="N"):?>
								<td>&nbsp;</td>
								<td class="quickbuy-pbot quickbuy-pright quickbuy-pleft quickbuy-bb quickbuy-bl quickbuy-br"><?=GetMessage("TIME_LIMIT_QUANTITY")?></td>
							<?endif;?>
						</tr>
					</table>
				<?else:?>
					<table class="quickbuy-item-timer-table" border="0" cellpadding="0" cellspacing="0">
						<tr class="quickbuy-t-title">
							<td style="padding-bottom:4px;" colspan="6"><?=GetMessage("TIME_LIMIT")?></td>
							<td style="padding-bottom:4px;"><?=GetMessage("TO_ACTION_END_MORE")?></td>
						<tr class="quickbuy-t-time">
							<td class="quickbuy-ptop quickbuy-pleft quickbuy-bt quickbuy-bl"><span class="quickbuy-js-h"><?=$arItem["QUICKBUY"]["TIMER"]["HOUR"]?></span></td>
							<td class="quickbuy-ptop quickbuy-bt"><span class="quickbuy-time-dvoetochie">:</span></td>
							<td class="quickbuy-ptop quickbuy-bt"><span class="quickbuy-js-m"><?=$arItem["QUICKBUY"]["TIMER"]["MINUTE"]?></span></td>
							<td class="quickbuy-ptop quickbuy-bt"><span class="quickbuy-time-dvoetochie">:</span></td>
							<td class="quickbuy-ptop quickbuy-pright quickbuy-bt quickbuy-br"><span class="quickbuy-js-s"><?=$arItem["QUICKBUY"]["TIMER"]["SECOND"]?></span></td>
							<?if($arParams['QUANTITY_TRACE']!="N"):?>
								<td>&nbsp;</td>
								<td class="quickbuy-ptop quickbuy-pright quickbuy-pleft quickbuy-bt quickbuy-bl quickbuy-br"><span class="quickbuy-quantity"><?=$arItem["QUICKBUY"]["QUANTITY"]?></span></td>
							<?endif;?>
						</tr>
						<tr class="quickbuy-t-names">
							<td class="quickbuy-pbot quickbuy-pleft quickbuy-bb quickbuy-bl"><?=GetMessage("TIME_LIMIT_H")?></td>
							<td class="quickbuy-pbot quickbuy-bb">&nbsp;</td>
							<td class="quickbuy-pbot quickbuy-bb"><span class="quickbuy-js-m-mess"><?=GetMessage("TIME_LIMIT_M")?></span></td>
							<td class="quickbuy-pbot quickbuy-bb">&nbsp;</td>
							<td class="quickbuy-pbot quickbuy-pright quickbuy-bb quickbuy-br"><span class="quickbuy-js-s-mess"><?=GetMessage("TIME_LIMIT_S")?></span></td>
							<?if($arParams['QUANTITY_TRACE']!="N"):?>
								<td>&nbsp;</td>
								<td class="quickbuy-pbot quickbuy-pright quickbuy-pleft quickbuy-bb quickbuy-bl quickbuy-br"><?=GetMessage("TIME_LIMIT_QUANTITY")?></td>
							<?endif;?>
						</tr>
					</table>
				<?endif;?>
			</div>
			<div class="quickbuy-js-item-data" data-date_to="<?=$arItem["QUICKBUY"]["TIMER"]["DATE_TO"]?>" data-time_limit="0"></div>
		</div>
	<?endforeach;?>
	</div>
</div>
<?endif;?>