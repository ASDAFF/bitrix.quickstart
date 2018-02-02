<?
$arParams['PICTURE_WIDTH'] = ($arParams['PICTURE_WIDTH'] > 0 ) ? $arParams['PICTURE_WIDTH'] : 100;

if($arParams['INCLUDE_BXSLIDER'] == 'Y'):?>
	<script type="text/javascript" src="/bitrix/js/alfa1c.adsectlist/jquery.bxslider.min.js"></script>
<?endif;?>
<script>
$(document).ready(function(){
	$('.section-list-slider').bxSlider({
			  auto:true,
			  minSlides: 1,
			  maxSlides: 6,
			  slideWidth: <?=$arParams['PICTURE_WIDTH'];?>,
			  slideMargin: 10,
			  controls:true,
			  pager:false
	});
})
</script>