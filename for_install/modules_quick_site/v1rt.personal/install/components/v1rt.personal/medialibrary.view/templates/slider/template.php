<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="slider-wrapper theme-default">
    <div id="slider" class="nivoSlider">
    <?if(count($arResult["FOLDER_IMAGE"])):?>
        <?foreach($arResult["FOLDER_IMAGE"] as $img):?>
            <img src="<?=$img["URL"]["IMAGE"]?>"<?=$img["SIZE"]?> alt=""/>
        <?endforeach;?>
    <?endif;?>
    </div>
</div>