<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arParams["AUTOPLAY"] = $arParams["AUTOPLAY"] == "Y" ? 1 : 0;
	$arParams["START_SLIDE"] = $arParams["START_SLIDE"] == "Y" ? 1 : 0;
?>
<script>
jQuery(document).ready(function($){

	$.supersized.themeVars.image_path = '<?=$arParams["THEMEVARS_IMAGE_PATH"]?>';
	$.supersized({
		autoplay			:	<?=$arParams["AUTOPLAY"]?>,
		start_slide			:	<?=$arParams["START_SLIDE"]?>,
		
		// Functionality
		slide_interval		:	<?=$arParams["SLIDE_INTERVAL"]?>,
		transition			:	<?=$arParams["TRANSITION"]?>,
		transition_speed	:	<?=$arParams["TRANSITION_SPEED"]?>,

		// Components
		slide_links			:	'<?=$arParams["SLIDE_LINKS"]?>',
		slides 				:	[
									<?foreach($arParams["BANNER_IMAGES_BIG"] as $key=>$banner_images_big):?>
										<?if(empty($banner_images_big)) continue;												
										  $arParams["BANNER_HEAD"][$key] = str_replace("'", "\"", $arParams["BANNER_HEAD"][$key]);
										  $arParams["BANNER_TEXT"][$key] = str_replace("'", "\"", $arParams["BANNER_TEXT"][$key]);
										?><?if($key>0):?>,<?endif?>
										{
											image	:	'<?=$banner_images_big?>'<?if(!empty($arParams["BANNER_IMAGES_SMALL"][$key])):?>,
											thumb	:	'<?=$arParams["BANNER_IMAGES_SMALL"][$key]?>'<?endif?><?if(!empty($arParams["BANNER_HEAD"][$key])):?>,
											title	:	'<?=html_entity_decode($arParams["BANNER_HEAD"][$key])?>'<?endif?><?if(!empty($arParams["BANNER_TEXT"][$key])):?>,
											desc	:	'<?=html_entity_decode($arParams["BANNER_TEXT"][$key])?>'<?endif?>
										}
									<?endforeach?>
								]
	});
});
</script>