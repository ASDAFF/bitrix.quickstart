<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if(strlen($arResult["ERROR_MESSAGE"])>0) ShowError($arResult["ERROR_MESSAGE"]);?>
<?if(is_array($arResult["STORES"]) && !empty($arResult["STORES"])):?>
	<?global $TEMPLATE_OPTIONS;?>
	<div class="block_wr <?=strtolower($TEMPLATE_OPTIONS["STORES"]["CURRENT_VALUE"]);?>">
		<div class="bg_map"></div>
		<div class="wrapper_inner">
			<div class="stores news">
				<div class="top_block">
					<?$title_block=($arParams["TITLE_BLOCK"] ? $arParams["TITLE_BLOCK"] : GetMessage('STORES_TITLE'));
					$url=($arParams["ALL_URL"] ? $arParams["ALL_URL"] : "contacts/stores/");
					$count=ceil(count($arResult["STORES"])/3);?>
					<div class="title_block"><?=$title_block;?></div>
					<a href="<?=$url;?>"><?=GetMessage('ALL_STORES')?></a>
				</div>
				<div class="stores_list">
					<div class="stores_navigation slider_navigation"></div>
					<ul class="stores_list_wr wr">
						<?foreach($arResult["STORES"] as $pid=>$arProperty):
							$pattern = '/^.*\((.*)\)/i';
							preg_match($pattern, $arProperty["TITLE"], $arAddress);?>
							<li class="item">
								<div class="wrapp_block">
									<a href="<?=$arProperty["URL"]?>"><span class="icon"></span><span class="text"><?=$arProperty["ADDRESS"]?></span></a>
									<?if($arAddress[1]){?>
										<div class="store_text">
											<span class="title"><?=GetMessage('ADDRESS')?></span>
											<span class="value"><?=$arAddress[1];?></span>
										</div>
										<div class="clear"></div>
									<?}?>
									<?if($arProperty["PHONE"] && $arParams["PHONE"]=="Y"){?>
										<div class="store_text">
											<span class="title"><?=GetMessage('PHONE')?></span>
											<span class="value"><?=$arProperty["PHONE"];?></span>
										</div>
										<div class="clear"></div>
									<?}?>
								</div>
							</li>
						<?endforeach;?>
					</ul>
					<ul class="flex-control-nav flex-control-paging">
						<?for($i=1;$i<=$count;$i++){?>
							<li>
								<a></a>
							</li>
						<?}?>
					</ul>
				</div>
				<div class="all_map">
					<a href="<?=$url;?>" class="wrapp_block">
						<div class="icon"></div>
						<div class="text"><?=GetMessage('ALL_STORES_ON_MAP')?></div>
					</a>
				</div>
			</div>
		</div>
	</div>
<?endif;?>
<script>
	/*$(document).ready(function(){
		var flexsliderItemWidth = 268,
			flexsliderItemMargin = 20;
		$(".stores .stores_list").flexslider({
			animation: "slide",
			selector: ".stores_list_wr > li",
			slideshow: false,
			slideshowSpeed: 6000,
			animationSpeed: 600,
			directionNav: true,
			//controlNav: false,
			pauseOnHover: true,
			animationLoop: true, 
			controlsContainer: ".stores_navigation",
			itemWidth: flexsliderItemWidth,
			itemMargin: flexsliderItemMargin, 
			manualControls: ".block_wr .flex-control-nav.flex-control-paging li a"
		});
		$('.stores').equalize({children: '.wrapp_block', reset: true});
		$(window).resize(function(){
			$('.stores .flex-viewport .stores_list_wr').equalize({children: '.item'});
		})
	});*/
	var timeoutSlide;
	InitFlexSlider = function() {
		var flexsliderItemWidth = 268,
			flexsliderItemMargin = 20;
		$(".stores .stores_list").flexslider({
			animation: "slide",
			selector: ".stores_list_wr > li",
			slideshow: false,
			slideshowSpeed: 6000,
			animationSpeed: 600,
			directionNav: true,
			//controlNav: false,
			pauseOnHover: true,
			animationLoop: true, 
			controlsContainer: ".stores_navigation",
			itemWidth: flexsliderItemWidth,
			itemMargin: flexsliderItemMargin, 
			// manualControls: ".block_wr .flex-control-nav.flex-control-paging li a"
			start:function(slider){
				$('.flex-control-nav li a').on('touchend', function(){
					// $(this).closest('.flex-control-nav').find('a').removeClass('touch');
					$(this).addClass('touch');
				})
				slider.find('li').css('opacity', 1);
			}
		});
		$('.stores').equalize({children: '.wrapp_block', reset: true});
	}
	$(document).ready(function(){
		$(window).resize(function(){
			clearTimeout(timeoutSlide);
			timeoutSlide = setTimeout(InitFlexSlider(),50);
			$('.stores .flex-viewport .stores_list_wr').equalize({children: '.item'});
		})
		

	});
</script>