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
<div class="api-category-list">
<? if($arResult['COUNT_ITEMS'] && $arResult['CATEGORIES']): ?>
	<? foreach($arResult['CATEGORIES'] as $category_id => $arCategory): ?>
		<? if($arCategory['ITEMS']): ?>
			<div class="api-category">
				<? if($arCategory['TITLE']): ?>
					<span class="api-category-title"><?=$arCategory['TITLE']?></span>
				<? endif ?>
				<div class="api-items">
					<? foreach($arCategory['ITEMS'] as $i => $arItem): ?>
						<div class="api-item">
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="api-item-link">
								<? if($arParams['PICTURE']): ?>
									<span class="api-item-picture">
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
										</span>
								<? endif ?>
								<span class="api-item-info"<? if($arResult['MARGIN_LEFT']): ?> style="margin-left:<?=($arResult['MARGIN_LEFT'])?>px"<? endif ?>>
										<span class="api-item-name"><?=$arItem['FAKE_NAME']?></span>
									<? if($minPrice = $arItem['MIN_PRICE']): ?>
										<span class="api-item-prices">
													<span class="api-item-price">
														<?
														echo GetMessage(
															($arParams['PRICE_EXT'] ? 'API_SEARCH_TITLE_PRICE_EXT_MODE' : 'API_SEARCH_TITLE_PRICE_SIMPLE_MODE'),
															array(
																'#PRICE#'   => $minPrice['PRINT_DISCOUNT_VALUE'] . $arParams['CURRENCY_SYMBOL'],
																'#MEASURE#' => GetMessage(
																	($arParams['PRICE_EXT'] ? 'API_SEARCH_TITLE_MEASURE_EXT_MODE' : 'API_SEARCH_TITLE_MEASURE_SIMPLE_MODE'),
																	array(
																		'#VALUE#' => $minPrice['CATALOG_MEASURE_RATIO'],
																		'#UNIT#'  => $minPrice['CATALOG_MEASURE_NAME'],
																	)
																),
															)
														);
														?>
													</span>
												</span>
									<? elseif($arItem['PRICES']): ?>
										<? foreach($arItem['PRICES'] as $code => $arPrice): ?>
											<? if($arPrice['CAN_ACCESS']): ?>
												<span class="api-item-prices"><? //=$arResult['PRICES'][$code]['TITLE'];?>
													<? if($arPrice['DISCOUNT_VALUE'] < $arPrice['VALUE']): ?>
														<span class="api-item-price"><?=$arPrice['PRINT_DISCOUNT_VALUE'] . $arParams['CURRENCY_SYMBOL']?></span>
														<span class="api-item-discount"><?=$arPrice['PRINT_VALUE'] . $arParams['CURRENCY_SYMBOL']?></span>
													<? else: ?>
														<span class="api-item-price"><?=$arPrice['PRINT_VALUE'] . $arParams['CURRENCY_SYMBOL']?></span>
													<? endif; ?>
													</span>
											<? endif; ?>
										<? endforeach; ?>
									<? endif; ?>
									<? if($arItem['PROPERTY']): ?>
										<span class="api-item-props">
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
											</span>
									<? endif ?>
									</span>
							</a>
						</div>
					<? endforeach; ?>
				</div>
			</div>
		<? endif ?>
	<? endforeach; ?>
	<? if($arParams['RESULT_URL'] && $arResult['COUNT_ITEMS'] >= $arParams['ITEMS_LIMIT']): ?>
		<div class="api-result-url">
			<a href="<?=$arParams['RESULT_URL']?>"><?=$arParams['RESULT_URL_TEXT']?></a>
		</div>
	<? endif; ?>
<? else: ?>
	<div class="api-not-found"><?=$arParams['RESULT_NOT_FOUND']?></div>
<? endif; ?>
</div>
