<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (count($arResult) > 0):?>
<div class="have_look_splitter"></div>
<div class="catalog">
	<div class="have_look_title">
		<h3><?=GetMessage("VIEW_HEADER");?></h3>
	</div>
	<div class="have_look_slider_wrapper" id="have_look_slider_wrapper">
		<ul id="have_look_slider">
		<?foreach($arResult as $arItem):?>
			<li class="catalog_item">
				<div class="catalog_item_content">
					<div class="catalog_item_top_block">
						<?if($arParams["VIEWED_IMAGE"]=="Y" && is_array($arItem["PICTURE"])):?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="catalog_item_content_a"><img src="<?=$arItem["PICTURE"]["src"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>"></a>
						<?endif?>
						<?if($arParams["VIEWED_PRICE"]=="Y"):?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class='prices'>
								<span class='price'><?=$arItem["PRICE_FORMATED"]?></span>
							</a>
						<?endif?>
					</div>
					<div class="catalog_item_descr">
						<?if($arParams["VIEWED_NAME"]=="Y"):?>
							<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h4>
						<?endif?>
					</div>
				</div>
			</li>
		<?endforeach;?>
		</ul>
		<div class="clearfix"></div>
		<a class="prev" id="have_look_slider_prev" href="#"><span>prev</span></a>
		<a class="next" id="have_look_slider_next" href="#"><span>next</span></a>
		<div class="pagination" id="have_look_slider_pag"></div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#have_look_slider").carouFredSel({
			circular: true,
			infinite: false,
			auto: false,
			width: "100%",
			align: false,
			prev: {
					button: "#have_look_slider_prev",
					key: "left"
				},
				next: {
					button: "#have_look_slider_next",
					key: "right"
				},
				pagination: "#have_look_slider_pag"
		});

		$('#have_look_slider_wrapper').mouseenter(function(){
				$('.prev', this).css('visibility', 'visible');
				$('.next', this).css('visibility', 'visible');
			});
		$('#have_look_slider_wrapper').mouseleave(function(){
				$('.prev', this).css('visibility', 'hidden');
				$('.next', this).css('visibility', 'hidden');
			});

	});
</script>

<?endif;?>