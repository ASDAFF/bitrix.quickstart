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
<?if(!empty($arResult["PROPERTIES"]["HREF_VIDEO"]["VALUE"])):?>
<div class="embed-responsive embed-responsive-16by9 mb-35">
	<iframe class="embed-responsive-item" src="<?=$arResult["PROPERTIES"]["HREF_VIDEO"]["VALUE"]?>?rel=0&showinfo=0&color=white&html5=1" allowfullscreen></iframe>
</div>
<?elseif(!empty($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"])):?>
<script>
jQuery(function(){
	$(".popup-img-top-<?=$arResult["ID"]?>").magnificPopup({
		type:"image",
		gallery: {
			enabled: true,
			tCounter : "%curr% <?=GetMessage("QUICK_EFFORTLESS_OF")?> %total%"
		}		
	});
});
</script>
<div class="owl-carousel <?if(!empty($arResult["PROPERTIES"]["PHOTO_TOP_AUTOPLAY"]["VALUE"])):?>content-slider-with-controls-autoplay<?else:?>content-slider-with-controls<?endif?> photo-block mb-35">
<?foreach($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"] as $arImage):?>
	<div class="overlay-container">
		<img src="<?=$arImage["SRC"]?>" alt="<?=$arResult["NAME"]?>">
		<a href="<?=$arImage["SRC"]?>" title="<?=$arImage["DESCRIPTION"]?>" class="popup-img-top-<?=$arResult["ID"]?> overlay"><i class="fa fa-search-plus"></i></a>
	</div>
<?endforeach?>
</div>
<?endif?>
<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
<div class="tags mb-10">
	<span class="badge transparent-bg"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
</div>
<?endif?>
<h1 class="page-title mb-15"><?=$arResult["NAME"]?></h1>
<?=$arResult["DETAIL_TEXT"];?>
<?if($arParams["USE_SHARE"] == "Y"):?>
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
<div class="pluso pull-right mt-15 mb-15" 
	data-url="http://<?=$_SERVER["SERVER_NAME"]?><?=$arResult["DETAIL_PAGE_URL"]?>" 
	data-image="http://<?=$_SERVER["SERVER_NAME"]?><?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" 
	data-description="<?=htmlspecialchars($arResult["PREVIEW_TEXT"], ENT_QUOTES)?>" 
	data-title="<?=htmlspecialchars($arResult["NAME"], ENT_QUOTES)?>" 
	data-background="none;" 
	data-options="small,square,line,horizontal,nocounter,sepcounter=1,theme=14" 
	data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir">
</div>
<div class="clearfix"></div>
<?endif?>
<?if(!empty($arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"])):?>
<?if(!empty($arResult["PROPERTIES"]["DOCUMENTS_HEADER"]["VALUE"])):?><h5><?=$arResult["PROPERTIES"]["DOCUMENTS_HEADER"]["VALUE"]?></h5><?endif?>
<div class="doc mt-15 mb-20">
	<?$count = count($arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"]);
	foreach($arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"] as $arItem):?>
		<?if($cell%2 == 0):?>
		<div class="row">
		<?endif?>
			<div class="<?if($count>1):?>col-sm-6<?else:?>col-sm-12<?endif?>">
				<i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?>"></i><a href="<?=$arItem["PROPERTIES"]["FILE"]["SRC"]?>" target="_blank"><?=$arItem["NAME"]?></a><?if(!empty($arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"])):?><span class="file-type">[<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>]</span><?endif?>
			</div>
		<?$cell++;
		if($cell%2 == 0 || $count == $cell):?>
		</div>
		<?endif?>
	<?endforeach?>
</div>
<?endif?>

<?if(!empty($arResult["PROPERTIES"]["SHOW_CALLBACK_FORM"]["VALUE"])):?>
<div class="gray-bg row mt-40 mb-30 pt-15">
	<div class="col-md-12">
		<div id="results-callback">
			<div class="alert alert-danger" id="beforesend-callback">
				<?=GetMessage("QUICK_EFFORTLESS_NEWS_BEFORESEND")?>
			</div>
			<div class="alert alert-danger" id="error-callback">
				<?=GetMessage("QUICK_EFFORTLESS_NEWS_ERROR")?>
			</div> 
			<div class="alert alert-success" id="success-callback">
				<?=GetMessage("QUICK_EFFORTLESS_NEWS_SUCCESS")?>
			</div>
		</div>
	</div>
	<img src="<?=SITE_DIR?>images/loading-t.gif" alt="Loading" id="form-loading-callback" class="pull-right mb-10 pr-10" />
	<div class="clearfix"></div>
	<form name="CALLBACK" action="<?=SITE_DIR?>include/" method="POST" role="form">
		<input type="hidden" name="CALLBACK[SITE_ID]" value="<?=SITE_ID?>"/>
		<input type="hidden" name="CALLBACK[TITLE]" value="<?=$arResult["NAME"]?>"/>
		<div class="col-md-4 col-sm-6">
			<div class="form-group has-feedback">
				<input type="text" name="CALLBACK[NAME]" placeholder="<?=GetMessage("QUICK_EFFORTLESS_NEWS_NAME")?>" class="form-control req">
				<i class="fa fa-user form-control-feedback"></i>
			</div>
		</div>
		<div class="col-md-4 col-sm-6">
			<div class="form-group has-feedback">
				<input type="tel" name="CALLBACK[PHONE]" placeholder="<?=GetMessage("QUICK_EFFORTLESS_NEWS_PHONE")?>" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}" class="form-control req">
				<i class="fa fa-phone form-control-feedback"></i>
			</div>
		</div>
		<div class="col-md-4 col-sm-12">
			<div class="form-group has-feedback">
				<button type="submit" class="btn btn-white btn-block"><i class="fa fa-sign-out"></i> <?=$arResult["PROPERTIES"]["TEXT_CALLBACK_FORM"]["VALUE"]?></button>
			</div>
		</div>
	</form>
</div>
<?endif?>
<?if(!empty($arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"])):?>
<script>
jQuery(function(){
	$(".popup-img-bottom-<?=$arResult["ID"]?>").magnificPopup({
		type:"image",
		gallery: {
			enabled: true,
			tCounter : "%curr% <?=GetMessage("QUICK_EFFORTLESS_OF")?> %total%"
		}		
	});
});
</script>
<div class="owl-carousel <?if(!empty($arResult["PROPERTIES"]["PHOTO_BOTTOM_AUTOPLAY"]["VALUE"])):?>content-slider-with-controls-bottom-autoplay-items-3<?else:?>content-slider-with-controls-bottom-items-3<?endif?> photo-block mt-40 mb-40">
	<?foreach($arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"] as $cell=>$arImage):?>
	<div class="image-box <?if($cell<3 && empty($arResult["PROPERTIES"]["PHOTO_BOTTOM_AUTOPLAY"]["VALUE"])):?>object-non-visible<?endif?>" <?if($cell<3 && empty($arResult["PROPERTIES"]["PHOTO_BOTTOM_AUTOPLAY"]["VALUE"])):?>data-animation-effect="fadeInLeft" data-effect-delay="<?=(300-$cell*100)?>"<?endif?>>
		<div class="overlay-container">
			<img src="<?=$arImage["PREVIEW"]["SRC"]?>" alt="<?=$arImage["DESCRIPTION"]?>">
			<div class="overlay">
				<div class="overlay-links <?if(empty($arImage["HREF"])):?>single<?endif?>">
					<?if(!empty($arImage["HREF"])):?><a href="<?=$arImage["HREF"]?>"><i class="fa fa-link"></i></a><?endif?>
					<a href="<?=$arImage["DETAIL"]["SRC"]?>" class="popup-img-bottom-<?=$arResult["ID"]?>" title="<?=$arImage["DESCRIPTION"]?>"><i class="fa fa-search-plus"></i></a>
				</div>
			</div>
		</div>
	</div>
	<?endforeach?>
</div>
<?endif?>