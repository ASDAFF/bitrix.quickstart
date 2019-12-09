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
		<!-- first view -->
		<div class="daysarticle metal_wrap view1" id="<?=$element['ID']?>">
			<div class="left_part">
				<div class="titles">
					<h4><?=GetMessage("QUICK_BLOCK_TITLE")?></h4>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="name"><?=$arItem["NAME"]?></a>
				</div>
				<div class="prices_wrap">
					<div class="price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_VALUE"]?></div>
					<div class="discount"><span><?=GetMessage("DISCOUNT_VALUE")?></span> <?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_DIFF"]?></div>
					<div class="discount_price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_VALUE"]?></div>
				</div>
				<div class="time_wrap">
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
					<div class="progress_bar">
						<div class="progress" style="width: <? echo round($timer['TIMER']['TIME_LIMIT']/(strtotime($timer['DATE_TO']) - strtotime($timer['DATE_FROM']))*100); ?>%;"></div>
					</div>
				</div>
				<div class="progress_number">
					<span class="cubes"></span>
					<?=GetMessage("QUICK_TO_THE_END")?> <br><span><? echo round($timer['TIMER']['TIME_LIMIT']/(strtotime($timer['DATE_TO']) - strtotime($timer['DATE_FROM']))*100); ?>%</span>
				</div>
			</div>
			<div class="right_part">
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
			</div>
		</div>
		<!-- /first view -->
	<? endforeach; ?>
<? endif; ?>