<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 ?>
<?if ($arParams["POSITION"] != "in_place"):?> 
    <style>
        .pluso { 
            position: fixed !important;
            margin: 10px;
            <?if ($arParams["POSITION"] == "top_left"):?>
                top: 0;
                left: 0;
            <?elseif ($arParams["POSITION"] == "top_right"):?>
                top: 0;
                right: 0;
            <?elseif ($arParams["POSITION"] == "bottom_left"):?>
                bottom: 0;
                left: 0;
            <?elseif ($arParams["POSITION"] == "bottom_right"):?>
                bottom: 0;
                right: 0;
            <?endif?>
            }
    </style>
<?endif?>
<script async type="text/javascript">(function() {
  if(window.pluso) if(typeof window.pluso.start == "function") return;
  var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
  s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
  s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
  var h=d[g]('head')[0] || d[g]('body')[0];
  h.appendChild(s);
 })();</script>
<div class="pluso" data-options="<?=$arParams["SIZE"]?>,<?=$arParams["FORM"]?>,<?=$arParams["LINE"]?>,<?=$arParams["PLACEMENT"]?>,<?=$arParams["COUNTER"]?>,theme=<?=$arParams["THEME"]?>" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir,email,bookmark" data-background="transparent"></div>
 