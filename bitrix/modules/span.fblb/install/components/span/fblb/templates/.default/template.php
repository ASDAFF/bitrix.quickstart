<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=210765678934549";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="fb-like-box" data-href="<?=$arParams['FB_DOMAIN']?>" data-width="<?=$arParams['FB_WIDTH']?>" data-height="<?=$arParams['FB_HEIGHT']?>" data-show-faces="true" data-border-color="#<?=$arParams['FB_BORDER']?>" data-stream="true" data-header="true"></div>