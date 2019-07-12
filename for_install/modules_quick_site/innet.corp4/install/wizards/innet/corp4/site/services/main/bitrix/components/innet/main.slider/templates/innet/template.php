<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('.bxslider').bxSlider({
            mode: 'fade',
            pager: true,
            controls: true,
            auto: false,
            pause: <?=$arParams['INNET_SLIDE_PAUSE']?>000,
            speed: <?=$arParams['INNET_SLIDE_SPEED']?>000
        });
    });
</script>

<!-- to enable the slider width the content of the added class size2 block slider1 -->
<div class="slider1">
    <ul class="bxslider">
        <?$first = array_shift($arResult);?>
        <li class="slide1" style="background: url(<?=$first['PREVIEW_PICTURE']?>) no-repeat center 0;">
            <div class="inner">
                <?=$first['PREVIEW_TEXT']?>
                <div class="in-row">
                    <a href="<?=$first['PROPERTY_SLIDER_LINK_VALUE']?>" class="btn"><?=$first['PROPERTY_SLIDER_LINK_NAME_VALUE']?></a>
                    <a href="<?=$first['PROPERTY_SLIDER_LINK_2_VALUE']?>" class="btn2"><?=$first['PROPERTY_SLIDER_LINK_NAME_2_VALUE']?></a>
                </div>
            </div>
        </li>

        <?foreach ($arResult as $slide) {?>
            <?$frame = $this->createFrame()->begin();?>
                <li class="slide1" style="background: url(<?=$slide['PREVIEW_PICTURE']?>) no-repeat center 0;">
                    <div class="inner">
                        <?=$slide['PREVIEW_TEXT']?>
                        <div class="in-row">
                            <a href="<?=$slide['PROPERTY_SLIDER_LINK_VALUE']?>" class="btn"><?=$slide['PROPERTY_SLIDER_LINK_NAME_VALUE']?></a>
                            <a href="<?=$slide['PROPERTY_SLIDER_LINK_2_VALUE']?>" class="btn2"><?=$slide['PROPERTY_SLIDER_LINK_NAME_2_VALUE']?></a>
                        </div>
                    </div>
                </li>
            <?$frame->end();?>
        <?}?>
    </ul>
</div>