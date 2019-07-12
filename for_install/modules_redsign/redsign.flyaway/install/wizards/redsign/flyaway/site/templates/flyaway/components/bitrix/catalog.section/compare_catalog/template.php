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
$templateData = $arResult;

if (count($arResult['ELEMENTS']) > 0) {
  ?><ul class="list-unstyled products products_table informer-products"><?
    foreach ($arResult['ITEMS'] as $key => $arItem) {
    	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
		  $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
			
			?><li class="products__item js-elementid<?=$arItem['ID']?> js-compare" data-elementid="<?=$arItem['ID']?>"><?
				?><div class="products__in"><?
					?><div class="row"><?
					
						?><div class="col col-xs-2"><?
							?><div class="products__col products__pic"><?
								?><a class="js-compare-label" href="<?=$arItem['DETAIL_PAGE_URL'];?>"><?
										if (isset($arItem['PREVIEW_PICTURE']['SRC'])) {
											?><img class="products__img" alt="" src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>"><?
										} else {
											?><img class="products__img" alt="" src="<?=SITE_TEMPLATE_PATH?>/images/img/default-img.png"><?
										}
								?></a><?
							?></div><?
						?></div><?
						
						?><div class="col col-xs-10"><?
								?><div class="products__data"><?
									?><div class="col col-xs-8 products__col"><?
										?><div class="row"><?
											?><div class="products__name">
												<a class="products-title js-compare-name" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
											</div><?
											
											if ($arItem['PREVIEW_TEXT'] != ''):
												?><div class="products__description hidden-xs"><?=$arItem['PREVIEW_TEXT']?></div><?
											endif;
										?></div><?
									?></div><?
									?><div class="col col-xs-3 text-center products__col products__col_last"><?
										?><div class="products-box"><?
												?><div class="products-prices"><?
													?><div class="prices"><?
														if (IntVal($arItem['RS_PRICE']['DISCOUNT_DIFF']) > 0):
															?><div class="prices__val prices__val_old hidden-xs"><?=$arItem['RS_PRICE']['PRINT_VALUE']?></div><?
															?><div class="prices__val prices__val_cool prices__val_new"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
														else:
															?><div class="prices__val prices__val_cool"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
														endif;
												?></div><?
											?></div><?
										?></div><?
									?></div><?
									?><div class="col col-xs-1 text-center products__col products__col_last"><?
										?><div class="row">
												<span class="icon-east js-compare-box">
													<a class="js-compare-switcher js-informer-switcher" href="javascript:;">
														<i class="fa fa-trash"></i>
													</a>
													<span class="tooltip tooltip_hidden"><?=GetMessage('DELETE_FROM_COMPARE')?></span>
												</span>
											</div><?
									?></div><?
								?></div><?
						?></div><?
					
					?></div><?
				?></div><?
			?></li><?
    }
	?></ul><?
	?><a class="pull-right btn btn-default btn2 btn2_mod" href="/catalog/compare/"><?=GetMessage('FAVORITE_SECTION')?></a><?
}
