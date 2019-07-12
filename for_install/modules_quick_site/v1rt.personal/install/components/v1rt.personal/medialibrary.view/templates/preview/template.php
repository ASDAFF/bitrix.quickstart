<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<script type="text/javascript">
    $(document).ready(function(){
        $("a[rel=image_group_<?=md5(print_r($arParams["FOLDERS"], true))?>]").fancybox({
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'titlePosition' 	: 'over',
            'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
                return '<span id="fancybox-title-over">' + tplImage + ' ' + (currentIndex + 1) + ' / ' + currentArray.length + ($("#img_" + $(currentArray[currentIndex]).attr('id')).attr("title").length ? '&nbsp;- ' + $("#img_" + $(currentArray[currentIndex]).attr('id')).attr("title") : '') + '</span>';
            }
        });
        
        $(".preview-gal-img").each(function(){$(this).stop().animate({opacity:'0.5'})});
        $(".preview-gal-img").hover(function(){
            $(this).stop().animate({opacity:'1.0'})
        },
            function(){$(this).stop().animate({opacity:'0.5'})}
        );
    });
</script>
<?if(count($arResult["FOLDER_IMAGE"])):?>
    <?foreach($arResult["FOLDER_IMAGE"] as $img):?>
        <a href="<?=$img["URL"]["FILE"]?>" rel="image_group_<?=md5(print_r($arParams["FOLDERS"], true))?>" id="<?=$img["RAND_ID"]?>" class="preview-gal-img"><img src="<?=$img["URL"]["IMAGE"]?>"<?=$img["SIZE"]?> id="img_<?=$img["RAND_ID"]?>" title="<?=$arResult["DESCRIPTION"][$img["ID"]];?>" alt="<?=$arResult["DESCRIPTION"][$img["ID"]];?>"/></a>       
    <?endforeach;?>
<?endif;?>
<div style="clear: both;">&nbsp;</div>