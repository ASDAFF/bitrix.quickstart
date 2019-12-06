<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
?>
<h2 class="page-title"><?=$arResult["NAME"]?></h2>
<div class="overlay-container">
	<img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>">
	<a href="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" class="overlay popup-img-<?=$arResult["ID"]?>" title="<?=$arResult["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>">
		<i class="fa fa-search-plus"></i>
	</a>
</div>
<div class="space-bottom"></div>
<script>
jQuery(function(){
	$(".popup-img-<?=$arResult["ID"]?>").magnificPopup({
		type:"image",
		gallery: {
			enabled: true,
			tCounter : "%curr% <?=GetMessage("QUICK_EFFORTLESS_OF")?> %total%"
		}		
	});
});
</script>
<?if(!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])):?>
<div class="owl-carousel content-slider-with-controls-bottom-items-3 photo-block mb-30">
	<?foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["ITEMS"] as $arPhoto):?>
	<div class="overlay-container">
		<img src="<?=$arPhoto["PREVIEW"]["SRC"]?>" alt="<?=$arPhoto["DESCRIPTION"]?>">
		<a href="<?=$arPhoto["DETAIL"]["SRC"]?>" class="overlay popup-img-<?=$arResult["ID"]?>" title="<?=$arPhoto["DESCRIPTION"]?>">
			<i class="fa fa-search-plus"></i>
		</a>
	</div>
	<?endforeach?>	
</div>
<?endif?>
<?if(!empty($arResult["DETAIL_TEXT"])):?><div><?=$arResult["DETAIL_TEXT"]?></div><?endif?>
<?if(!empty($arResult["PROPERTIES"]["USE_SHARE"]["VALUE"])):?>
<script type="text/javascript">
(function() {
  if (window.pluso)if (typeof window.pluso.start == "function") return;
  if (window.ifpluso==undefined) { window.ifpluso = 1;
	var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
	s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
	s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
	var h=d[g]('body')[0];
	h.appendChild(s);
}})();
</script>
<div class="pluso pull-right mt-25" 
	data-url="http://<?=$_SERVER["SERVER_NAME"]?><?=$arResult["DETAIL_PAGE_URL"]?>" 
	data-image="http://<?=$_SERVER["SERVER_NAME"]?><?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" 
	data-description="<?=htmlspecialchars($arResult["DETAIL_TEXT"], ENT_QUOTES)?>" 
	data-title="<?=htmlspecialchars($arResult["NAME"], ENT_QUOTES)?>" 
	data-background="none;" 
	data-options="small,square,line,horizontal,nocounter,sepcounter=1,theme=14" 
	data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir">
</div>
<div class="clearfix"></div>
<?endif?>