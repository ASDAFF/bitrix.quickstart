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

$strTitle = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	: $arResult['NAME']
);
$strAlt = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	: $arResult['NAME']
);
?>
<div class="works-detail">
	<div class="row">
		<div class="col-md-12">
			<h1><?=$arResult["NAME"]?></h1>
		</div>
		<div class="col-md-<?if(!empty($arResult["PROPERTIES"]["SHOW_CALLBACK_FORM"]["VALUE"])):?>7<?else:?>12<?endif?>">
			<ul class="nav nav-pills white" role="tablist">
				<li <?if(!empty($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"]) || (empty($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"]) && empty($arResult["PROPERTIES"]["HREF_VIDEO"]["VALUE"]))):?>class="active"<?endif?>><a href="#images" role="tab" data-toggle="tab" title="images"><i class="fa fa-camera pr-5"></i> <?=GetMessage("SERGELAND_EFFORTLESS_TAB_PHOTO")?></a></li>
				<li <?if(!empty($arResult["PROPERTIES"]["HREF_VIDEO"]["VALUE"]) && empty($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"])):?>class="active"<?endif?>><a href="#video" role="tab" data-toggle="tab" title="video"><i class="fa fa-video-camera pr-5"></i> <?=GetMessage("SERGELAND_EFFORTLESS_TAB_VIDEO")?></a></li>
			</ul>
			<div class="tab-content clear-style">
				<div class="tab-pane <?if(!empty($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"]) || (empty($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"]) && empty($arResult["PROPERTIES"]["HREF_VIDEO"]["VALUE"]))):?>active<?endif?>" id="images">
					<?if(!empty($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"])):?>
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
					<div class="owl-carousel <?if(!empty($arResult["PROPERTIES"]["PHOTO_TOP_AUTOPLAY"]["VALUE"])):?>content-slider-with-controls-autoplay<?else:?>content-slider-with-controls<?endif?> owl-theme photo-block">
						<?foreach($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"] as $arImage):?>
						<div class="overlay-container">
							<img src="<?=$arImage["PREVIEW"]["SRC"]?>" alt="<?=$arImage["DESCRIPTION"]?>">
							<a href="<?=$arImage["DETAIL"]["SRC"]?>" class="popup-img-top-<?=$arResult["ID"]?> overlay" title="<?=$arImage["DESCRIPTION"]?>"><i class="fa fa-search-plus"></i></a>
						</div>
						<?endforeach?>
					</div>
					<?else:?>
						<i class="fa fa-image pic"></i>
					<?endif?>
				</div>
				<div class="tab-pane <?if(!empty($arResult["PROPERTIES"]["HREF_VIDEO"]["VALUE"]) && empty($arResult["PROPERTIES"]["PHOTO_TOP"]["ITEMS"])):?>active<?endif?>" id="video">
					<?if(!empty($arResult["PROPERTIES"]["HREF_VIDEO"]["VALUE"])):?>
					<div class="embed-responsive embed-responsive-16by9 mb-40">
						<iframe class="embed-responsive-item" src="<?=$arResult["PROPERTIES"]["HREF_VIDEO"]["VALUE"]?>?rel=0&showinfo=0&color=white&html5=1" allowfullscreen></iframe>
					</div>
					<?else:?>
						<i class="fa fa-youtube pic"></i>
					<?endif?>
				</div>
			</div>
		</div>
		<?if(!empty($arResult["PROPERTIES"]["SHOW_CALLBACK_FORM"]["VALUE"])):?>
		<div class="col-md-5">
			<p class="info mt-45"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_INFO")?></p>
			<div id="results-callback">
				<div class="alert alert-danger" id="beforesend-callback">
					<?=GetMessage("SERGELAND_EFFORTLESS_WORKS_BEFORESEND")?>
				</div>
				<div class="alert alert-danger" id="error-callback">
					<?=GetMessage("SERGELAND_EFFORTLESS_WORKS_ERROR")?>
				</div> 
				<div class="alert alert-success" id="success-callback">
					<?=GetMessage("SERGELAND_EFFORTLESS_WORKS_SUCCESS")?>
				</div>
			</div>
			<img src="<?=SITE_DIR?>images/loading.gif" alt="Loading" id="form-loading-callback" class="pull-right mb-5" />
			<div class="clearfix"></div>
			<form name="CALLBACK" action="<?=SITE_DIR?>include/" method="POST" role="form">
				<input type="hidden" name="CALLBACK[SITE_ID]" value="<?=SITE_ID?>"/>
				<input type="hidden" name="CALLBACK[TITLE]" value="<?=$arResult["NAME"]?>"/>				
				<div class="form-group has-feedback">
					<input type="text" name="CALLBACK[NAME]" placeholder="<?=GetMessage("SERGELAND_EFFORTLESS_WORKS_NAME")?>" class="form-control req">
					<i class="fa fa-user form-control-feedback"></i>
				</div>
				<div class="form-group has-feedback">
					<input type="tel" name="CALLBACK[PHONE]" placeholder="<?=GetMessage("SERGELAND_EFFORTLESS_WORKS_PHONE")?>" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}" class="form-control req">
					<i class="fa fa-phone form-control-feedback"></i>
				</div>
				<button type="submit" class="btn btn-sm btn-default pull-right"><i class="fa fa-sign-out pr-5"></i> <?=$arResult["PROPERTIES"]["TEXT_CALLBACK_FORM"]["VALUE"]?></button>
			</form>
		</div>
		<?endif?>
	</div>
</div>
<div class="sidebar mt-40">
	<div class="side product-item">
		<div class="tabs-style-2">
			<ul class="nav nav-tabs" role="tablist">
				<li><a href="#descriprion" role="tab" data-toggle="tab"><i class="fa fa-file-text-o"></i><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_TAB_DESCRIPTION")?></a></li>
				<li class="active"><a href="#specifications" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_TAB_SPECIFICATIONS")?></a></li>
				<li><a href="#documents" role="tab" data-toggle="tab"><i class="fa fa-file-word-o"></i><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_TAB_DOCUMENTS")?></a></li>
				<li><a href="#comments" role="tab" data-toggle="tab"><i class="fa fa-star"></i>(<?if(!empty($arResult["PROPERTIES"]["REVIEWS"]["ITEMS"])):?><?=count($arResult["PROPERTIES"]["REVIEWS"]["ITEMS"])?><?else:?>0<?endif?>) <?=GetMessage("SERGELAND_EFFORTLESS_WORKS_TAB_REVIEWS")?></a></li>
			</ul>
			<div class="tab-content padding-top-clear padding-bottom-clear">
				<div class="tab-pane fade" id="descriprion">
					<?=$arResult["DETAIL_TEXT"]?>
				</div>
				<div class="tab-pane fade in active" id="specifications">
				<?if(!empty($arResult["PROPERTIES"]["SPECIFICATION_NAME"]["VALUE"])):?>
					<dl class="dl-horizontal space-top">
					<?foreach($arResult["PROPERTIES"]["SPECIFICATION_NAME"]["VALUE"] as $index=>$NAME):?>
						<dt><?=$NAME?></dt>
						<dd><?=$arResult["PROPERTIES"]["SPECIFICATION_VALUE"]["VALUE"][$index]?></dd>
					<?endforeach?>
					</dl>
				<?endif?>
				</div>
				<div class="tab-pane fade" id="documents">
				<?if(!empty($arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"])):?>
					<div class="space-top doc">
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
				</div>
				<div class="tab-pane fade" id="comments">
				<?if(!empty($arResult["PROPERTIES"]["REVIEWS"]["ITEMS"])):?>
					<div class="comments margin-clear space-top">
						<?foreach($arResult["PROPERTIES"]["REVIEWS"]["ITEMS"] as $arReview):?>
							<div class="comment clearfix">
								<div class="comment-content">
									<h3><?=$arReview["PROPERTIES"]["FIO"]["VALUE"]?></h3>
									<?if(!empty($arReview["PROPERTIES"]["POSITION"]["VALUE"])):?><div class="comment-meta pl-5"><?=$arReview["PROPERTIES"]["POSITION"]["VALUE"]?></div><?endif?>
									<div class="comment-body clearfix">
										<p><?=$arReview["PREVIEW_TEXT"]?></p>
									</div>
								</div>
							</div>
						<?endforeach?>
					</div>
				<?endif?>
				</div>
			</div>
		</div>
	</div>
</div>
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
<div class="pluso pull-right" 
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
<div class="owl-carousel <?if(!empty($arResult["PROPERTIES"]["PHOTO_BOTTOM_AUTOPLAY"]["VALUE"])):?>content-slider-with-controls-bottom-autoplay-items-3<?else:?>content-slider-with-controls-bottom-items-3<?endif?> photo-block mt-50 mb-30">
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
					<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"])):?><span class="badge default-bg"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_ACTION")?> <?if(!empty($arItem["PROPERTIES"]["PERCENT"]["VALUE"])):?><?=$arItem["PROPERTIES"]["PERCENT"]["VALUE"]?><?endif?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?><span class="badge danger-bg"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_NEW")?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["PRESENCE"]["VALUE"])):?><span class="badge success-bg"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_PRESENCE")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["EXPECTED"]["VALUE"])):?><span class="badge warning-bg"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_EXPECTED")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNDER"]["VALUE"])):?><span class="badge info-bg"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_UNDER")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNAVAILABLE"]["VALUE"])):?><span class="badge gray-bg"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_UNAVAILABLE")?></span><?endif?>
				</div>
				<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h4>
				<?=$arItem["PREVIEW_TEXT"]?>
				<hr>
				<div class="clearfix">
					<?if(!empty($arItem["PROPERTIES"]["PRICE"]["VALUE"])):?><span class="price"><?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?></span> <?=$arItem["PROPERTIES"]["CURRENCY"]["~VALUE"]?> <?if(!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?><del><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></del><?endif?><?endif?>
					
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-default btn-sm hidden-xs pull-right"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_DETAIL")?></a>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-white btn-sm hidden-xs pull-right"><i class="fa fa-shopping-cart pr-10"></i> <?=GetMessage("SERGELAND_EFFORTLESS_WORKS_BUY")?></a>
					
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-white hidden-sm hidden-md hidden-lg pull-right"><i class="fa fa-shopping-cart"></i></a>
				</div>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<?endif?>
<?if(!empty($arResult["PROPERTIES"]["MORE_WORKS"]["ITEMS"])):?>
<h2 class="underline mt-30"><?=$arResult["PROPERTIES"]["MORE_WORKS_HEADER"]["~VALUE"]?></h2>
<div class="owl-carousel carousel-items-1 catalog-list">
	<?foreach($arResult["PROPERTIES"]["MORE_WORKS"]["ITEMS"] as $arItem):?>
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
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_DETAIL")?></a>
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
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_DETAIL")?></a>
			</div>
		</div>			
		<?else:?>
		<div class="col-md-12">
			<div class="overlay-container">
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-block text-right"><?=GetMessage("SERGELAND_EFFORTLESS_WORKS_DETAIL")?></a>
			</div>
		</div>
		<?endif?>
	</div>
	<?endforeach?>
</div>
<?endif?>