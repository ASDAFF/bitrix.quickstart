<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	die();
}

$this->setFrameMode(true);

// pictures
$arImages = array();
if( is_array($arResult["DETAIL_PICTURE"]) ) {
	$arImages[] = $arResult['DETAIL_PICTURE'];
}
if(is_array($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE']) && count($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE'])>0) {
	foreach($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE'] as $arImage) {
		$arImages[] = $arImage;
	}
}
?>
<div class="newsdetail imagetop">
	<div class="row">
		<?php if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]): ?>
			<div class="col col-md-12 activefrom"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div>
		<?php endif; ?>
		<div class="col col-md-9 pic">

			<?php if(is_array($arImages) && count($arImages)>0): ?>
				<div class="js-owl owl-carousel owl-theme">
				<?php foreach($arImages as $index => $arImage): ?>
					<div class="pic" data-dot="<img src='<?=$arImage['RESIZE']['src']?>'>">
						<img src="<?=$arImage['SRC']?>"
							 alt="<?=($arImage['ALT']!='' ? $arImage['ALT'] : $arResult['NAME'])?>"
							 title="<?=($arImage['TITLE']!='' ? $arImage['TITLE'] : $arResult['NAME'])?>"
						>
					</div>
				<?php endforeach; ?>
				</div>
			<?php endif;?>
			<div class="owl-dots-wrapper"><div class="js-owl__dots"></div></div>
		</div>
		<div class="col col-md-9 text"><?=$arResult["DETAIL_TEXT"]?></div>
	</div>
</div>
