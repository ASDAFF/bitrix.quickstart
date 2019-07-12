<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<noindex>
<div id="test_ban" class="transbanner">
	<div class="data">
		<?echo json_encode($arResult['DATA']);?>
	</div>
</div>
<script type="text/javascript">
	window.banners = window.banners||[];
	banners.push(new iesa.Banner($("#test_ban").get(0),"",<?=$arResult["PARAMS"]["DELAY"]?>));
</script>
</noindex>
