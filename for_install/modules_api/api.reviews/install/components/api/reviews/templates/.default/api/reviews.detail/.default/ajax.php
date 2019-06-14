<?php
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

//$this - объект шаблона
//$component - объект компонента

//$this->GetFolder()
//$tplId = $this->GetEditAreaId($arResult['ID']);

//Объект родительского компонента
//$parent = $component->getParent();
//$parentPath = $parent->getPath();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(dirname(__FILE__) . '/template.php');

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

$bCanEdit = $arParams['IS_EDITOR'];
?>
<? if($arResult): ?>
	<div class="api-reviews-detail ardetail-color-<?=$arParams['COLOR']?>">
		<div class="api-items">
				<?
				$item_class = '';
				if($bCanEdit && ($arResult['PUBLISH'] == 'N' || $arResult['ACTIVE'] == 'N')) {
					$item_class = 'api-item-hidden';
				}

				$arElement = $arResult['ELEMENT_FIELDS'];
				?>
				<div id="review<?=$arResult['ID']?>" class="api-item <?=$item_class?>" itemprop="review" itemscope itemtype="http://schema.org/Review">
					<? if($arResult['STATUS']): ?>
						<div class="api-item-status"><?=$arResult['STATUS']?></div>
					<? endif ?>
					<div class="api-review-link">
						<a class="js-getLink" data-url="<?=$arResult['DETAIL_URL']?>" data-id="<?=$arResult['ID']?>">#<?=$arResult['ID']?></a>
					</div>
					<?/*if($arParams['USE_LIST'] == 'Y'):?>
					<?else:?>
						<div class="api-review-link">
							<a href="<?=$arResult['DETAIL_URL']?>">#<?=$arResult['ID']?></a>
						</div>
					<?endif*/?>
					<div class="api-header">
						<div class="api-user-info">
							<? if($arElement): ?>
								<div class="api-guest-picture">
									<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
										<div class="api-prodpic" style="background-image: url('<?=($arElement['PICTURE']['SRC'] ? $arElement['PICTURE']['SRC'] : $arResult['PICTURE'])?>')"></div>
									</a>
								</div>
							<? elseif($arResult['PICTURE']): ?>
								<div class="api-guest-picture">
									<div class="api-userpic" style="background-image: url('<?=$arResult['PICTURE']['SRC']?>')"></div>
								</div>
							<? endif ?>

							<div class="api-guest-info">
								<? if($arResult['GUEST_NAME']): ?>
									<div class="api-guest-name" itemprop="author" itemscope itemtype="http://schema.org/Person">
										<div itemprop="name">
											<? if($arParams['USE_USER'] == 'Y' && $arResult['USER_URL']): ?>
												<a href="<?=$arResult['USER_URL']?>"><?=$arResult['GUEST_NAME']?></a>
											<?else:?>
												<?=$arResult['GUEST_NAME']?>
											<? endif ?>
										</div>
									</div>
									<div class="api-star-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
										<? if($arResult['RATING']): ?>
											<? for($i = 1; $i <= 5; $i++): ?>
												<? $active = ($arResult['RATING'] >= $i) ? '-active' : ''; ?>
												<i class="api-icon-star api-icon<?=$active?>"></i>
											<? endfor ?>
										<? endif ?>
										<span class="api-hidden" itemprop="ratingValue"><?=($arResult['RATING'] ? $arResult['RATING'] : 5)?></span>
									</div>
									<? if($arResult['DISPLAY_ACTIVE_FROM']): ?>
										<div class="api-date" itemprop="datePublished" content="<?=$arResult['DISPLAY_DATE_PUBLISHED']?>"><?=$arResult['DISPLAY_ACTIVE_FROM']?></div>
									<? endif; ?>
								<? endif; ?>
								<? if($arResult['ORDER_ID']): ?>
									<div class="api-true-buyer"><?=$arParams['MESS_TRUE_BUYER']?></div>
								<? endif ?>
							</div>
						</div>
					</div>

					<div class="api-content">
						<div class="api-fields">
							<? if($arResult['TITLE']): ?>
								<div class="api-field api-field-title" data-edit="TITLE" itemprop="name"><?=trim($arResult['TITLE'])?></div>
							<? endif ?>
							<? if($arResult['COMPANY']): ?>
								<div class="api-field api-field-company">
									<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_COMPANY'] ? $arParams['MESS_FIELD_NAME_COMPANY'] : Loc::getMessage('API_REVIEWS_LIST_COMPANY'))?></div>
									<div class="api-field-value" data-edit="COMPANY"><?=$arResult['COMPANY']?></div>
								</div>
							<? endif ?>
							<? if($arResult['WEBSITE']): ?>
								<div class="api-field api-field-website">
									<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_WEBSITE'] ? $arParams['MESS_FIELD_NAME_WEBSITE'] : Loc::getMessage('API_REVIEWS_LIST_WEBSITE'))?></div>
									<div class="api-field-value" data-edit="WEBSITE"><?=$arResult['WEBSITE']?></div>
								</div>
							<? endif ?>
							<? if($arResult['ADVANTAGE']): ?>
								<div class="api-field api-field-advantage">
									<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_ADVANTAGE'] ? $arParams['MESS_FIELD_NAME_ADVANTAGE'] : Loc::getMessage('API_REVIEWS_LIST_ADVANTAGE'))?></div>
									<div class="api-field-value" data-edit="ADVANTAGE"><?=$arResult['ADVANTAGE']?></div>
								</div>
							<? endif ?>
							<? if($arResult['DISADVANTAGE']): ?>
								<div class="api-field api-field-disadvantage">
									<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_DISADVANTAGE'] ? $arParams['MESS_FIELD_NAME_DISADVANTAGE'] : Loc::getMessage('API_REVIEWS_LIST_DISADVANTAGE'))?></div>
									<div class="api-field-value" data-edit="DISADVANTAGE"><?=$arResult['DISADVANTAGE']?></div>
								</div>
							<? endif ?>
							<? if($arResult['ANNOTATION']): ?>
								<div class="api-field api-field-annotation">
									<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_ANNOTATION'] ? $arParams['MESS_FIELD_NAME_ANNOTATION'] : Loc::getMessage('API_REVIEWS_LIST_ANNOTATION'))?></div>
									<div class="api-field-value" data-edit="ANNOTATION" itemprop="reviewBody"><?=$arResult['ANNOTATION']?></div>
								</div>
							<? endif ?>
							<? if($arResult['FILES']): ?>
								<div class="api-field api-field-files">
									<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_FILES'] ? $arParams['MESS_FIELD_NAME_FILES'] : Loc::getMessage('API_REVIEWS_LIST_FILES'))?></div>
									<div class="api-field-value">
										<?foreach($arResult['FILES'] as $arFile):?>
											<div class="api-attachment-wrap">
												<a href="<?=strtok($arFile['SRC'],'?')?>"
												   class="api-attachment"
												   title="<?=$arFile['ORIGINAL_NAME']?>"
												   data-group="review<?=$arResult['ID']?>"
												   data-id="<?=$arFile['ID']?>"
												   data-type="<?=($arFile['THUMBNAIL'] ? 'image' : 'file')?>"
												   target="_blank">
													<?if($arFile['THUMBNAIL']):?>
														<div class="api-file-outer api-file-thumbnail api-file-ext-<?=$arFile['EXTENSION']?>"
														     style="background-image: url(<?=$arFile['THUMBNAIL']['SRC']?>)"></div>
													<?else:?>
														<div class="api-file-outer api-file-attachment js-getDownload" title="<?=$arFile['FORMAT_NAME']?>">
															<span class="api-file-content"><?=$arFile['ORIGINAL_NAME']?></span>
															<span class="api-file-extension api-file-ext-<?=$arFile['EXTENSION']?>"><?=$arFile['EXTENSION']?></span>
														</div>
													<?endif?>
												</a>
												<?if($bCanEdit):?>
													<div class="api-file-delete js-getFileDelete" data-id="<?=$arResult['ID']?>" data-file="<?=$arFile['ID']?>">&times;</div>
												<? endif ?>
											</div>
										<?endforeach;?>
									</div>
								</div>
							<? endif ?>
							<? if($arResult['VIDEOS']): ?>
								<div class="api-field api-field-videos api-field-files">
									<div class="api-field-label"><?=($arParams['MESS_FIELD_NAME_VIDEOS'] ? $arParams['MESS_FIELD_NAME_VIDEOS'] : Loc::getMessage('API_REVIEWS_LIST_VIDEOS'))?></div>
									<div class="api-field-value">
										<?foreach($arResult['VIDEOS'] as $video):?>
											<div class="api-attachment-wrap">
												<a href="<?=$video['SRC']?>"
												   class="api-attachment"
												   title="<?=$video['TITLE']?>"
												   data-group="review<?=$arResult['ID']?>"
												   data-id="<?=$video['CODE']?>"
												   data-service="<?=$video['SERVICE']?>"
												   data-title="<?=$video['TITLE']?>"
												   data-type="iframe"
												   target="_blank">
													<div class="api-file-thumbnail"
													     style="background-image: url(<?=$video['THUMBNAIL']['SRC']?>)"></div>
												</a>
												<?if($bCanEdit):?>
													<div class="api-file-delete js-getVideoDelete" data-id="<?=$arResult['ID']?>" data-file="<?=$video['ID']?>">&times;</div>
												<? endif ?>
											</div>
										<?endforeach;?>
									</div>
								</div>
							<? endif ?>
						</div>
					</div>

					<div class="api-footer">
						<div class="api-user-info">
							<div class="api-left">

								<? if($bCanEdit && ($arResult['GUEST_EMAIL'] || $arResult['GUEST_PHONE'] || $arResult['ORDER_ID'] || $arResult['IP'])): ?>
									<div class="api-guest-contacts">
										(
										<? if($arResult['GUEST_EMAIL']): ?>
											<a href="mailto:<?=$arResult['GUEST_EMAIL']?>"><?=$arResult['GUEST_EMAIL']?></a> <? endif ?>
										<? if($arResult['GUEST_PHONE']): ?> |
											<a href="tel:<?=$arResult['GUEST_PHONE']?>"><?=$arResult['GUEST_PHONE']?></a> <? endif ?>
										<? if($arResult['ORDER_ID']): ?> | <?=Loc::getMessage('API_REVIEWS_LIST_ORDER_NUM')?> <?=$arResult['ORDER_ID']?><? endif ?>
										<? if($arResult['IP']): ?> | <?=$arResult['IP']?><? endif ?>
										)
									</div>
								<? endif ?>

								<? if($arResult['LOCATION'] || ($arResult['DELIVERY'] && $arResult['DELIVERY']['NAME']) || $arParams['SHOW_THUMBS']): ?>
									<? if($arResult['LOCATION']): ?>
										<span class="api-guest-loc"><?=$arResult['LOCATION']?></span>
									<? endif ?>
									<? if($arResult['DELIVERY'] && $arResult['DELIVERY']['NAME']): ?>
										<span class="api-guest-delivery"> <?=($arResult['LOCATION'] ? '/' : '')?> <?=$arResult['DELIVERY']['NAME']?></span>
									<? endif ?>
								<? endif ?>
							</div>
							<div class="api-right">
								<? if($arParams['SHOW_THUMBS']): ?>
									<div class="api-thumbs">
										<div class="api-thumbs-label"><?=$arParams['MESS_HELPFUL_REVIEW']?></div>
										<div class="api-thumbs-up<? if($arResult['THUMBS_UP'] && $arResult['THUMBS_UP_ACTIVE']): ?> api-thumbs-active<? endif ?>"
										     onclick="jQuery.fn.apiReviewsDetail('vote',this,<?=$arResult['ID']?>,1);">
											<span class="api-hand"></span>
											<span class="api-counter"><?=$arResult['THUMBS_UP']?></span>
										</div>
										<div class="api-thumbs-down<? if($arResult['THUMBS_DOWN'] && $arResult['THUMBS_DOWN_ACTIVE']): ?> api-thumbs-active<? endif ?>"
										     onclick="jQuery.fn.apiReviewsDetail('vote',this,<?=$arResult['ID']?>,-1)">
											<span class="api-hand"></span>
											<span class="api-counter"><?=$arResult['THUMBS_DOWN']?></span>
										</div>
									</div>
								<? endif ?>
							</div>
						</div>

						<? if($arResult['REPLY']): ?>
							<div class="api-answer<?=($arResult['REPLY_SEND'] == 'Y' ? ' api-answer-send' : '')?>">
								<? if($arParams['SHOP_NAME_REPLY']): ?>
									<div class="api-shop-name"><?=$arParams['SHOP_NAME_REPLY']?></div>
								<? endif ?>
								<div class="api-shop-text" id="api-answer-text-<?=$arResult['ID']?>"><?=$arResult['REPLY']?></div>
							</div>
						<? endif ?>

						<? if($bCanEdit): ?>
							<div class="api-admin-controls">
								<button class="api-reply api-button api-button-grey api-button-small"
								        onclick="jQuery.fn.apiReviewsDetail('showReply',<?=$arResult['ID']?>,<?=(int)($arResult['USER_ID'] || $arResult['GUEST_EMAIL'])?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_REPLY')?></button>

								<button class="api-edit api-button api-button-grey api-button-small"
								        onclick="jQuery.fn.apiReviewsDetail('edit',<?=$arResult['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_EDIT')?></button>

								<button class="api-save api-hidden api-button api-button-grey api-button-small"
								        onclick="jQuery.fn.apiReviewsDetail('save',<?=$arResult['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_SAVE')?></button>

								<button class="api-cancel api-hidden api-button api-button-grey api-button-small"
								        onclick="jQuery.fn.apiReviewsDetail('cancel',<?=$arResult['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_CANCEL')?></button>

								<button class="api-hide api-button api-button-grey api-button-small <? if($arResult['ACTIVE'] == 'N'): ?>api-hidden<? endif ?>"
								        onclick="jQuery.fn.apiReviewsDetail('hide',<?=$arResult['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_HIDE')?></button>

								<button class="api-show api-button api-button-grey api-button-small <? if($arResult['ACTIVE'] == 'Y'): ?>api-hidden<? endif ?>"
								        onclick="jQuery.fn.apiReviewsDetail('show',<?=$arResult['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_SHOW')?></button>

								<button class="api-delete api-button api-button-grey api-button-small"
								        onclick="jQuery.fn.apiReviewsDetail('delete',<?=$arResult['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_DELETE')?></button>

								<button class="api-send api-button api-button-success api-button-small <? if($arResult['ACTIVE'] == 'N' || $arParams['USE_SUBSCRIBE'] != 'Y' || $arResult['SUBSCRIBE_SEND'] == 'Y'): ?>api-hidden<? endif ?>"
								        onclick="jQuery.fn.apiReviewsDetail('send',this,<?=$arResult['ID']?>);"><?=Loc::getMessage('API_REVIEWS_LIST_BTN_SEND')?></button>
							</div>
						<? endif ?>
					</div>
				</div>
		</div>
	</div>
<? endif ?>