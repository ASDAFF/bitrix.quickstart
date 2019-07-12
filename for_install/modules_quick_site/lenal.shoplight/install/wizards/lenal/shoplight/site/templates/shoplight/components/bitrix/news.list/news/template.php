<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
if (count($arResult["ITEMS"]) < 1)
     return;
?>
<div class="b-footer__news">
<h3><?= GetMessage("SDNW_TITLE") ?></h3>
<div class="b-footer__news-section">
<? foreach ($arResult["ITEMS"] as $arItem): ?>
     <div class='block_news'>
          <div class='block_news__title'><a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?=$arItem["NAME"]?></a></div>
          <div class='block_news__date'><?= $arItem["DISPLAY_ACTIVE_FROM"] ?></div>
          <div class='block_news__detail'>
               <?=$arItem["PREVIEW_TEXT"]?>
          </div>
          <div class='clear'></div>
     </div>
<? endforeach; ?>

</div>
</div>

