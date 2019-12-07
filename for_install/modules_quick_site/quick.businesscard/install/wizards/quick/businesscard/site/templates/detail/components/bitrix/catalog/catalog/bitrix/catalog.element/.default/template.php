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
<script>
jQuery(function(){
	$(".popup-img-<?=$arResult["ID"]?>").magnificPopup({
		type:"image",
		gallery: {
			enabled: true,
			tCounter : "%curr% <?=GetMessage("QUICK_BUSINESSCARD_OF")?> %total%"
		}		
	});
});
</script>
<div class="catalog-detail">
	<div class="row">
		<div class="col-md-5">
			<ul class="nav nav-pills white" role="tablist">
				<li class="active"><a href="#images" role="tab" data-toggle="tab" title="images"><i class="fa fa-camera pr-5"></i> <?=GetMessage("QUICK_BUSINESSCARD_TAB_PHOTO")?></a></li>
				<li><a href="#video" role="tab" data-toggle="tab" title="video"><i class="fa fa-video-camera pr-5"></i> <?=GetMessage("QUICK_BUSINESSCARD_TAB_VIDEO")?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="images">
					<?if(!empty($arResult["DETAIL_PICTURE"]["SRC"])):?>
					<div class="owl-carousel <?if(!empty($arResult["PROPERTIES"]["MORE_PHOTO_AUTOPLAY"]["VALUE"])):?>content-slider-with-controls-bottom-autoplay<?else:?>content-slider-with-controls-bottom<?endif?> photo-block mb-20">
						<div class="overlay-container">
							<img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$strAlt?>">
							<a href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" class="popup-img-<?=$arResult["ID"]?> overlay" title="<?=$strAlt?>"><i class="fa fa-search-plus"></i></a>
						</div>
						<?if(!empty($arResult["MORE_PHOTO"])):?>
							<?foreach($arResult["MORE_PHOTO"] as $arPhoto):?>
							<div class="overlay-container">
								<img src="<?=$arPhoto["SRC"]?>" alt="<?=$strAlt?>">
								<a href="<?=$arPhoto["SRC"]?>" class="popup-img-<?=$arResult["ID"]?> overlay" title="<?=$strAlt?>"><i class="fa fa-search-plus"></i></a>
							</div>
							<?endforeach?>
						<?endif?>
					</div>
					<?else:?>
					<i class="fa fa-image pic"></i>
					<?endif?>
				</div>
				<div class="tab-pane" id="video">
					<?if(!empty($arResult["PROPERTIES"]["URL_VIDEO"]["VALUE"])):?>
					<div class="embed-responsive embed-responsive-16by9 mb-40">
						<iframe allowfullscreen src="<?=$arResult["PROPERTIES"]["URL_VIDEO"]["VALUE"]?>?rel=0&showinfo=0&color=white&html5=1" class="embed-responsive-item"></iframe>
					</div>
					<?else:?>
					<i class="fa fa-youtube pic"></i>
					<?endif?>
				</div>
			</div>
		</div>
		<div class="col-md-7">
			<?if(!empty($arResult["PROPERTIES"]["ACTION"]["VALUE"]) || !empty($arResult["PROPERTIES"]["NEW"]["VALUE"])):?>
			<div class="tags">
				<?if(!empty($arResult["PROPERTIES"]["ACTION"]["VALUE"])):?><span class="badge default-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ACTION")?> <?if(!empty($arResult["PROPERTIES"]["PERCENT"]["VALUE"])):?><?=$arResult["PROPERTIES"]["PERCENT"]["VALUE"]?><?endif?></span><?endif?>
				<?if(!empty($arResult["PROPERTIES"]["NEW"]["VALUE"])):?><span class="badge danger-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_NEW")?></span><?endif?>
			</div>
			<?endif?>
			<h1><?=$arResult["NAME"]?></h1>
			<div class="mb-30">
				<?=$arResult["PREVIEW_TEXT"]?>
				<div class="mb-20">
					<?if(!empty($arResult["PROPERTIES"]["PRICE"]["VALUE"])):?><span class="price"><?=$arResult["PROPERTIES"]["PRICE"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["CURRENCY"]["~VALUE"]?> <?if(!empty($arResult["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?><del><?=$arResult["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></del><?endif?><?endif?>
					<?if(!empty($arResult["PROPERTIES"]["PRESENCE"]["VALUE"])):?><span class="badge success-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_PRESENCE")?></span>
					<?elseif(!empty($arResult["PROPERTIES"]["EXPECTED"]["VALUE"])):?><span class="badge warning-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_EXPECTED")?></span>
					<?elseif(!empty($arResult["PROPERTIES"]["UNDER"]["VALUE"])):?><span class="badge info-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_UNDER")?></span>
					<?elseif(!empty($arResult["PROPERTIES"]["UNAVAILABLE"]["VALUE"])):?><span class="badge gray-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_UNAVAILABLE")?></span><?endif?>
				</div>
				<?if(!empty($arResult["PROPERTIES"]["SHOW_ORDER_FORM"]["VALUE"])):?>
				<p class="info"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ORDER")?></p>
				<div id="results-order">
					<div class="alert alert-danger" id="beforesend-order">
						<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ORDER_BEFORESEND")?>
					</div>
					<div class="alert alert-danger" id="error-order">
						<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ORDER_ERROR")?>
					</div>
					<div class="alert alert-success" id="success-order">
						<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ORDER_SUCCESS")?>
					</div>
				</div>
				<img src="<?=SITE_DIR?>images/loading.gif" alt="Loading" id="form-loading-order" class="pull-right mb-5" />
				<div class="clearfix"></div>				
				<form name="ORDER" action="<?=SITE_DIR?>include/" method="POST" role="form">
					<input type="hidden" name="ORDER[SITE_ID]" value="<?=SITE_ID?>"/>
					<input type="hidden" name="ORDER[IBLOCK_ID]" value="<?=$arParams["LINK_ORDER_IBLOCK_ID"]?>"/>
					<input type="hidden" name="ORDER[ID]" value="<?=$arResult["ID"]?>">
					<input type="hidden" name="ORDER[PRODUCT_NAME]" value="<?=$arResult["NAME"]?>">
					<?if(!empty($arResult["PROPERTIES"]["SHOW_NAME"]["VALUE"])):?>
					<div class="form-group has-feedback">
						<input type="text" name="ORDER[NAME]" class="form-control <?if(!empty($arResult["PROPERTIES"]["REQ_NAME"]["VALUE"])):?>req<?endif?>" placeholder="<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ORDER_NAME")?>">
						<i class="fa fa-user form-control-feedback"></i>
					</div>
					<?else:?>
						<input type="hidden" name="ORDER[NAME]" value="-" />
					<?endif?>
					<?if(!empty($arResult["PROPERTIES"]["SHOW_PHONE"]["VALUE"])):?>
					<div class="form-group has-feedback">
						<input type="tel" name="ORDER[PHONE]" class="form-control <?if(!empty($arResult["PROPERTIES"]["REQ_PHONE"]["VALUE"])):?>req<?endif?>" placeholder="<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ORDER_PHONE")?>" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}">
						<i class="fa fa-phone form-control-feedback"></i>
					</div>
					<?else:?>
						<input type="hidden" name="ORDER[PHONE]" value="-" />
					<?endif?>
					<?if(!empty($arResult["PROPERTIES"]["SHOW_EMAIL"]["VALUE"])):?>
					<div class="form-group has-feedback">
						<input type="email" name="ORDER[EMAIL]" class="form-control <?if(!empty($arResult["PROPERTIES"]["REQ_EMAIL"]["VALUE"])):?>req<?endif?>" placeholder="<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ORDER_EMAIL")?>">
						<i class="fa fa-envelope form-control-feedback"></i>
					</div>
					<?else:?>
						<input type="hidden" name="ORDER[EMAIL]" value="-" />
					<?endif?>					
					<?if(!empty($arResult["PROPERTIES"]["SHOW_COMMENT"]["VALUE"])):?>
					<div class="form-group has-feedback">
						<textarea name="ORDER[COMMENT]" class="form-control <?if(!empty($arResult["PROPERTIES"]["REQ_COMMENT"]["VALUE"])):?>req<?endif?>" rows="4" placeholder="<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ORDER_COMMENT")?>"></textarea>
						<i class="fa fa-pencil form-control-feedback"></i>
					</div>
					<?else:?>
						<input type="hidden" name="ORDER[COMMENT]" value="-" />
					<?endif?>
					<button type="submit" class="btn btn-default pull-right"><i class="fa fa-shopping-cart pr-10"></i> <?=$arResult["PROPERTIES"]["TEXT_BUTTON"]["VALUE"]?></button>
				</form>
				<?endif?>
			</div>
		</div>
	</div>
</div>