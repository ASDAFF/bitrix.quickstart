<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<script>
$(function(){
    $('#slides').slides({
        preload: true,
        generateNextPrev: false,
	play: <?=$arParams['PLAY']?> 
    });
});
</script>
<div class="b-main-slider">
    <div id="slides">
        <div class="slides_container">
            <? foreach ($arResult['BANNERS'] as $banner) { ?>
            <div><?=$banner["CODE_"]?></div>
            <? } ?>
        </div>
    </div>  
</div>
