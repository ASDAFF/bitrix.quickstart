<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$i = 1;
?>
<div class="catalog_banner">
	<a href="#" class="small_banner_2"></a>
</div>

<!--
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
</div>-->
