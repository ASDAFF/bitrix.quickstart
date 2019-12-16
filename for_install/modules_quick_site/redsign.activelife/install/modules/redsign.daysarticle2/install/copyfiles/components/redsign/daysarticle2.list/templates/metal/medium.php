<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode(true); ?>
<?if(count($arResult["ITEMS"])>0):?>
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
		<? if (isset($arItem['OFFERS']) && !empty($arItem['OFFERS'])) { 
			$element = $arItem['OFFERS'][0];
		 } else {
			$element = $arItem;
		 } 
		 if (isset($arItem['OFFERS'][0]['DAYSARTICLE2']) && !empty($arItem['OFFERS'][0]['DAYSARTICLE2'])) { 
			$timer = $arItem['OFFERS'][0]['DAYSARTICLE2'];
		 } else {
			$timer = $arItem['DAYSARTICLE2'];
		 } ?>
				<!-- second view -->
		<div class="daysarticle metal_wrap view2" id="<?=$element['ID']?>">
			<div class="left_part">
				<h4><?=GetMessage("DA2_BLOCK_TITLE")?></h4>
				<div class="prices_wrap">
					<div class="price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_VALUE"]?></div>
					<div class="discount"><span><?=GetMessage("DISCOUNT_VALUE")?></span> <?=$timer["DISCOUNT_FORMATED"]?></div>
					<div class="discount_price"><?=$element["PRICES"][$arParams["PRICE_CODE"]]["PRINT_DISCOUNT_VALUE"]?></div>
				</div>
				<div class="progress_bar">
					<div class="progress" style="width: <?=$timer["DINAMICA_EX"]["PHP_DATA"]["persent"]?>%;"><?=$timer["DINAMICA_EX"]["PHP_DATA"]["persent"]?>%</div>
				</div>
				<div class="time_wrap">
				<span><?=GetMessage("DA2_TIME")?></span>
				<? if ($timer["TIMER"]["DAYS"]==0) { ?>
					<div class="digits" data-dateto="<? echo strtotime($timer['DATE_TO']); ?>">
						<div class="digit" id="hours"><span class="number js-hours" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit" id="minutes"><span class="number js-minutes" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit" id="seconds"><span class="number js-seconds" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit progress"><span class="number"><?=$timer["DINAMICA_EX"]["PHP_DATA"]["persent"]?></span><div class="digit_outter"></div></div>
					</div>
					<div class="digit_titles">
						<span class="digit_title"><?=GetMessage("DA2_TIME_HOUR")?></span>
						<span class="digit_title"><?=GetMessage("DA2_TIME_MIN")?></span>
						<span class="digit_title"><?=GetMessage("DA2_TIME_SEC")?></span>
						<span class="digit_title percent">%</span>
					</div>
				<? } else { ?>
					<div class="digits" data-dateto="<? echo strtotime($timer['DATE_TO']); ?>">
						<div class="digit" id="days"><span class="number js-days" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit" id="hours"><span class="number js-hours" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit" id="minutes"><span class="number js-minutes" data-time=""></span><div class="digit_outter"></div></div>
						<div class="digit progress"><span class="number"><?=$timer["DINAMICA_EX"]["PHP_DATA"]["persent"]?></span><div class="digit_outter"></div></div>
					</div>
					<div class="digit_titles">
						<span class="digit_title"><?=GetMessage("DA2_TIME_DAY")?></span>
						<span class="digit_title"><?=GetMessage("DA2_TIME_HOUR")?></span>
						<span class="digit_title"><?=GetMessage("DA2_TIME_MIN")?></span>
						<span class="digit_title percent">%</span>
					</div>
				<? } ?>
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
				<div class="titles">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
				</div>
			</div>
		</div>
		<!-- /second view -->
	<? endforeach; ?>
<? endif; ?>