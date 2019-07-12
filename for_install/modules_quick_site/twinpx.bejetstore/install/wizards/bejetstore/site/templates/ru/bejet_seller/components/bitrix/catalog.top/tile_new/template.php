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

CJSCore::Init(array("fx"));
$randID = $this->randString();
$strContID = 'bx_catalog_slider_'.$randID;
$itemsCount = count($arResult["ITEMS"]);
$arRowIDs = array();
$boolFirst = true;
$strContWidth = 100*$itemsCount;
$strItemWidth = 100/$itemsCount;
$bFirst = true;
$bOpen = false;
//echo "<pre>";print_r($arResult["ITEMS"]);echo "</pre>";
?>
<div class="tab-pane" id="new-0">
	<?foreach($arResult["ITEMS"] as $key => $arItem):
	$strTitle = (
		isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && '' != isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])
		? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
		: $arItem['NAME']
	);?>
	<?if($key % 3 == 0):$bOpen = true;?>
		<div class="item<?=($key == 0 ? ' active' : '')?>">
			<div class="row">
	<?endif;?>
				<div class="col-sm-4">
					<div class="bj-product-card">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="bj-product-card__img" title="<? echo $strTitle; ?>">
						<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" class="img-responsive">
						</a>
						<div class="bj-product-card__title bj-table">
							<div class="bj-table-row">
								<div href="" class="bj-table-cell">
									<span class="bj-product-card__title__wrapper">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<? echo $strTitle; ?>">
									<?=$arItem["NAME"]?>
									</a>
									</span>
								</div>
							</div>
						</div>
						<div class="row">
						<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):?>
							<div class="col-xs-6 bj-product-card__price bj-price text-large"><? echo GetMessage('CATALOG_FROM'); ?> <?=$arItem["PRINT_MIN_OFFER_PRICE"]?></div>
						<?else:?>
							<?
							if (isset($arItem['MIN_PRICE']) && !empty($arItem['MIN_PRICE']))
							{
								if ($arItem['MIN_PRICE']["DISCOUNT_VALUE"] < $arItem['MIN_PRICE']["VALUE"]):?>
									<div class="col-xs-6 bj-product-card__price bj-price">
										<div class="text-large text-info"><?=$arItem['MIN_PRICE']["PRINT_DISCOUNT_VALUE"]?></div>
										<div class="text-small"><s><?=$arItem['MIN_PRICE']["PRINT_VALUE"]?></s></div>
									</div>
								<?else:?>
									<div class="col-xs-6 bj-product-card__price bj-price text-large"><?=$arItem['MIN_PRICE']["PRINT_VALUE"]?></div>
								<?endif;
							}
							else
							{
								//foreach($arItem["PRICES"] as $priceCode=>$arPrices):
								$arPrices = array_shift($arItem["PRICES"]);?>
								<?if ($arPrices["DISCOUNT_VALUE"] < $arPrices["VALUE"]):?>
									<div class="col-xs-6 bj-product-card__price bj-price">
										<div class="text-large text-info"><?=$arPrices["PRINT_DISCOUNT_VALUE"]?></div>
										<div class="text-small"><s><?=$arPrices["PRINT_VALUE"]?></s></div>
									</div>
								<?else:?>
									<div class="col-xs-6 bj-product-card__price bj-price text-large"><?=$arPrices["PRINT_VALUE"]?></div>
								<?endif?>
								<?//endforeach;
							}
						endif?>
							<?if ($arItem['CAN_BUY'])
							{
				?>
							<div class="col-xs-6">
								<a id="<? echo $arItemIDs['BUY_LINK']; ?>" href="javascript:void(0)" class="btn btn-default" rel="nofollow">
								<?echo ('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCT_TPL_MESS_BTN_BUY'));?>
								</a>
							</div>
				<?
							}
							else
							{
				?>
							<div class="col-xs-6">
								<span class="btn btn-default">
								<?echo ('' != $arParams['MESS_NOT_AVAILABLE'] ? $arParams['MESS_NOT_AVAILABLE'] : GetMessage('CT_BCT_TPL_MESS_PRODUCT_NOT_AVAILABLE'));?>
								</span>
							</div>
				<?
							}?>
						</div>
					</div>
				</div>
	<?if($key % 3 == 2):$bOpen = false;?>
			</div>
		</div>
	<?endif;?>
	<?endforeach;?>
	<?if($bOpen):?>
		</div>
	</div>
	<?endif;?>
</div>