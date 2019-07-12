<? $this->setFrameMode(true); ?>
<!-- first view -->
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
		<div class="contrast_wrap daysarticle view1" id="<?=$element['ID']?>">
			<div class="top_info">
				<a class="left_part" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
					<span class="main_title"><?=GetMessage("QUICK_BLOCK_TITLE")?></span>
					<h4><?=$arItem["NAME"]?></h4>
					<div class="discount_price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_VALUE"]?><span class="rub" style="display: none;">?</span></div>
					<div class="price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_VALUE"]?></div>
					<div class="discount_wrap">
						<div class="discount"><span><?=GetMessage("DISCOUNT_VALUE")?></span> <?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_DIFF"]?><div class="triangle"></div></div>
					</div>
				</a>
				<div class="right_part">
					<?if($arItem["PREVIEW_PICTURE"]["SRC"]!=""):?>
						<a class="img_wrap" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
							<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["TRUE_SIZE"][0]?>" height="<?=$arItem["PREVIEW_PICTURE"]["TRUE_SIZE"][1]?>" border="" alt="" />
						</a>
					<?elseif($arItem["DETAIL_PICTURE"]["SRC"]!=""):?>
						<a class="img_wrap" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
							<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arItem["DETAIL_PICTURE"]["TRUE_SIZE"][0]?>" height="<?=$arItem["DETAIL_PICTURE"]["TRUE_SIZE"][1]?>" border="" alt="" />
						</a>
					<?endif;?>
				</div>
			</div>
			<div class="bottom_info">
				<div class="time_wrap">
					<span><?=GetMessage("QUICK_TIME")?></span>
					<div class="digits" data-dateto="<? echo strtotime($timer['DATE_TO']); ?>">
						<div class="time">
							<? if ($timer["TIMER"]["DAYS"]==0) { ?>
								<div class="digit js-hours" id="hours" data-time=""></div>
								<div class="separator"></div>
								<div class="digit js-minutes" id="minutes" data-time=""></div>
								<div class="separator"></div>
								<div class="digit js-seconds" id="seconds" data-time=""></div>
							<? } else { ?>
								<div class="digit js-days" id="days" data-time=""></div>
								<div class="separator"></div>
								<div class="digit js-hours" id="hours" data-time=""></div>
								<div class="separator"></div>
								<div class="digit js-minutes" id="minutes" data-time=""></div>
							<? } ?>
						</div>
						<? if ($timer["QUANTITY"]>0) { ?>
							<div class="percentage">
								<div class="separator"></div>
								<div class="digit progress"><?=$timer["QUANTITY"]?></div>
							</div>
						<? } ?>
					</div>
				</div>
				<div class="title_part">
					<? if ($timer["TIMER"]["DAYS"]==0) { ?>
						<span><?=GetMessage("QUICK_TIME_HOUR")?></span>
						<span><?=GetMessage("QUICK_TIME_MIN")?></span>
						<span><?=GetMessage("QUICK_TIME_SEC")?></span>
						<? if ($timer["QUANTITY"]>0) { ?><span class="quantity"><?=GetMessage("QUICK_QUANT")?></span><? } ?>
					<? } else { ?>
						<span><?=GetMessage("QUICK_TIME_DAY")?></span>
						<span><?=GetMessage("QUICK_TIME_HOUR")?></span>
						<span><?=GetMessage("QUICK_TIME_MIN")?></span>
						<? if ($timer["QUANTITY"]>0) { ?><span class="quantity"><?=GetMessage("QUICK_QUANT")?></span><? } ?>
					<? } ?>
				</div>
			</div>
		</div>
	<? endforeach; ?>
<? endif; ?>
<!-- /first view -->