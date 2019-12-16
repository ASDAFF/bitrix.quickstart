<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$advId = $this->getEditAreaId('adv');

$frame = $this->createFrame()->begin('');
    if (!empty($arResult['BANNERS'])): ?>
    <div class="adv_owl owl-shift" id="<?=$advId?>">
    <?php foreach ($arResult['BANNERS'] as $k => $banner): ?>
        <?=$banner?>
    <?php endforeach; ?>
    </div>
    <?php endif;
$frame->end();

$owlParams = array(
    'autoplayHoverPause' => true,
    'dots' => false,
    'items' => 1,
    'margin' => 8,
    'stagePadding' => 16,
    'nav' => false,
    'responsive' => array(
        768 => array(
            'items' => 3
        ),
        992 => array(
            'nav' => true,
            'items' => 4
        ),
    )
);
if ($arParams['SLIDER_CENTER']) {
    $owlParams['center'] = $arParams['SLIDER_CENTER'];
}
/*
if ($arParams['SLIDER_ANIMATEIN']) {
    $owlParams['animateIn'] = $arParams['SLIDER_ANIMATEIN'];
}
if ($arParams['SLIDER_ANIMATEOUT']) {
    $owlParams['animateOut'] = $arParams['SLIDER_ANIMATEOUT'];
}*/

if ($bIsVideo) {
    $owlParams['video'] = true;
}
if ($arParams['SLIDER_AUTOPLAY']) {
    $owlParams['autoplay'] = $arParams['SLIDER_AUTOPLAY'] == 'Y';
    $owlParams['autoplaySpeed'] = $arParams['SLIDER_AUTOPLAY_SPEED'];
    $owlParams['autoplayTimeout'] = $arParams['SLIDER_AUTOPLAY_TIMEOUT'];
}
if ($arParams['SLIDER_LAZYLOAD']) {
    $owlParams['lazyLoad'] = $arParams['SLIDER_LAZYLOAD'];
}
if ($arParams['SLIDER_LOOP']) {
    $owlParams['loop'] = $arParams['SLIDER_LOOP'];
}
if ($arParams['SLIDER_SMARTSPEED']) {
    $owlParams['smartSpeed'] = $arParams['SLIDER_SMARTSPEED'];
}

?>
<script>
$(document).ready(function(){
  var bannerOption = <?=CUtil::PhpToJSObject($owlParams, false, false, true)?>,
      $bannerCarousel = $('#<?=$advId?>');

  $bannerCarousel.find('img:last').onImageLoad(function(){
    $bannerCarousel.owlCarousel(
      $.extend(
          {}, appSLine.owlOptions, bannerOption, {
          onInitialized: function () {
            this.$element.addClass('owl-carousel');<?
            if ($owlParams['autoplay']) {
              ?>this.$element.find('.center').find('.owl-video-play-icon').trigger('click.owl.video');<?
            }
          ?>},
        }
      )
    );
  });
});
</script>