<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode(true); ?>
		<!-- second view -->
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
		<div class="flat_wrap daysarticle view2" id="<?=$element['ID']?>">
			<div class="top_info">
				<div class="time_icon_wrap">
					<!-- <div class="tinytriangle"></div> -->
					<div class="time_icon"></div>
					<!-- <div class="triangle"></div> -->
				</div>
				<div class="title">
					<?=GetMessage("QUICK_BLOCK_TITLE_FOR_SMALL")?>
				</div>
			</div>
			<div class="body_info">
				<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h4>
				<?if($arItem["PREVIEW_PICTURE"]["SRC"]!=""):?>
					<a class="img_wrap" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" border="" alt="" />
					</a>
				<?elseif($arItem["DETAIL_PICTURE"]["SRC"]!=""):?>
					<a class="img_wrap" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" border="" alt="" />
					</a>
				<?endif;?>
				<div class="price_wrap">
					<div class="discount_price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_VALUE"]?><span class="rub" style="display: none;">?</span></div>
					<div class="price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_VALUE"]?></div>
					<div class="discount"><span><?=GetMessage("DISCOUNT_VALUE")?></span> <?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_DIFF"]?><div class="triangle"></div></div>
				</div>
			</div>
			<div class="digits" data-dateto="<? echo strtotime($timer['DATE_TO']); ?>">
					<div class="time">
						<span><?=GetMessage("QUICK_TIME")?></span>
						<? if ($timer["TIMER"]["DAYS"]==0) { ?>
							<div class="digit" id="hours"><div class="digit_outter"><span class="number js-hours" data-time=""></span><hr></div><span><?=GetMessage("QUICK_TIME_HOUR")?></span></div>
							<div class="digit" id="minutes"><div class="digit_outter"><span class="number js-minutes" data-time=""></span><hr></div><span><?=GetMessage("QUICK_TIME_MIN")?></span></div>
							<div class="digit" id="seconds"><div class="digit_outter"><span class="number js-seconds" data-time=""></span><hr></div><span><?=GetMessage("QUICK_TIME_SEC")?></span></div>
						<? } else { ?>
							<div class="digit" id="days"><div class="digit_outter"><span class="number js-days" data-time=""></span><hr></div><span><?=GetMessage("QUICK_TIME_DAY")?></span></div>
							<div class="digit" id="hours"><div class="digit_outter"><span class="number js-hours" data-time=""></span><hr></div><span><?=GetMessage("QUICK_TIME_HOUR")?></span></div>
							<div class="digit" id="minutes"><div class="digit_outter"><span class="number js-minutes" data-time=""></span><hr></div><span><?=GetMessage("QUICK_TIME_MIN")?></span></div>
						<? } ?>
					</div>
					<div class="percentage">
						<span><?=GetMessage("QUICK_TO_THE_END")?></span>
						<div class="digit progress"><div class="digit_outter"><span class="number"><?=$timer["QUANTITY"]?></span><hr></div></div><span class="quantity"><?=GetMessage("QUICK_QUANT")?></span>
					</div>
				</div>
		</div>
	<? endforeach; ?>
<? endif; ?>
		<!-- /second view -->