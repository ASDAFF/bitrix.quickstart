<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?foreach($arResult["ITEMS"] as $key => $arItem):?>
	<div id="da2" class="<?=$arItem["ID"]?>">
		<div class="da2-leftblock">
			<div class="da2-already_selled"><?=GetMessage("DA2_ALREADY_SELLED")?>: <span class="da2-persent-full"><span class="da2-persent-only"><?=$arItem["DAYSARTICLE2"]["DINAMICA_EX"]["PHP_DATA"]["persent"]?></span>%</span></div>
			<div class="da2-progress_bar">
				<div class="da2-progress_bar-line" style="width:<?=$arItem["DAYSARTICLE2"]["DINAMICA_EX"]["PHP_DATA"]["persent"]?>%;"></div>
			</div>
			<span class="da2-do_okon_prodag"><?=GetMessage("DA2_TIME")?></span>
		</div>
		<div class="da2-centerblock">
			<?if($arItem["PREVIEW_PICTURE"]["SRC"]!=""):?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="blank">
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" border="0" width="<?=$arItem["PREVIEW_PICTURE"]["TRUE_SIZE"][0]?>"<?
						?> height="<?=$arItem["PREVIEW_PICTURE"]["TRUE_SIZE"][1]?>" alt="" />
				</a>
			<?elseif($arItem["DETAIL_PICTURE"]["SRC"]!=""):?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="blank">
					<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" border="0" width="<?=$arItem["DETAIL_PICTURE"]["TRUE_SIZE"][0]?>"<?
						?> height="<?=$arItem["DETAIL_PICTURE"]["TRUE_SIZE"][1]?>" alt="" />
				</a>
			<?else:?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="blank">
					<img src="<?=$templateFolder."/img/noimage.gif"?>" border="0" alt="" />
				</a>
			<?endif;?>
		</div>
		<div class="da2-rightblock">
			<div class="da2-super_price"><?=GetMessage("DA2_SUPER_PRICE")?>: <span class="da2-super_price-price"><?=$arItem["DAYSARTICLE2"]["PRICE"]?></span></div>
			<div class="da2-price_and_discount">
				<div class="da2-price_and_discount-lp">
					<?=GetMessage("DA2_PRICE")?>:<br />
					<span class="da2-price"><?=$arItem["DAYSARTICLE2"]["OLD_PRICE"]?></span>
				</div>
				<div class="da2-price_and_discount-rp">
					<?=GetMessage("DA2_ECONOMY")?><br />
					<span class="da2-economy">- <?=$arItem["DAYSARTICLE2"]["DISCOUNT"]?></span>
				</div>
			</div>
			<div class="da2-clear"></div>
			<a class="da-buy_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
				<img src="<?=$templateFolder."/img/btn_buy.png"?>" border="0" alt="<?=GetMessage("DA2_ADD2BASKET")?>" title="<?=GetMessage("DA2_ADD2BASKET")?>" />
			</a>
		</div>
		<div class="da2-clear"></div>
		<div class="da2-bottomblock">
			<div class="da2-bot1">
				<div class="da2-progress_bar-text">
					<div class="da2-time_block"><?=GetMessage("DA2_TIME_HOUR")?><br /><span class="da2-js-h"><?=$arItem["DAYSARTICLE2"]["DINAMICA_EX"]["HOUR2"]?></span>: </div>
					<div class="da2-time_block"><?=GetMessage("DA2_TIME_MIN")?><br /><span class="da2-js-m"><?=$arItem["DAYSARTICLE2"]["DINAMICA_EX"]["MINUTE2"]?></span>: </div>
					<div class="da2-time_block"><?=GetMessage("DA2_TIME_SEC")?><br /><span class="da2-js-s"><?=$arItem["DAYSARTICLE2"]["DINAMICA_EX"]["SECOND2"]?></span></div>
					<div class="da2-clear"></div>
				</div>
			</div>
			<div class="da2-bot2">
				<div class="da2-name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="blank"><?=$arItem["NAME"]?></a></div>
				<div class="da2-text">
					<?if($arParams["TEXT_OR_PROP"]=="prop"):?>
							<?foreach($arItem["PROPERTIES"] as $arProperty):?>
								<?if(in_array($arProperty["CODE"],$arParams["PROPERTY_CODE"])):?>
									<?=$arProperty["NAME"]?>:&nbsp;<?
										if(is_array($arProperty["VALUE"]))
											echo implode(" / ", $arProperty["VALUE"]);
										else
											echo $arProperty["VALUE"];?><br />
								<?endif;?>
							<?endforeach?>
					<?else:?>
						<?if(strlen($arItem["PREVIEW_TEXT"])>3):?>
							<?if((strlen($arItem["PREVIEW_TEXT"]))>200):?>
								<?$name = substr($arItem["PREVIEW_TEXT"], 0, 195);?>
								<?$num_ch = strrpos($name, " ");?>
								<?$arItem["PREVIEW_TEXT"] = substr($arItem["PREVIEW_TEXT"], 0, $num_ch);?>
								<?=$arItem["PREVIEW_TEXT"]?>...
							<?else:?>
								<?=$arItem["PREVIEW_TEXT"]?>
							<?endif;?>
						<?endif;?>
					<?endif;?>
				</div>
			</div>
		</div>
		<div class="da2-clear"></div>
	</div>
	<script>
		var da2_data = <?=CUtil::PhpToJSObject($arItem["DAYSARTICLE2"]);?>;
		
		var da2_date_now = da2_data.DINAMICA_EX.DATE_NOW;
		var da2_date_from = da2_data.DINAMICA_EX.DATE_FROM;
		var da2_date_to = da2_data.DINAMICA_EX.DATE_TO;
		var da2_remain = da2_date_to - da2_date_now;
		
		function da2_UpdateTimer(timestamp)
		{
			var hour = Math.floor(timestamp/60/60);
			var mins = Math.floor((timestamp-hour*60*60)/60);
			var secs = Math.floor(timestamp-hour*60*60-mins*60);
			if(hour<10) hour = "0" + hour;
			if(mins<10) mins = "0" + mins;
			if(secs<10) secs = "0" + secs;
			$('.da2-js-h').text(hour);
			$('.da2-js-m').text(mins);
			$('.da2-js-s').text(secs);
		}
		
		$(document).ready(function(){
			if($('#da2').length>0)
			{
				setInterval(function(){
					da2_remain = da2_remain - 1;
					da2_UpdateTimer(da2_remain);
				}, 1000);
			}
		});
	</script>
<?endforeach;?>

<div style="display:none;">AlfaSystems daysarticle2 da2 AS09BTR</div>