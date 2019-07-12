<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->createFrame()->begin();?>
<?if($arResult["COUNT"] <= 0)
	return;
?>
<div id="banner-carousel-1" class="carousel slide" data-ride="carousel" data-interval="20000">
	<!-- Indicators -->
	<ol class="carousel-indicators">
	<?
	for ($i=0; $i < $arResult["COUNT"]; $i++) { 
	?><li data-target="#banner-carousel-1" data-slide-to="<?=$i?>"<?=($i == 0 ? ' class="active"' : '')?>></li><?
	}
	?>
	</ol>
	<!-- Wrapper for slides -->
	<div class="carousel-inner">
	<?foreach ($arResult["banners"] as $key => $arBanner) {?>
	<?=$arBanner?>
	<?}?>
	</div>
	<!-- Controls -->
	<a class="left carousel-control" href="#banner-carousel-1" role="button" data-slide="prev">
		<span class="chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#banner-carousel-1" role="button" data-slide="next">
		<span class="chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	</a>
</div>