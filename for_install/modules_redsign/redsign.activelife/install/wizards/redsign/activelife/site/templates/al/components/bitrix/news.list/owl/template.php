<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$advId = $this->getEditAreaId('adv');

$owlParams = array(
    'autoplayHoverPause' => true,
    'dots' => false,
    'items' => 1,
    'margin' => 20,
    'nav' => false,
    'responsive' => array(
        480 => array(
            'items' => 2
        ),
        992 => array(
            'nav' => true,
            'items' => 3
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

<?php if(is_array($arResult['ITEMS']) && 0 < count($arResult['ITEMS'])): ?>
    <div class="adv_owl owl-shift" id="<?=$advId?>">
        <?php foreach($arResult['ITEMS'] as $key1 => $arItem): ?>
            <a href="<?=$arItem['DISPLAY_PROPERTIES']['LINK']['VALUE']?>"<?php if($arItem['DISPLAY_PROPERTIES']['TARGET']['VALUE'] != ''): ?> target="_blank"<?php endif; ?>>
                <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arResult['PREVIEW_PICTURE']['ALT']?>"  title="<?=$arResult['PREVIEW_PICTURE']['TITLE']?>">
            </a>
        <?php endforeach; ?>
    </div>
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
<?php endif; ?>