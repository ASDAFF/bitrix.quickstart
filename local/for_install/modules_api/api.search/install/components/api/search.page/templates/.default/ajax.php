<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */
?>
<? if($arParams['DISPLAY_TOP_PAGER'] && strlen($arResult['NAV_STRING'])): ?>
	<div class="api-pagination"><?=$arResult['NAV_STRING']?></div>
<? endif; ?>
<? if($arResult['ITEMS']): ?>
	<div class="api-count-result"><?=$arResult['COUNT_RESULT']?></div>
<? endif; ?>
<ul class="api-list">
	<? if($arResult['ITEMS']): ?>
		<? foreach($arResult['ITEMS'] as $i => $arItem): ?>
			<li class="api-item">
				<? if($arParams['PICTURE']): ?>
					<div class="api-item-picture" style="<? if($arResult['MARGIN_LEFT']): ?>width:<?=($arResult['MARGIN_LEFT'])?>px;<? endif ?>">
						<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
							<? if($arItem['PICTURE']): ?>
								<img src="<?=$arItem['PICTURE']['SRC']?>"
								     width="<?=$arItem['PICTURE']['WIDTH']?>"
								     height="<?=$arItem['PICTURE']['HEIGHT']?>"
								     alt="<?=$arItem['NAME']?>">
							<? else: ?>
								<img src="<?=$arResult['DEFAULT_PICTURE']['SRC']?>"
								     width="<?=$arResult['DEFAULT_PICTURE']['WIDTH']?>"
								     height="<?=$arResult['DEFAULT_PICTURE']['HEIGHT']?>"
								     alt="<?=$arItem['NAME']?>">
							<? endif ?>
						</a>
					</div>
				<? endif ?>

				<? if($arItem['BRAND'] || $arItem['MIN_PRICE'] || $arItem['PRICES'] || $arParams['MORE_BUTTON_TEXT']): ?>
					<div class="api-item-block-right">
						<? if($arBrand = $arItem['BRAND']): ?>
							<? if($arBrand['PICTURE']): ?>
								<div class="api-item-brand">
									<span class="api-item-brand-img">
										<? if($arBrand['DETAIL_PAGE_URL']): ?>
											<a href="<?=$arBrand['DETAIL_PAGE_URL']?>"><img src="<?=$arBrand['PICTURE']['SRC']?>" alt="<?=$arBrand['NAME']?>" title="<?=$arBrand['NAME']?>"></a>
										<? else: ?>
											<img src="<?=$arBrand['PICTURE']['SRC']?>" alt="<?=$arBrand['NAME']?>" title="<?=$arBrand['NAME']?>">
										<? endif ?>
									</span>
								</div>
							<? endif ?>
						<? endif ?>

						<? if($minPrice = $arItem['MIN_PRICE']): ?>
							<div class="api-item-prices">
								<span class="api-item-price">
								<?
								echo GetMessage(
									($arParams['PRICE_EXT'] ? 'API_SEARCH_PAGE_PRICE_EXT_MODE' : 'API_SEARCH_PAGE_PRICE_SIMPLE_MODE'),
									array(
										'#PRICE#'   => $minPrice['PRINT_DISCOUNT_VALUE'] . $arParams['CURRENCY_SYMBOL'],
										'#MEASURE#' => GetMessage(
											($arParams['PRICE_EXT'] ? 'API_SEARCH_PAGE_MEASURE_EXT_MODE' : 'API_SEARCH_PAGE_MEASURE_SIMPLE_MODE'),
											array(
												'#VALUE#' => $minPrice['CATALOG_MEASURE_RATIO'],
												'#UNIT#'  => $minPrice['CATALOG_MEASURE_NAME'],
											)
										),
									)
								);
								?>
								</span>
							</div>
						<? elseif($arItem['PRICES']): ?>
							<? foreach($arItem['PRICES'] as $code => $arPrice): ?>
								<? if($arPrice['CAN_ACCESS']): ?>
									<div class="api-item-prices"><? //=$arResult['PRICES'][$code]['TITLE'];?>
										<? if($arPrice['DISCOUNT_VALUE'] < $arPrice['VALUE']): ?>
											<span class="api-item-price"><?=$arPrice['PRINT_DISCOUNT_VALUE'] . $arParams['CURRENCY_SYMBOL']?></span>
											<span class="api-item-discount"><?=$arPrice['PRINT_VALUE'] . $arParams['CURRENCY_SYMBOL']?></span>
										<? else: ?>
											<span class="api-item-price"><?=$arPrice['PRINT_VALUE'] . $arParams['CURRENCY_SYMBOL']?></span>
										<? endif; ?>
									</div>
								<? endif; ?>
							<? endforeach; ?>
						<? endif; ?>
						<? if($arParams['MORE_BUTTON_TEXT']): ?>
							<div class="api-item-more-button">
								<!--noindex-->
								<a rel="nofollow" href="<?=$arItem['DETAIL_PAGE_URL']?>" class="<?=$arParams['MORE_BUTTON_CLASS']?>"><?=$arParams['MORE_BUTTON_TEXT']?></a>
								<!--/noindex-->
							</div>
						<? endif; ?>
					</div>
				<? endif ?>

				<div class="api-item-info" style="<? if($arResult['MARGIN_LEFT']): ?>margin-left:<?=($arResult['MARGIN_LEFT'])?>px;<? endif ?>">
					<div class="api-item-name">
						<a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['FAKE_NAME']?></a>
					</div>
					<? if($arSection = $arItem['SECTION']): ?>
						<div class="api-item-section">
							<a href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a>
						</div>
					<? endif ?>
					<? if($arItem['PROPERTY']): ?>
						<div class="api-item-props">
							<? foreach($arItem['PROPERTY'] as $arProp): ?>
								<? if($arProp['FAKE_VALUE']): ?>
									<span class="api-item-prop">
										<? if($arProp['NAME']): ?>
											<span class="api-item-prop-name"><?=$arProp['NAME']?></span>
										<? endif ?>
										<? if($arProp['FAKE_VALUE']): ?>
											<span class="api-item-prop-value"><?=$arProp['FAKE_VALUE']?></span>
										<? endif ?>
									</span>
								<? endif ?>
							<? endforeach; ?>
						</div>
					<? endif ?>
					<? if($arItem['DESCRIPTION']): ?>
						<div class="api-item-desc"><?=$arItem['DESCRIPTION']?></div>
					<? endif ?>
				</div>
			</li>
		<? endforeach; ?>
	<? elseif(is_set($_REQUEST['q']) || $arResult['isAjax']): ?>
		<li class="api-not-found"><?=$arParams['RESULT_NOT_FOUND']?></li>
	<? endif; ?>
</ul>
<? if($arParams['DISPLAY_BOTTOM_PAGER'] && strlen($arResult['NAV_STRING'])): ?>
	<div class="api-pagination"><?=$arResult['NAV_STRING']?></div>
<? endif; ?>
