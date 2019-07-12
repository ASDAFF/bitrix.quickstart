<?
global $TEMPLATE_OPTIONS;
// get actual basket counters from session
$arCounters = COptimus::getBasketCounters();
// and show fly counters in static content
?>
<?if(!COptimus::IsBasketPage() && !COptimus::IsOrderPage()):?>
	<div class="basket_fly">
		<div class="opener">
			<div title="" data-type="AnDelCanBuy" class="basket_count small clicked empty">
				<a href="<?=$arCounters['READY']['HREF']?>"></a>
				<div class="wraps_icon_block basket">
					<div class="count empty_items">
						<span>
							<span class="items">
								<span>0</span>
							</span>
						</span>
					</div>
				</div>
			</div>
			<div title="" data-type="DelDelCanBuy" class="wish_count small clicked empty">
				<a href="<?=$arCounters['DELAY']['HREF']?>"></a>
				<div class="wraps_icon_block delay">
					<div class="count empty_items">
						<span>
							<span class="items">
								<span>0</span>
							</span>
						</span>
					</div>
				</div>
			</div>
			<div title="<?=$arCounters['COMPARE']['TITLE']?>" class="compare_count small">
				<a href="<?=$arCounters['COMPARE']['HREF']?>"></a>
				<div id="compare_fly" class="wraps_icon_block compare">
					<div class="count empty_items">
						<span>
							<span class="items">
								<span>0</span>
							</span>
						</span>
					</div>
				</div>
			</div>
			<div title="<?=$arCounters['PERSONAL']['TITLE']?>" class="user_block small">
				<a href="<?=$arCounters['PERSONAL']['HREF']?>"></a>
				<div class="wraps_icon_block no_img user_reg"></div>
			</div>
		</div>
		<div class="basket_sort">
			<span class="basket_title"><?=GetMessage('T_BASKET')?></span>
		</div>
	</div>
	<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("header-cart");?>
		<?if(!COptimus::IsBasketPage() && !COptimus::IsOrderPage()){?>
			<?if($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"] == "FLY"):?>
				<script type="text/javascript">
					arBasketAsproCounters = <?=CUtil::PhpToJSObject($arCounters, false)?>;
					SetActualBasketFlyCounters();

					$(document).on('click', "#basket_line .basket_fly .opener > div.clicked", function(){
						function onOpenFlyBasket(_this){
							$("#basket_line .basket_fly .tabs li").removeClass("cur");
							$("#basket_line .basket_fly .tabs_content li").removeClass("cur");
							$("#basket_line .basket_fly .remove_all_basket").removeClass("cur");
							if(!$(_this).is(".wish_count.empty")){
								$("#basket_line .basket_fly .tabs_content li[item-section="+$(_this).data("type")+"]").addClass("cur");
								$("#basket_line .basket_fly .tabs li:eq("+$(_this).index()+")").addClass("cur");
								$("#basket_line .basket_fly .remove_all_basket."+$(_this).data("type")).addClass("cur");
							}
							else{
								$("#basket_line .basket_fly .tabs li").first().addClass("cur").siblings().removeClass("cur");
								$("#basket_line .basket_fly .tabs_content li").first().addClass("cur").siblings().removeClass("cur");
								$("#basket_line .basket_fly .remove_all_basket").first().addClass("cur");
							}
							$("#basket_line .basket_fly .opener > div.clicked").removeClass('small');
						}

						if(window.matchMedia('(min-width: 769px)').matches){
							var _this = this;
							if(parseInt($("#basket_line .basket_fly").css("right")) < 0){
								$("#basket_line .basket_fly").stop().animate({"right": "0"}, 333, function(){
									if($(_this).closest('.basket_fly.loaded').length){
										onOpenFlyBasket(_this);
									}
									else{
										$.ajax({
											url: arOptimusOptions['SITE_DIR'] + 'ajax/basket_fly.php',
											type: 'post',
											success: function(html){
												$('#basket_line .basket_fly').addClass('loaded').html(html);
												onOpenFlyBasket(_this);
											}
										});
									}
								});
							}
							else if($(this).is(".wish_count:not(.empty)") && !$("#basket_line .basket_fly .basket_sort ul.tabs li.cur").is("[item-section=DelDelCanBuy]")){
								$("#basket_line .basket_fly .tabs li").removeClass("cur");
								$("#basket_line .basket_fly .tabs_content li").removeClass("cur");
								$("#basket_line .basket_fly .remove_all_basket").removeClass("cur");
								$("#basket_line .basket_fly .tabs_content li[item-section="+$(this).data("type")+"]").addClass("cur");
								$("#basket_line  .basket_fly .tabs li:eq("+$(this).index()+")").first().addClass("cur");
								$("#basket_line .basket_fly .remove_all_basket."+$(this).data("type")).first().addClass("cur");
							}
							else if($(this).is(".basket_count") && $("#basket_line .basket_fly .basket_sort ul.tabs li.cur").length && !$("#basket_line .basket_fly .basket_sort ul.tabs li.cur").is("[item-section=AnDelCanBuy]")){
								$("#basket_line .basket_fly .tabs li").removeClass("cur");
								$("#basket_line .basket_fly .tabs_content li").removeClass("cur");
								$("#basket_line .basket_fly .remove_all_basket").removeClass("cur");
								$("#basket_line  .basket_fly .tabs_content li:eq("+$(this).index()+")").addClass("cur");
								$("#basket_line  .basket_fly .tabs li:eq("+$(this).index()+")").first().addClass("cur");
								$("#basket_line .basket_fly .remove_all_basket."+$(this).data("type")).first().addClass("cur");
							}
							else{
								$("#basket_line .basket_fly").stop().animate({"right": -$("#basket_line .basket_fly").outerWidth()}, 150);
								$("#basket_line .basket_fly .opener > div.clicked").addClass('small');
							}
						}
					});
				</script>
			<?elseif($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"] == "NORMAL"):?>
				<?/*$APPLICATION->IncludeComponent( "bitrix:sale.basket.basket.line", "normal", Array(
					"PATH_TO_BASKET" => SITE_DIR."basket/",
					"PATH_TO_ORDER" => SITE_DIR."order/",
					"SHOW_DELAY" => "Y",
					"SHOW_PRODUCTS"=>"Y",
					"SHOW_EMPTY_VALUES" => "Y",
					"SHOW_NOTAVAIL" => "N",
					"SHOW_SUBSCRIBE" => "N",
					"SHOW_IMAGE" => "Y",
					"SHOW_PRICE" => "Y",
					"SHOW_SUMMARY" => "Y",
					"SHOW_NUM_PRODUCTS" => "Y",
					"SHOW_TOTAL_PRICE" => "Y",
					"HIDE_ON_BASKET_PAGES" => "Y"
				));*/?>
				<script type="text/javascript">
					$(document).ready(function() {
						$.ajax({
							url: arOptimusOptions['SITE_DIR'] + 'ajax/show_basket_top.php',
							type: 'post',
							success: function(html){
								$('#basket_line').html(html);
								$('.header-compare-block').css({'opacity':'1'});
							}
						});
					});
				</script>
			<?endif;?>
		<?}else{?>
			<script type="text/javascript">
				$('.header-compare-block').closest('.wrapp_all_icons').css({'width': 'auto'});
				$('.header-compare-block').css({'opacity':'1'});
			</script>
		<?}?>
	<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("header-cart", "");?>
<?endif;?>