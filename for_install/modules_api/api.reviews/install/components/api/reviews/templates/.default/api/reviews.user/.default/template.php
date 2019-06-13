<?php

use \Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent         $component
 *
 * @var array                    $arParams
 * @var array                    $arResult
 *
 * @var string                   $templateName
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var array                    $templateData
 *
 * @var string                   $componentPath
 * @var string                   $parentTemplateFolder
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

Loc::loadMessages(dirname(__FILE__) . '/template.php');

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}

$pagenavigation = '';
if($arParams['DISPLAY_TOP_PAGER'] || $arParams['DISPLAY_BOTTOM_PAGER']) {
	ob_start();
	$this->addExternalCss('/bitrix/components/bitrix/main.pagenavigation/templates/.default/style.css');
	$APPLICATION->IncludeComponent(
		 'bitrix:main.pagenavigation',
		 '',
		 array(
				'NAV_OBJECT'     => $arResult['NAV_OBJECT'],
				'SEF_MODE'       => $arParams['SEF_MODE'],
				'TEMPLATE_THEME' => $arParams['PAGER_THEME'],
		 ),
		 false,
		 array('HIDE_ICONS' => 'Y')
	);
	$pagenavigation = ob_get_contents();
	ob_end_clean();
}

?>
<? if($arResult['ITEMS']): ?>
	<div class="api_reviews_user aruser_color_<?=$arParams['COLOR']?>">

		<? if($arUser = $arResult['USER']): ?>
			<div class="api_user">
				<div class="api_about">
					<div class="api_picture">
						<div class="api_thumbnail" style="background-image: url('<?=$arUser['SMALL_PICTURE']['SRC']?>')"></div>
					</div>
					<div class="api_info">
						<div class="api_format_name"><?=$arUser['FORMAT_NAME']?></div>
						<? if($arUser['PERSONAL_NOTES']): ?>
							<div class="api_personal_notes"><?=$arUser['PERSONAL_NOTES'];?></div>
						<? endif ?>
					</div>
				</div>
			</div>
		<? endif ?>

		<div class="api-reviews-list arlist-color-<?=$arParams['COLOR']?>">
			<? if($arParams['DISPLAY_TOP_PAGER'] && $pagenavigation): ?>
				<div class="api-pagination"><?=$pagenavigation?></div>
			<? endif; ?>
			<div class="api-items">
				<? foreach($arResult['ITEMS'] as $arItem): ?>
					<?
					$item_class = '';
					$arElement  = $arItem['ELEMENT_FIELDS'];
					?>
					<div id="review<?=$arItem['ID']?>" class="api-item <?=$item_class?>">
						<? if($arItem['STATUS']): ?>
							<div class="api-item-status"><?=$arItem['STATUS']?></div>
						<? endif ?>
						<div class="api-header">
							<div class="api-user-info">
								<? if($arElement): ?>
									<div class="api-guest-picture">
										<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
											<div class="api-prodpic" style="background-image: url('<?=($arElement['PICTURE']['SRC'] ? $arElement['PICTURE']['SRC'] : $arItem['PICTURE'])?>')"></div>
										</a>
									</div>
								<? elseif($arItem['PICTURE']): ?>
									<div class="api-guest-picture">
										<div class="api-userpic" style="background-image: url('<?=$arItem['PICTURE']['SRC']?>')"></div>
									</div>
								<? endif ?>
								<div class="api-guest-info">
									<? if($arItem['GUEST_NAME']): ?>
										<div class="api-guest-name">
											<div itemprop="name"><?=$arItem['GUEST_NAME']?></div>
										</div>
										<div class="api-star-rating">
											<? if($arItem['RATING']): ?>
												<? for($i = 1; $i <= 5; $i++): ?>
													<? $active = ($arItem['RATING'] >= $i) ? '-active' : ''; ?>
													<i class="api-icon-star api-icon<?=$active?>"></i>
												<? endfor ?>
											<? endif ?>
											<span class="api-hidden" itemprop="ratingValue"><?=($arItem['RATING'] ? $arItem['RATING'] : 5)?></span>
										</div>
										<? if($arItem['DISPLAY_ACTIVE_FROM']): ?>
											<div class="api-date" itemprop="datePublished" content="<?=$arItem['DISPLAY_DATE_PUBLISHED']?>"><?=$arItem['DISPLAY_ACTIVE_FROM']?></div>
										<? endif; ?>
										<? if($arItem['ORDER_ID']): ?>
											<div class="api-true-buyer"><?=$arParams['MESS_TRUE_BUYER']?></div>
										<? endif ?>
									<? endif; ?>
								</div>
							</div>
						</div>

						<div class="api-content">
							<div class="api-fields">
								<? if($arItem['TITLE']): ?>
									<div class="api-field api-field-title" data-edit="TITLE" itemprop="name"><?=trim($arItem['TITLE'])?></div>
								<? endif ?>
								<? if($arItem['COMPANY']): ?>
									<div class="api-field api-field-company">
										<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_COMPANY'] ? $arParams['MESS_FIELD_NAME_COMPANY'] : Loc::getMessage('API_REVIEWS_LIST_COMPANY'))?></div>
										<div class="api-field-value" data-edit="COMPANY"><?=$arItem['COMPANY']?></div>
									</div>
								<? endif ?>
								<? if($arItem['WEBSITE']): ?>
									<div class="api-field api-field-website">
										<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_WEBSITE'] ? $arParams['MESS_FIELD_NAME_WEBSITE'] : Loc::getMessage('API_REVIEWS_LIST_WEBSITE'))?></div>
										<div class="api-field-value" data-edit="WEBSITE"><?=$arItem['WEBSITE']?></div>
									</div>
								<? endif ?>
								<? if($arItem['ADVANTAGE']): ?>
									<div class="api-field api-field-advantage">
										<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_ADVANTAGE'] ? $arParams['MESS_FIELD_NAME_ADVANTAGE'] : Loc::getMessage('API_REVIEWS_LIST_ADVANTAGE'))?></div>
										<div class="api-field-value" data-edit="ADVANTAGE"><?=$arItem['ADVANTAGE']?></div>
									</div>
								<? endif ?>
								<? if($arItem['DISADVANTAGE']): ?>
									<div class="api-field api-field-disadvantage">
										<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_DISADVANTAGE'] ? $arParams['MESS_FIELD_NAME_DISADVANTAGE'] : Loc::getMessage('API_REVIEWS_LIST_DISADVANTAGE'))?></div>
										<div class="api-field-value" data-edit="DISADVANTAGE"><?=$arItem['DISADVANTAGE']?></div>
									</div>
								<? endif ?>
								<? if($arItem['ANNOTATION']): ?>
									<div class="api-field api-field-annotation">
										<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_ANNOTATION'] ? $arParams['MESS_FIELD_NAME_ANNOTATION'] : Loc::getMessage('API_REVIEWS_LIST_ANNOTATION'))?></div>
										<div class="api-field-value" data-edit="ANNOTATION" itemprop="reviewBody"><?=$arItem['ANNOTATION']?></div>
									</div>
								<? endif ?>
								<? if($arItem['FILES']): ?>
									<div class="api-field api-field-files">
										<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_FILES'] ? $arParams['MESS_FIELD_NAME_FILES'] : Loc::getMessage('API_REVIEWS_LIST_FILES'))?></div>
										<div class="api-field-value">
											<? foreach($arItem['FILES'] as $arFile): ?>
												<a href="<?=$arFile['SRC']?>"
													 <?=($arFile['THUMBNAIL'] ? 'rel="apiReviewsPhoto"' : '')?>
													 data-group="review<?=$arItem['ID']?>" target="_blank">
													<? if($arFile['THUMBNAIL']): ?>
														<div class="api-file-outer api-file-thumbnail api-file-ext-<?=$arFile['EXTENSION']?>"
														     style="background-image: url(<?=$arFile['THUMBNAIL']['SRC']?>)"></div>
													<? else: ?>
														<div class="api-file-outer api-file-attachment js-getDownload" title="<?=$arFile['FORMAT_NAME']?>">
															<span class="api-file-content"><?=$arFile['ORIGINAL_NAME']?></span>
															<span class="api-file-extension api-file-ext-<?=$arFile['EXTENSION']?>"><?=$arFile['EXTENSION']?></span>
														</div>
													<? endif ?>
												</a>
											<? endforeach; ?>
										</div>
									</div>
								<? endif ?>
								<? if($arItem['VIDEOS']): ?>
									<div class="api-field api-field-videos api-field-files">
										<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_VIDEOS'] ? $arParams['MESS_FIELD_NAME_VIDEOS'] : Loc::getMessage('API_REVIEWS_LIST_VIDEOS'))?></div>
										<div class="api-field-value">
											<? foreach($arItem['VIDEOS'] as $video): ?>
												<a href="<?=$video['SRC']?>"
												   rel="apiReviewsVideo"
												   title="<?=$video['TITLE']?>"
												   data-group="review<?=$arItem['ID']?>"
												   data-id="<?=$video['CODE']?>"
												   data-service="<?=$video['SERVICE']?>"
												   data-title="<?=$video['TITLE']?>"
												   target="_blank">
													<div class="api-file-outer api-file-thumbnail"
													     style="background-image: url(<?=$video['THUMBNAIL']['SRC']?>)"></div>
												</a>
											<? endforeach; ?>
										</div>
									</div>
								<? endif ?>
							</div>
						</div>

						<div class="api-footer">
							<div class="api-user-info">
								<div class="api-left">
									<? if($arItem['LOCATION'] || ($arItem['DELIVERY'] && $arItem['DELIVERY']['NAME']) || $arParams['SHOW_THUMBS']): ?>
										<? if($arItem['LOCATION']): ?>
											<span class="api-guest-loc"><?=$arItem['LOCATION']?></span>
										<? endif ?>
										<? if($arItem['DELIVERY'] && $arItem['DELIVERY']['NAME']): ?>
											<span class="api-guest-delivery"> <?=($arItem['LOCATION'] ? '/' : '')?> <?=$arItem['DELIVERY']['NAME']?></span>
										<? endif ?>
									<? endif ?>
								</div>
								<div class="api-right">
									<? if($arParams['SHOW_THUMBS']): ?>
										<div class="api-thumbs">
											<div class="api-thumbs-label"><?=$arParams['MESS_HELPFUL_REVIEW']?></div>
											<div class="api-thumbs-up<? if($arItem['THUMBS_UP'] && $arItem['THUMBS_UP_ACTIVE']): ?> api-thumbs-active<? endif ?>">
												<span class="api-hand"></span>
												<span class="api-counter"><?=$arItem['THUMBS_UP']?></span>
											</div>
											<div class="api-thumbs-down<? if($arItem['THUMBS_DOWN'] && $arItem['THUMBS_DOWN_ACTIVE']): ?> api-thumbs-active<? endif ?>">
												<span class="api-hand"></span>
												<span class="api-counter"><?=$arItem['THUMBS_DOWN']?></span>
											</div>
										</div>
									<? endif ?>
								</div>
							</div>

							<? if($arItem['REPLY']): ?>
								<div class="api-answer<?=($arItem['REPLY_SEND'] == 'Y' ? ' api-answer-send' : '')?>">
									<? if($arParams['SHOP_NAME_REPLY']): ?>
										<div class="api-shop-name"><?=$arParams['SHOP_NAME_REPLY']?></div>
									<? endif ?>
									<div class="api-shop-text" id="api-answer-text-<?=$arItem['ID']?>"><?=$arItem['REPLY']?></div>
								</div>
							<? endif ?>
						</div>
					</div>
				<? endforeach ?>
			</div>
			<? if($arParams['DISPLAY_BOTTOM_PAGER'] && $pagenavigation): ?>
				<div class="api-pagination"><?=$pagenavigation?></div>
			<? endif; ?>
		</div>
	</div>
<? endif ?>