<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<div class="popup-gallery" id="popup-gallery">
<?foreach($arResult["ITEMS"] as $arItem):?>
    <!--<?=$arItem["ID"]?><br>
    <?=$arItem["NAME"]?><br>
    <?=$arItem["DISPLAY_ACTIVE_FROM"]?><br>
    <?=$arItem["WIDTH"]?> x <?=$arItem["HEIGHT"]?><br>
    <?=$arItem["THUMB_PATH"]?><br>
    <?=$arItem["PATH"]?><br>
    <?    
        $thumb = CFile::ResizeImageGet(
            $arItem["SOURCE_ID"],
            array("width" => 207, "height" => 154),
            BX_RESIZE_IMAGE_PROPORTIONAL
        );
        $src = ($thumb["src"]) ? $thumb["src"] : $arItem["THUMB_PATH"];
    ?>
    <?=$src?><br>-->
    
    <a href="<?=$arItem["PATH"]?>" <?if($arParams["DISPLAY_NAME"] == "Y"):?> title="<?=$arItem["NAME"]?>"<?endif;?> data-gallery="" rel="gallery-1">
        <img src="<?=$src?>" <?if($arParams["DISPLAY_NAME"] == "Y"):?> title="<?=$arItem["NAME"]?>"<?endif;?>>
    </a>
    <hr>
<?endforeach;?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>

<?if($arParams["BACK_URL"]):?>
    <br /><a href="<?=$arParams["BACK_URL"]?>"><?=GetMessage("C_BACK_URL_TITLE")?></a>
<?endif;?>
