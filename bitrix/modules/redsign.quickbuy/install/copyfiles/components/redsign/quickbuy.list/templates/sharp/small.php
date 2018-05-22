<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode(true); ?>
<?if(count($arResult["ITEMS"])>0):?>
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
	<? if (isset($arItem['OFFERS']) && !empty($arItem['OFFERS'])) { 
		$element = $arItem['OFFERS'][0];
	 } else {
		$element = $arItem;
	 } 
	 if (isset($arItem['OFFERS'][0]['QUICKBUY']) && !empty($arItem['OFFERS'][0]['QUICKBUY'])) { 
		$timer = $arItem['OFFERS'][0]['QUICKBUY'];
	 } else {
		$timer = $arItem['QUICKBUY'];
	 } ?>
		<!-- third view -->
		<div class="daysarticle view3 sharp_wrap" id="<? echo $key.$arItem['ID']?>">
			<div class="titles">
				<h4><?=GetMessage("QUICK_BLOCK_TITLE")?></h4>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
			</div>
			<div class="img_wrap">
				<?if($arItem["PREVIEW_PICTURE"]["SRC"]!=""):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" border="" alt="" />
					</a>
				<?elseif($arItem["DETAIL_PICTURE"]["SRC"]!=""):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" border="" alt="" />
					</a>
				<?endif;?>
			</div>
			<div class="time_wrap">
				<div class="prices_wrap">
					<div class="price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_VALUE"]?></div>
					<div class="discount_price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_VALUE"]?></div>
					<div class="discount"><span><?=GetMessage("DISCOUNT_VALUE")?></span> <?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_DIFF"]?></div>
				</div>
				<span><?=GetMessage("QUICK_TIME")?></span>
				<? if ($timer["TIMER"]["DAYS"]==0) { ?>
					<div class="digits" data-dateto="<? echo strtotime($timer['DATE_TO']); ?>">
						<div class="digit" id="hours"><span class="number js-hours" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit" id="minutes"><span class="number js-minutes" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit" id="seconds"><span class="number js-seconds" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit progress"><span class="number"><?=$timer["QUANTITY"]?></span><div class="digit_outter"></div></div>
					</div>
					<div class="digit_titles">
						<span class="digit_title"><?=GetMessage("QUICK_TIME_HOUR")?></span>
						<span class="digit_title"><?=GetMessage("QUICK_TIME_MIN")?></span>
						<span class="digit_title"><?=GetMessage("QUICK_TIME_SEC")?></span>
						<span class="digit_title percent"><?=GetMessage("QUICK_QUANT")?></span>
					</div>
				<? } else { ?>
					<div class="digits" data-dateto="<? echo strtotime($timer['DATE_TO']); ?>">
						<div class="digit" id="days"><span class="number js-days" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit" id="hours"><span class="number js-hours" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit" id="minutes"><span class="number js-minutes" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit progress"><span class="number"><?=$timer["QUANTITY"]?></span><div class="digit_outter"></div></div>
					</div>
					<div class="digit_titles">
						<span class="digit_title"><?=GetMessage("QUICK_TIME_DAY")?></span>
						<span class="digit_title"><?=GetMessage("QUICK_TIME_HOUR")?></span>
						<span class="digit_title"><?=GetMessage("QUICK_TIME_MIN")?></span>
						<span class="digit_title percent"><?=GetMessage("QUICK_QUANT")?></span>
					</div>
				<? } ?>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				timer($('.daysarticle#<? echo $key.$arItem['ID']?>'));
				console.log($('.daysarticle#<? echo $key.$arItem['ID']?>'));
			});
		</script>
		<!-- /third view -->
	<? endforeach; ?>
<? endif; ?>