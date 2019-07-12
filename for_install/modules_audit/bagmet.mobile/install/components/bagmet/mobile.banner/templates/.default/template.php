<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$i = 1;
?>
<div class="top_block">
	<div class="flexslider">
		<ul class="slides">
			<?
			foreach ($arResult["ITEMS"] as $arBanner)
			{
			?>
				<li id="slide_<?=$i?>" style="background-image: url('<?=$arBanner["DETAIL_PICTURE"]["SRC"]?>');">
					<?if (isset($arBanner["PROPERTY_BANNER_LINK_VALUE"])):?>
					<a href="<?=$arBanner["PROPERTY_BANNER_LINK_VALUE"]?>"></a>
					<?endif?>
				</li>
			<?
				$i++;
			}
			?>
		</ul>
	</div>
</div>
<div class="top_block_bottom_line"></div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.flexslider').flexslider({
			pauseOnAction: true,
			pauseOnHover: true
		});

		$('.flexslider').mouseenter(function(){
			$('.flex-prev', this).css('visibility', 'visible');
			$('.flex-next', this).css('visibility', 'visible');
		});
		$('.flexslider').mouseleave(function(){
			$('.flex-prev', this).css('visibility', 'hidden');
			$('.flex-next', this).css('visibility', 'hidden');
		});
	});
</script>