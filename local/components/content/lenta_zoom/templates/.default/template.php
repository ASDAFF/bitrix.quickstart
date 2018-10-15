<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$sliderId = uniqid();
?>
<div class="aprof-simple-slider-block">
	<style type="text/css">
	#aprof_simple_slider_<?= $sliderId?> { 
		width:<?=($arParams["SLIDE_WIDTH"]+15)*$arParams["CNT"]-15?>px;
		height:<?=$arParams["SLIDE_HEIGHT"]?>px;
	}
	#aprof_simple_slider_<?= $sliderId?> .aprof-simple-slider-wraper {
		width:<?=($arParams["SLIDE_WIDTH"]+15)*$arParams["CNT"]-15?>px;
	}
	#aprof_simple_slider_<?= $sliderId?> li {
		width:<?=$arParams["SLIDE_WIDTH"]?>px;
	}
	#aprof-popup-block_<?= $sliderId?> .aprof-popup-block-content {
		height:<?=$arParams["SLIDE_ZOOM_HEIGHT"]?>px;
		width:<?=$arParams["SLIDE_ZOOM_WIDTH"]?>px;
	}
	</style>
	<div id="aprof_simple_slider_<?= $sliderId?>" class="aprof-simple-slider">
		<div class="aprof-simple-slider-wraper">
			<ul>
				<?foreach($arResult["ELEMENTS"] as $arElement){?>
					<li><a href="javascript:void(0);" data-width="<?=$arElement["DETAIL_PICTURE"]["WIDTH"]?>" data-src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>"><img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" /></a></li>
				<?}?>
			</ul>
		</div>
		<a href="javascript:void(0);" class="aprof-simple-slider-larr"></a>
		<a href="javascript:void(0);" class="aprof-simple-slider-rarr"></a>
	</div>
	<div id="aprof-popup-block_<?= $sliderId?>" class="aprof-popup-block">
		<div class="aprof-popup-block-content"></div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#aprof_simple_slider_<?= $sliderId?>').aprofSimpleSliderDefault({
			popupBlock: '#aprof-popup-block_<?= $sliderId?>'
		});
	});
</script>