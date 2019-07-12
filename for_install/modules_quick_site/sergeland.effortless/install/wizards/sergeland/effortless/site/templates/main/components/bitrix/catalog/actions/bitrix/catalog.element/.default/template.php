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
			tCounter : "%curr% <?=GetMessage("SERGELAND_EFFORTLESS_OF")?> %total%"
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
<?if( !empty($arResult["DISPLAY_ACTIVE_FROM"]) || !empty($arResult["DISPLAY_ACTIVE_TO"]) ):?>
<div class="tags mb-10">
	<span class="badge transparent-bg"><?if(!empty($arResult["DISPLAY_ACTIVE_FROM"])):?><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_FROM")?> <?=$arResult["DISPLAY_ACTIVE_FROM"]?><?endif?> <?if(!empty($arResult["DISPLAY_ACTIVE_TO"])):?><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_TO")?> <?=$arResult["DISPLAY_ACTIVE_TO"]?><?endif?></span>
</div>
<?endif?>
<h1 class="page-title mb-15"><?=$arResult["NAME"]?></h1>
<?=$arResult["DETAIL_TEXT"];?>
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
				<?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_BEFORESEND")?>
			</div>
			<div class="alert alert-danger" id="error-callback">
				<?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_ERROR")?>
			</div> 
			<div class="alert alert-success" id="success-callback">
				<?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_SUCCESS")?>
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
				<input type="text" name="CALLBACK[NAME]" placeholder="<?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_NAME")?>" class="form-control req">
				<i class="fa fa-user form-control-feedback"></i>
			</div>
		</div>
		<div class="col-md-4 col-sm-6">
			<div class="form-group has-feedback">
				<input type="tel" name="CALLBACK[PHONE]" placeholder="<?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_PHONE")?>" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}" class="form-control req">
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
<?if(!empty($arResult["PROPERTIES"]["REVIEWS"]["ITEMS"])):?>
<div class="<?=$arResult["PROPERTIES"]["REVIEWS_COLOR_BG"]["VALUE"]?> row pt-15 pb-15 mt-30 mb-30">
	<div class="owl-carousel <?=$arResult["PROPERTIES"]["REVIEWS_VER"]["VALUE"]?>">
	<?foreach($arResult["PROPERTIES"]["REVIEWS"]["ITEMS"] as $arItem):?>
		<div class="testimonial">
			<div class="col-md-12">
				<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>" class="popup-img-single"><div class="<?if(!empty($arResult["PROPERTIES"]["REVIEWS_CIRCLE_IMG"]["VALUE"])):?>img-circle<?endif?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"></div></a>
				</div>
				<div class="col-md-10 col-sm-10 col-xs-12">
				<?else:?>
				<div class="col-md-12">
				<?endif?>
					<blockquote>
						<p><?=$arItem["PREVIEW_TEXT"]?></p>
					</blockquote>
					<?if(!empty($arItem["PROPERTIES"]["FIO"]["VALUE"])):?><div class="testimonial-info-1 text-right">- <?=$arItem["PROPERTIES"]["FIO"]["VALUE"]?></div><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["POSITION"]["VALUE"])):?><div class="testimonial-info-2 text-right"><?=$arItem["PROPERTIES"]["POSITION"]["VALUE"]?></div><?endif?>
				</div>
			</div>
		</div>
	<?endforeach?>
	</div>
</div>
<?endif?>
<?if(!empty($arResult["PROPERTIES"]["PHOTO_BOTTOM"]["ITEMS"])):?>
<script>
jQuery(function(){
	$(".popup-img-bottom-<?=$arResult["ID"]?>").magnificPopup({
		type:"image",
		gallery: {
			enabled: true,
			tCounter : "%curr% <?=GetMessage("SERGELAND_EFFORTLESS_OF")?> %total%"
		}		
	});
});
</script>
<div class="owl-carousel <?if(!empty($arResult["PROPERTIES"]["PHOTO_BOTTOM_AUTOPLAY"]["VALUE"])):?>content-slider-with-controls-bottom-autoplay-items-3<?else:?>content-slider-with-controls-bottom-items-3<?endif?> photo-block mt-40 mb-30">
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
<?if(!empty($arResult["PROPERTIES"]["MORE_SERVICES"]["ITEMS"])):?>
<h2 class="underline mt-30"><?=$arResult["PROPERTIES"]["MORE_SERVICES_HEADER"]["~VALUE"]?></h2>
<div class="owl-carousel carousel-items-1 catalog-list">
	<?foreach($arResult["PROPERTIES"]["MORE_SERVICES"]["ITEMS"] as $arItem):?>
	<div class="listing-item">
		<?if(!empty($arItem["PROPERTIES"]["PREVIEW_VIDEO"]["VALUE"]) && !empty($arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"])):?>
		<div class="col-sm-6 col-md-4">
			<div class="overlay-container pic">
				<div class="embed-responsive embed-responsive-4by3">
					<iframe class="embed-responsive-item" src="<?=$arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"]?>?rel=0&showinfo=0&color=white&html5=1" allowfullscreen></iframe>
				</div>
				<div class="mb-15 hidden-sm hidden-md hidden-lg"></div>
			</div>
		</div>
		<div class="col-sm-6 col-md-8">
			<div class="overlay-container">
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_DETAIL")?></a>
			</div>
		</div>
		<?elseif(is_array($arItem["PREVIEW_PICTURE"])):?>
		<div class="col-sm-6 col-md-4">
			<div class="overlay-container pic">
				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
				<div class="overlay">
					<div class="overlay-links">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-link"></i></a>
						<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["NAME"]?>" class="popup-img-single"><i class="fa fa-search-plus"></i></a>
					</div>
				</div>
			</div>
			<div class="mb-15 hidden-sm hidden-md hidden-lg"></div>
		</div>
		<div class="col-sm-6 col-md-8">
			<div class="overlay-container">
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_DETAIL")?></a>
			</div>
		</div>			
		<?else:?>
		<div class="col-md-12">
			<div class="overlay-container">
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-block text-right"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_DETAIL")?></a>
			</div>
		</div>
		<?endif?>
	</div>
	<?endforeach?>
</div>
<?endif?>
<?if(!empty($arResult["PROPERTIES"]["MORE_LICENSE"]["ITEMS"])):?>
<h2 class="underline mt-30"><?=$arResult["PROPERTIES"]["MORE_LICENSE_HEADER"]["~VALUE"]?></h2>
<div class="owl-carousel carousel-items-1 catalog-list">
	<?foreach($arResult["PROPERTIES"]["MORE_LICENSE"]["ITEMS"] as $arItem):?>
	<div class="listing-item">
		<div class="col-sm-6 col-md-4">
			<div class="overlay-container pic">
			<?if(!empty($arItem["PREVIEW_PICTURE"]["SRC"])):?>
				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>">
				<a href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" class="popup-img-single overlay" title="<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>"><i class="fa fa-search-plus"></i></a>
			<?else:?>
				<a href="#"><i class="fa fa-spinner pic"></i></a>
			<?endif?>
			</div>
		</div>
		<div class="col-sm-6 col-md-8">
			<div class="overlay-container">
				<h3 class="page-title"><a href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" class="popup-img-single" title="<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
			</div>
		</div>
	</div>
	<?endforeach?>
</div>
<?endif?>
<?if(!empty($arResult["PROPERTIES"]["MORE_STAFF"]["ITEMS"])):?>
<h2 class="underline mt-30"><?=$arResult["PROPERTIES"]["MORE_STAFF_HEADER"]["~VALUE"]?></h2>
<div class="owl-carousel carousel-items-1 catalog-list">
	<?foreach($arResult["PROPERTIES"]["MORE_STAFF"]["ITEMS"] as $arItem):?>
	<div class="listing-item">
		<div class="team-member">
			<div class="col-sm-6 col-md-4">
				<div class="overlay-container pic">
				<?if(!empty($arItem["PREVIEW_PICTURE"]["SRC"])):?>
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="overlay small">
						<i class="fa fa-plus"></i>
					</a>
				<?else:?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-spinner pic"></i></a>
				<?endif?>
				</div>
			</div>
			<div class="col-sm-6 col-md-8">
				<div class="overlay-container">
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<span class="badge default-bg"><?=$arItem["PROPERTIES"]["POSITION"]["VALUE"]?></span>
					<ul class="list-unstyled team">
						<?if(!empty($arItem["PROPERTIES"]["EMAIL"]["VALUE"])):?><li><i class="fa fa-envelope-o"></i> <a href="<?=$arItem["PROPERTIES"]["EMAIL"]["VALUE"]?>"><?=$arItem["PROPERTIES"]["EMAIL"]["VALUE"]?></a></li><?endif?>
						<?if(!empty($arItem["PROPERTIES"]["PHONE"]["VALUE"])):?><li><i class="fa fa-phone"></i> <?=$arItem["PROPERTIES"]["PHONE"]["VALUE"]?></li><?endif?>
						<?if(!empty($arItem["PROPERTIES"]["SKYPE"]["VALUE"])):?><li><i class="fa fa-skype"></i> <?=$arItem["PROPERTIES"]["SKYPE"]["VALUE"]?></li><?endif?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?endforeach?>						
</div>
<?endif?>
<?if(!empty($arResult["PROPERTIES"]["MORE_PRODUCTS"]["ITEMS"])):?>
<h2 class="underline mt-30"><?=$arResult["PROPERTIES"]["MORE_PRODUCTS_HEADER"]["~VALUE"]?></h2>
<div class="owl-carousel carousel-items-1 catalog-list">
<?foreach($arResult["PROPERTIES"]["MORE_PRODUCTS"]["ITEMS"] as $cell=>$arItem):?>
	<div class="listing-item">
		<div class="col-lg-3 col-md-4 col-sm-4">
			<div class="overlay-container pic">
				<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="overlay small">
						<i class="fa fa-plus"></i>
					</a>
				<?else:?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-image pic"></i></a>
				<?endif?>
			</div>
			<hr class="hidden-sm hidden-md hidden-lg pic">
		</div>
		<div class="col-lg-9 col-md-8 col-sm-8">
			<div class="overlay-container">
				<div class="tags">
					<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"])):?><span class="badge default-bg"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_ACTION")?> <?if(!empty($arItem["PROPERTIES"]["PERCENT"]["VALUE"])):?><?=$arItem["PROPERTIES"]["PERCENT"]["VALUE"]?><?endif?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?><span class="badge danger-bg"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_NEW")?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["PRESENCE"]["VALUE"])):?><span class="badge success-bg"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_PRESENCE")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["EXPECTED"]["VALUE"])):?><span class="badge warning-bg"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_EXPECTED")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNDER"]["VALUE"])):?><span class="badge info-bg"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_UNDER")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNAVAILABLE"]["VALUE"])):?><span class="badge gray-bg"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_UNAVAILABLE")?></span><?endif?>
				</div>
				<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h4>
				<?=$arItem["PREVIEW_TEXT"]?>
				<hr>
				<div class="clearfix">
					<?if(!empty($arItem["PROPERTIES"]["PRICE"]["VALUE"])):?><span class="price"><?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?></span> <?=$arItem["PROPERTIES"]["CURRENCY"]["~VALUE"]?> <?if(!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?><del><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></del><?endif?><?endif?>
					
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-default btn-sm hidden-xs pull-right"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_DETAIL")?></a>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-white btn-sm hidden-xs pull-right"><i class="fa fa-shopping-cart pr-10"></i> <?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_BUY")?></a>
					
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-white hidden-sm hidden-md hidden-lg pull-right"><i class="fa fa-shopping-cart"></i></a>
				</div>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<?endif?>