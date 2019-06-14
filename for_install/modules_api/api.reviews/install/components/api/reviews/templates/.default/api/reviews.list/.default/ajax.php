<?php
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

use \Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(dirname(__FILE__) . '/template.php');

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

$bCanEdit = $arParams['IS_EDITOR'];

if($arParams['DISPLAY_TOP_PAGER'] || $arParams['DISPLAY_BOTTOM_PAGER']) {
	ob_start();
	$this->addExternalCss('/bitrix/components/bitrix/main.pagenavigation/templates/.default/style.css');
	$APPLICATION->IncludeComponent('bitrix:main.pagenavigation', '', array(
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
<div class="api-reviews-list arlist-color-<?=$arParams['COLOR']?>">
	<? $APPLICATION->IncludeComponent('api:reviews.filter', "", $arParams, $component->getParent()); ?>
	<? if($arResult['ITEMS']): ?>
		<? if($arParams['DISPLAY_TOP_PAGER'] && $pagenavigation): ?>
			<div class="api-pagination"><?=$pagenavigation?></div>
		<? endif; ?>
		<div class="api-items">
			<? foreach($arResult['ITEMS'] as $arItem): ?>
				<?
				$item_class = '';
				if($bCanEdit && ($arItem['PUBLISH'] == 'N' || $arItem['ACTIVE'] == 'N')) {
					$item_class = 'api-item-hidden';
				}

				$arElement = $arItem['ELEMENT_FIELDS'];
				?>
				<div id="review<?=$arItem['ID']?>" class="api-item <?=$item_class?>" itemprop="review" itemscope itemtype="http://schema.org/Review">
					<? if($arItem['STATUS']): ?>
						<div class="api-item-status"><?=$arItem['STATUS']?></div>
					<? endif ?>
					<div class="api-review-link">
						<a href="<?=$arItem['DETAIL_URL']?>" rel="nofollow" target="_blank">#<?=$arItem['ID']?></a>
					</div>
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
									<div class="api-guest-name" itemprop="author" itemscope itemtype="http://schema.org/Person">
										<div itemprop="name">
											<? if($arParams['USE_USER'] == 'Y' && $arItem['USER_URL']): ?>
												<a href="<?=$arItem['USER_URL']?>"><?=$arItem['GUEST_NAME']?></a>
											<? else: ?>
												<?=$arItem['GUEST_NAME']?>
											<? endif ?>
										</div>
									</div>
								<? endif; ?>
								<div class="api-star-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
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
											<div class="api-attachment-wrap">
												<a href="<?=strtok($arFile['SRC'],'?')?>"
												   class="api-attachment"
													 title="<?=$arFile['ORIGINAL_NAME']?>"
													 data-group="review<?=$arItem['ID']?>"
													 data-id="<?=$arFile['ID']?>"
													 data-type="<?=($arFile['THUMBNAIL'] ? 'image' : 'file')?>"
												   target="_blank">
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
												<? if($bCanEdit): ?>
													<div class="api-file-delete js-getFileDelete" data-id="<?=$arItem['ID']?>" data-file="<?=$arFile['ID']?>">&times;</div>
												<? endif ?>
											</div>
										<? endforeach; ?>
									</div>
								</div>
							<? endif ?>
							<? if($arItem['VIDEOS']): ?>
								<div class="api-field api-field-videos api-field-files">
									<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_VIDEOS'] ? $arParams['MESS_FIELD_NAME_VIDEOS'] : Loc::getMessage('API_REVIEWS_LIST_VIDEOS'))?></div>
									<div class="api-field-value">
										<? foreach($arItem['VIDEOS'] as $video): ?>
											<div class="api-attachment-wrap">
												<a href="<?=$video['SRC']?>"
												   class="api-attachment"
												   title="<?=$video['TITLE']?>"
												   data-group="review<?=$arItem['ID']?>"
												   data-id="<?=$video['CODE']?>"
												   data-service="<?=$video['SERVICE']?>"
												   data-type="iframe"
												   target="_blank">
													<div class="api-file-outer api-file-thumbnail"
													     style="background-image: url(<?=$video['THUMBNAIL']['SRC']?>)"></div>
												</a>
												<? if($bCanEdit): ?>
													<div class="api-file-delete js-getVideoDelete" data-id="<?=$arItem['ID']?>" data-file="<?=$video['ID']?>">&times;</div>
												<? endif ?>
											</div>
										<? endforeach; ?>
									</div>
								</div>
							<? endif ?>
						</div>
					</div>

					<div class="api-footer">
						<div class="api-user-info">
							<div class="api-left">

								<? if($bCanEdit && ($arItem['GUEST_EMAIL'] || $arItem['GUEST_PHONE'] || $arItem['ORDER_ID'] || $arItem['IP'])): ?>
									<div class="api-guest-contacts">
										(
										<? if($arItem['GUEST_EMAIL']): ?>
											<a href="mailto:<?=$arItem['GUEST_EMAIL']?>"><?=$arItem['GUEST_EMAIL']?></a> <? endif ?>
										<? if($arItem['GUEST_PHONE']): ?> |
											<a href="tel:<?=$arItem['GUEST_PHONE']?>"><?=$arItem['GUEST_PHONE']?></a> <? endif ?>
										<? if($arItem['ORDER_ID']): ?> | <?=Loc::getMessage('API_REVIEWS_LIST_ORDER_NUM')?> <?=$arItem['ORDER_ID']?><? endif ?>
										<? if($arItem['IP']): ?> | <?=$arItem['IP']?><? endif ?>
										)
									</div>
								<? endif ?>

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
										<div class="api-thumbs-up<? if($arItem['THUMBS_UP'] && $arItem['THUMBS_UP_ACTIVE']): ?> api-thumbs-active<? endif ?>"
										     onclick="jQuery.fn.apiReviewsList('vote',this,<?=$arItem['ID']?>,1);">
											<span class="api-hand"></span> <span class="api-counter"><?=$arItem['THUMBS_UP']?></span>
										</div>
										<div class="api-thumbs-down<? if($arItem['THUMBS_DOWN'] && $arItem['THUMBS_DOWN_ACTIVE']): ?> api-thumbs-active<? endif ?>"
										     onclick="jQuery.fn.apiReviewsList('vote',this,<?=$arItem['ID']?>,-1)">
											<span class="api-hand"></span> <span class="api-counter"><?=$arItem['THUMBS_DOWN']?></span>
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

						<? if($bCanEdit): ?>
							<div class="api-admin-controls">
								<button class="api-reply api_button api_button_small"
								        onclick="jQuery.fn.apiReviewsList('showReply',<?=$arItem['ID']?>,<?=($arItem['GUEST_EMAIL'] ? 1 : 0)?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_REPLY')?></button>

								<button class="api-edit api_button api_button_small"
								        onclick="jQuery.fn.apiReviewsList('edit',<?=$arItem['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_EDIT')?></button>

								<button class="api-save api-hidden api_button api_button_small"
								        onclick="jQuery.fn.apiReviewsList('save',<?=$arItem['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_SAVE')?></button>

								<button class="api-cancel api-hidden api_button api_button_small"
								        onclick="jQuery.fn.apiReviewsList('cancel',<?=$arItem['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_CANCEL')?></button>

								<button class="api-hide api_button api_button_small <? if($arItem['ACTIVE'] == 'N'): ?>api-hidden<? endif ?>"
								        onclick="jQuery.fn.apiReviewsList('hide',<?=$arItem['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_HIDE')?></button>

								<button class="api-show api_button api_button_small <? if($arItem['ACTIVE'] == 'Y'): ?>api-hidden<? endif ?>"
								        onclick="jQuery.fn.apiReviewsList('show',<?=$arItem['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_SHOW')?></button>

								<button class="api-delete api_button api_button_small"
								        onclick="jQuery.fn.apiReviewsList('delete',<?=$arItem['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_DELETE')?></button>

								<button class="api-send api_button api_button_success api_button_small <? if($arItem['ACTIVE'] == 'N' || $arParams['USE_SUBSCRIBE'] != 'Y' || $arItem['SUBSCRIBE_SEND'] == 'Y'): ?>api-hidden<? endif ?>"
								        onclick="jQuery.fn.apiReviewsList('send',this,<?=$arItem['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_SEND')?></button>
							</div>
						<? endif ?>
					</div>
				</div>
			<? endforeach ?>
		</div>
		<? if($arParams['DISPLAY_BOTTOM_PAGER'] && $pagenavigation): ?>
			<div class="api-pagination"><?=$pagenavigation?></div>
		<? endif; ?>
	<? endif ?>
</div>
