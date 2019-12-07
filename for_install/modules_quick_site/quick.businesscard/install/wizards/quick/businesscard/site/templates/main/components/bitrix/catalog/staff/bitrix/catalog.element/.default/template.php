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
<div class="row">
	<div class="col-sm-<?if(!empty($arResult["PREVIEW_PICTURE"]["SRC"])):?>7<?else:?>12<?endif?> team-member mb-20">
		<h2 class="page-title"><?=$arResult["NAME"]?></h2>
		<?if(!empty($arResult["PROPERTIES"]["POSITION"]["VALUE"])):?><span class="badge default-bg mb-15"><?=$arResult["PROPERTIES"]["POSITION"]["VALUE"]?></span><?endif?>
		<p><?=$arResult["PREVIEW_TEXT"]?></p>
		<?if(!empty($arResult["PREVIEW_PICTURE"]["SRC"])):?><hr><?endif?>
		<ul class="list-unstyled team no-underline">
			<?if(!empty($arResult["PROPERTIES"]["EMAIL"]["VALUE"])):?><li><i class="fa fa-envelope-o"></i> <a href="mailto:<?=$arResult["PROPERTIES"]["EMAIL"]["VALUE"]?>"><?=$arResult["PROPERTIES"]["EMAIL"]["VALUE"]?></a></li><?endif?>
			<?if(!empty($arResult["PROPERTIES"]["PHONE"]["VALUE"])):?><li><i class="fa fa-phone"></i> <?=$arResult["PROPERTIES"]["PHONE"]["VALUE"]?></li><?endif?>
			<?if(!empty($arResult["PROPERTIES"]["SKYPE"]["VALUE"])):?><li><i class="fa fa-skype"></i> <?=$arResult["PROPERTIES"]["SKYPE"]["VALUE"]?></li><?endif?>
		</ul>
	</div>
	<?if(!empty($arResult["PREVIEW_PICTURE"]["SRC"])):?>
	<div class="col-sm-5">
		<img alt="<?=$strAlt?>" class="img-responsive mb-20" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>">
	</div>
	<?endif?>
</div>
<?if(!empty($arResult["PROPERTIES"]["SHOW_CALLBACK_FORM"]["VALUE"])):?>
<div class="gray-bg row mt-35 mb-30 pt-15">
	<div class="col-md-12">
		<div id="results-callback">
			<div class="alert alert-danger" id="beforesend-callback">
				<?=GetMessage("QUICK_BUSINESSCARD_STAFF_BEFORESEND")?>
			</div>
			<div class="alert alert-danger" id="error-callback">
				<?=GetMessage("QUICK_BUSINESSCARD_STAFF_ERROR")?>
			</div> 
			<div class="alert alert-success" id="success-callback">
				<?=GetMessage("QUICK_BUSINESSCARD_STAFF_SUCCESS")?>
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
				<input type="text" name="CALLBACK[NAME]" placeholder="<?=GetMessage("QUICK_BUSINESSCARD_STAFF_NAME")?>" class="form-control req">
				<i class="fa fa-user form-control-feedback"></i>
			</div>
		</div>
		<div class="col-md-4 col-sm-6">
			<div class="form-group has-feedback">
				<input type="tel" name="CALLBACK[PHONE]" placeholder="<?=GetMessage("QUICK_BUSINESSCARD_STAFF_PHONE")?>" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}" class="form-control req">
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
			tCounter : "%curr% <?=GetMessage("QUICK_BUSINESSCARD_OF")?> %total%"
		}		
	});
});
</script>
<div class="owl-carousel <?if(!empty($arResult["PROPERTIES"]["PHOTO_BOTTOM_AUTOPLAY"]["VALUE"])):?>content-slider-with-controls-bottom-autoplay-items-3<?else:?>content-slider-with-controls-bottom-items-3<?endif?> photo-block mt-30 mb-30">
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
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
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
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
			</div>
		</div>			
		<?else:?>
		<div class="col-md-12">
			<div class="overlay-container">
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-block text-right"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
			</div>
		</div>
		<?endif?>
	</div>
	<?endforeach?>
</div>
<?endif?>
<?if(!empty($arResult["PROPERTIES"]["MORE_ARTICLES"]["ITEMS"])):?>
<h2 class="underline mt-30"><?=$arResult["PROPERTIES"]["MORE_ARTICLES_HEADER"]["~VALUE"]?></h2>
<div class="owl-carousel carousel-items-1 catalog-list">
	<?foreach($arResult["PROPERTIES"]["MORE_ARTICLES"]["ITEMS"] as $arItem):?>
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
				<?if($arItem["DISPLAY_ACTIVE_FROM"]):?>
				<div class="tags mb-10">
					<span class="badge transparent-bg"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
				</div>
				<?endif?>
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
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
				<?if($arItem["DISPLAY_ACTIVE_FROM"]):?>
				<div class="tags mb-10">
					<span class="badge transparent-bg"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
				</div>
				<?endif?>
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
			</div>
		</div>			
		<?else:?>
		<div class="col-md-12">
			<div class="overlay-container">
				<?if($arItem["DISPLAY_ACTIVE_FROM"]):?>
				<div class="tags mb-10">
					<span class="badge transparent-bg"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
				</div>
				<?endif?>
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-block text-right"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
			</div>
		</div>
		<?endif?>
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
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
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
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
			</div>
		</div>			
		<?else:?>
		<div class="col-md-12">
			<div class="overlay-container">
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-block text-right"><?=GetMessage("QUICK_BUSINESSCARD_STAFF_DETAIL")?></a>
			</div>
		</div>
		<?endif?>
	</div>
	<?endforeach?>
</div>
<?endif?>