<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a href="<?=SITE_DIR?>offers/favourites/" class="realty-favourites"><?=GetMessage("CITRUS_REALTY_FAV_TEXT")?> (<?=intval($arResult["COUNT"])?>)</a>
<script defer>
window.citrusRealtyFav = <?=\Bitrix\Main\Web\Json::encode(array_keys($arResult["LIST"]))?>;
</script>