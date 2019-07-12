<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<? if (count($arResult["ITEMS"]) < 1) { return; }
CModule::IncludeModule("currency"); CModule::IncludeModule("sale"); ?>
<div class="owl-carousel main_banner_big">
	<? foreach ($arResult["ITEMS"] as $arItem) {
		?><div class="items">
			<div class="img" style="background-image: url('<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>')"></div>
			<div class="container">
				<div class="name"><?=$arItem["~NAME"];?></div>
				<? if (strlen($arItem["DISPLAY_PROPERTIES"]["PRICE"]["VALUE"]) > 0) {
					?><div class="price_banner radius5 inline"<? if (strlen($arItem["DISPLAY_PROPERTIES"]["LINK"]["VALUE"]) > 0) { echo ' style="right: 150px;"'; } ?>><?=str_replace(GetMessage("STUDIOFACT_RUB"), '<span class="rub">'.GetMessage("STUDIOFACT_R").'</span>', SaleFormatCurrency($arItem["DISPLAY_PROPERTIES"]["PRICE"]["VALUE"], CCurrency::GetBaseCurrency()));?></div><?
				} ?>
				<? if (strlen($arItem["DISPLAY_PROPERTIES"]["LINK"]["VALUE"]) > 0) {
					?><a href="<?=$arItem["DISPLAY_PROPERTIES"]["LINK"]["VALUE"];?>" class="more radius5"><?=$arItem["DISPLAY_PROPERTIES"]["LINK"]["NAME"];?></a><?
				} ?>
			</div>
		</div><?
	} ?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$(".owl-carousel").owlCarousel({
			autoPlay: 5000,
			stopOnHover: true,
			navigation: true,
			slideSpeed: 500,
			singleItem: true,
			autoHeight: false,
			transitionStyle: "fade"
		});
		leftmenu ();
		$("#main_block_page").css("margin-top", $(".main_banner_big").height() + "px");
	});
</script>