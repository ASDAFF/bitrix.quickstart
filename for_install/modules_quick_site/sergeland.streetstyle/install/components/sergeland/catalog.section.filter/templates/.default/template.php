<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arResult["~PRICES"]["SHOW_PRICE"]):?>
	<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
	<script>
		jQuery(function(){
			var slider 	= $(".filter-container .slider.<?=$PRICE_CODE?>"),
				submit	= slider.parent().find(".slider-submit a"),
				min 	= parseInt("<?=$arResult["~PRICES"]["MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]?>") || 0,
				max		= parseInt("<?=$arResult["~PRICES"]["MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"]?>") || 20000;
			
			min = (min == max ? 0 : min);			
			slider.slider("option", {
					min: min,
					max: max,
					values: [min, max]
			});			
			slider.find(".ui-slider-handle").each(function(){									
				if($(this).data("uiSliderHandleIndex") == 0)
					 $(this).children().html(slider.slider("values", 0));					
				else $(this).children().html(slider.slider("values", 1));						
			});
			$(submit).click(function(){
				$(this).attr("href", slider.attr("data-min")+"="+slider.find(".left").html()+slider.attr("data-max")+"="+slider.find(".right").html());
			});
		});
	</script>
	<?endforeach?>
<?endif?>	
<div class="filter-container">
	<?foreach($arResult["ITEMS"] as $arItem):?>
	<div class="widget-container widget_categories">
		<h3 class="widget-title"><?=$arItem["NAME"]?></h3>
			<div class="filter">
			<ul>
			<?foreach($arItem["VALUE"] as $arField):?>
				<li><a href="<?=$arField["DETAIL_PAGE_URL"]?>"><?=$arField["NAME"]?><?if($arResult["SHOW_COUNT_ELEMENT"]):?> <span class="kol-elem">(<?=$arField["COUNT"]?>)</span><?endif?></a></li>
			<?endforeach?>
			</ul>
			</div>
			<div class="line"></div>
	</div>
	<?endforeach;?>
	<?if($arResult["~PRICES"]["SHOW_PRICE"]):?>
		<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
		<div class="widget-container widget_categories price">	
			<h3 class="widget-title"><?=GetMessage("SERGELAND_FILTER_PRICE")?></h3>
			<div class="slider <?=$PRICE_CODE?>" data-min="<?=$arResult["~PRICES"]["MINIMUM_PROP_URL"][$PRICE_CODE]?>" data-max="<?=$arResult["~PRICES"]["MAXIMUM_PROP_URL"][$PRICE_CODE]?>"></div>
			<div class="slider-submit"><a href=""><?=GetMessage("SERGELAND_FILTER_SUBMIT")?></a></div>
		</div>
		<?endforeach?>
	<?endif?>
</div>