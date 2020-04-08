<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<?if(isset($arResult["DISPLAY_ACTIVE_FROM"]) && !empty($arResult["DISPLAY_ACTIVE_FROM"])):?>
	<div><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div>
<?endif;?>
<!-- detail-text -->
<div class="detail-text">
	<?if(isset($arItem["DETAIL_PICTURE"]["SRC"]) && !empty($arItem["DETAIL_PICTURE"]["SRC"])):?>
		<img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["~NAME"]?>" />
	<?else:?>
		<img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["~NAME"]?>" />
	<?endif;?>
	<?=$arResult["~DETAIL_TEXT"]?>
</div>

<!-- detail-photo -->
<?if(isset($arResult["DISPLAY_PROPERTIES"]["GALLERY"]["FILE_VALUE"]) && !empty($arResult["DISPLAY_PROPERTIES"]["GALLERY"]["FILE_VALUE"])):?>
	<div class="detail-photo">
		<h2><?=GetMessage("PHOTO");?></h2>

		<div class="photos">
			<?foreach($arResult["DISPLAY_PROPERTIES"]["GALLERY"]["FILE_VALUE"] as $arImage):?>
				<div class="photos-item"><img src="<?=$arImage["SRC"]?>" alt="<?=$arImage["ORIGINAL_NAME"]?>" /></div>
			<?endforeach;?>
		</div>
	</div>
<?endif;?>
