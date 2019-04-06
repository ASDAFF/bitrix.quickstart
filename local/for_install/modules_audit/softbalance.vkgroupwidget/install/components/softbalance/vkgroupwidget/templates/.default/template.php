<!-- VK Widget -->
<div id="vk_groups"></div>
<script type="text/javascript">
VK.Widgets.Group("vk_groups", {
		mode: <?=$arParams["TYPE"];?>,
		<?if($arParams["WIDE"] == "Y"):?>wide: 1,<?endif;?>
		width: "<?=$arParams["WIDTH"];?>", 
		height: "<?=$arParams["HEIGHT"];?>", 
		color1: '<?=$arParams["COLOR_BACKGROUND"];?>', 
		color2: '<?=$arParams["COLOR_TEXT"];?>', 
		color3: '<?=$arParams["COLOR_BUTTON"];?>'
	}, 
	<?=$arResult["LINK"];?>
);
</script>