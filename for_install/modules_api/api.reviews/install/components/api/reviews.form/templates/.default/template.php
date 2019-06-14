<?php

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;

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

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}

$formId  = trim($arParams['FORM_ID']);
$modalId = $formId . '_modal';
?>
<div class="api-reviews-form arform-color-<?=$arParams['COLOR']?>">
	<? if($arParams['MESS_SHOP_TEXT'] || $arParams['MESS_SHOP_NAME'] || $arParams['SHOP_BTN_TEXT']): ?>
		<div class="api-shop-stat">
			<? if($arParams['MESS_SHOP_TEXT']): ?>
				<div class="api-shop-desc">
					<? if($arParams['MESS_SHOP_TEXT']): ?>
						<span class="api-shop-name" itemprop="name"><?=$arParams['MESS_SHOP_TEXT']?></span>
					<? endif ?>
				</div>
			<? endif ?>
			<button class="api-button api-button-large" onclick="jQuery.fn.apiModal('show',{id:'#<?=$modalId?>',width: 600});">
				<span class="api-icon"></span><?=$arParams['MESS_SHOP_BTN_TEXT']?>
			</button>
		</div>
	<? endif ?>

	<div id="<?=$modalId?>" class="api_modal">
		<div class="api_modal_dialog">
			<a class="api_modal_close"></a>
			<? if($arParams['MESS_FORM_TITLE'] || $arParams['MESS_FORM_SUBTITLE']): ?>
				<div class="api_modal_header">
					<? if($arParams['MESS_FORM_TITLE']): ?>
						<div class="api_modal_title"><?=$arParams['MESS_FORM_TITLE']?></div>
					<? endif ?>
					<? if($arParams['MESS_FORM_SUBTITLE']): ?>
						<div class="api_modal_subtitle"><?=$arParams['MESS_FORM_SUBTITLE']?></div>
					<? endif ?>
				</div>
			<? endif ?>
			<div class="api_modal_content">
				<div class="api_modal_loader">
					<div class="api_spinner">
						<svg width="48" height="48" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
							<circle cx="50" cy="50" fill="none" stroke="#0a84ff" stroke-width="5" r="45" stroke-dasharray="100%">
								<animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1.2s" begin="0s" repeatCount="indefinite"></animateTransform>
							</circle>
						</svg>
					</div>
				</div>
				<div class="api_form">
					<? foreach($arParams['DISPLAY_FIELDS'] as $FIELD): ?>
						<?
						$bRequired = in_array($FIELD, $arParams['REQUIRED_FIELDS']);

						if($FIELD == 'DELIVERY' && !$arResult['DELIVERY'])
							continue;

						if($FIELD == 'GUEST_NAME' || $FIELD == 'GUEST_EMAIL' || $FIELD == 'GUEST_PHONE')
							continue;

						if($FIELD == 'CITY') {
							if(!$arResult['MODULES']['sale'] || $arParams['CITY_VIEW'])
								continue;
						}


						$fieldId          = $formId . '_field_' . ToLower($FIELD);
						$fieldName        = $arParams[ 'MESS_FIELD_NAME_' . $FIELD ];
						$fieldPlaceholder = $arParams[ 'MESS_FIELD_PLACEHOLDER_' . $FIELD ];
						?>
						<div class="api_row">
							<? if($FIELD != 'RATING'): ?>
								<div class="api_label<? if($bRequired): ?> required<? endif ?>">
									<?=$fieldName?><? if($bRequired): ?><span class="api_required">*</span><? endif ?>
								</div>
							<? endif ?>

							<div class="api_controls">
								<? if($FIELD == 'RATING'): ?>
									<div class="api-form-rating">
										<div class="api-star-rating">
											<i class="api-icon-star active" data-label="<?=$arParams['MESS_STAR_RATING_1']?>"></i>
											<i class="api-icon-star active" data-label="<?=$arParams['MESS_STAR_RATING_2']?>"></i>
											<i class="api-icon-star active" data-label="<?=$arParams['MESS_STAR_RATING_3']?>"></i>
											<i class="api-icon-star active" data-label="<?=$arParams['MESS_STAR_RATING_4']?>"></i>
											<i class="api-icon-star active" data-label="<?=$arParams['MESS_STAR_RATING_5']?>"></i>
											<input type="hidden" value="5" name="RATING" class="api-field">
										</div>
										<div class="api-star-rating-label"><?=$arParams['MESS_STAR_RATING_5']?></div>
									</div>
								<? elseif($FIELD == 'TITLE' || $FIELD == 'ORDER_ID' || $FIELD == 'COMPANY' || $FIELD == 'WEBSITE'): ?>
								<input type="text" class="api-field" name="<?=$FIELD?>"
								       placeholder="<?=$fieldPlaceholder?>">
								<? elseif($FIELD == 'DELIVERY'): ?>
									<select id="API_REVIEWS_FORM_DELIVERY" name="DELIVERY" class="api-field">
										<option value=""><?=Loc::getMessage('API_REVIEWS_FORM_CHOOSE')?></option>
										<? foreach($arResult['DELIVERY'] as $arDelivery): ?>
											<option value="<?=$arDelivery['ID']?>"><?=$arDelivery['NAME']?></option>
										<? endforeach ?>
									</select>
								<? elseif($FIELD == 'CITY'): ?>
									<?
									\CSaleLocation::proxySaleAjaxLocationsComponent(
										 array(
												"LOCATION_VALUE"  => "",
												"CITY_INPUT_NAME" => 'CITY',
												"SITE_ID"         => SITE_ID,
										 ),
										 array(),
										 '',
										 true,
										 'api-location'
									);
									?>
								<? elseif($FIELD == 'FILES'): ?>
									<div class="api_upload" id="<?=$fieldId;?>">
										<ul class="api_file_list">
											<? if($arResult['FILES']): ?>
												<? foreach($arResult['FILES'] as $file): ?>
													<li>
														<div class="api_progress_bar">
															<div class="api_progress" rel="100" style="width: 100%;"></div>
															<div class="api_file_remove" data-code="<?=$file['code']?>" data-type="<?=$file['type']?>"></div>
														</div>
														<div class="api_file_label">
															<span class="api_file_ext_<?=GetFileExtension($file['name'])?>"></span>
															<span class="api_file_name"><?=$file['name']?></span>
															<span class="api_file_size"><?=$file['size_round']?></span>
														</div>
													</li>
												<? endforeach; ?>
											<? endif; ?>
										</ul>
										<div class="api_upload_drop">
											<span class="api_upload_drop_icon"></span>
											<span class="api_upload_drop_text"><?=Loc::getMessage('API_REVIEWS_FORM_UPLOAD_DROP')?></span>
											<input id="<?=$fieldId;?>_file"
											       class="api_upload_file api-field"
											       type="file"
											       name="<?=$FIELD;?>[]"
											       multiple="">
										</div>
										<div class="api_upload_info">
											<?=Loc::getMessage('API_REVIEWS_FORM_UPLOAD_INFO', array(
												 '#UPLOAD_FILE_SIZE#'  => $arParams['UPLOAD_FILE_SIZE'],
												 '#UPLOAD_FILE_LIMIT#' => $arParams['UPLOAD_FILE_LIMIT'],
												 '#FILE_TYPE#'         => $arParams['UPLOAD_FILE_TYPE'],
											))?>
										</div>
									</div>
									<script type="text/javascript">
										(function ($) {
											$('#<?=$fieldId;?>').apiUpload({
												fileName: '<?=$FIELD;?>',
												maxFiles: <?=$arParams['UPLOAD_FILE_LIMIT'];?>,
												maxFileSize: <?=$arParams['UPLOAD_MAX_FILESIZE'];?>,
												extFilter: '<?=$arParams['UPLOAD_FILE_TYPE']?>',
												extraData: {
													'sessid': BX.bitrix_sessid(),
													'API_REVIEWS_FORM_ACTION': 'FILE_UPLOAD',
												},
												errors: {
													onFileSizeError: '<?=Loc::getMessage('API_REVIEWS_FORM_UPLOAD_onFileSizeError')?>',
													onFileTypeError: '<?=Loc::getMessage('API_REVIEWS_FORM_UPLOAD_onFileTypeError')?>',
													onFileExtError: '<?=Loc::getMessage('API_REVIEWS_FORM_UPLOAD_onFileExtError')?>',
													onFilesMaxError: '<?=Loc::getMessage('API_REVIEWS_FORM_UPLOAD_onFilesMaxError')?>',
												},
												callback: {
													onComplete: function (node, response, xhr) {
														if (response) {
															if (response.result === 'error') {
																if (response.alert)
																	$.fn.apiAlert(response.alert);
																else if (response.message.length)
																	$.fn.apiAlert({content: response.message});
															}
														}
													},
													onError: function (node, errors) {
														var mess = '';
														for (var i in errors) {
															mess += errors[i] + "<br>";
														}
														$.fn.apiAlert({
															type: 'info',
															theme: 'jbox',
															content: mess,
														});
													},
													onFallbackMode: function (message) {
														$('#<?=$fieldId;?> .api_upload_drop').html(message);
														console.error(message);
													},
												}
											});

											$('#<?=$fieldId;?>').on('click', '.api_file_remove', function () {
												var fileButton = $(this);
												var fileCode = $(this).data('code') || '';
												if (fileCode.length) {
													$.ajax({
														type: 'POST',
														cache: false,
														data: {
															'sessid': BX.bitrix_sessid(),
															'API_REVIEWS_FORM_ACTION': 'FILE_DELETE',
															'FILE_CODE': fileCode,
														},
														success: function () {
															$(fileButton).closest('li').remove();
														}
													});
												}
												else {
													$(fileButton).closest('li').remove();
												}
											})
										})(jQuery)
									</script>
								<? elseif($FIELD == 'VIDEOS'): ?>
									<div id="<?=$fieldId;?>">
										<div class="api_video_list">
											<? if($arResult['VIDEOS']): ?>
												<? foreach($arResult['VIDEOS'] as $video): ?>
													<div class="api_video_item">
														<div class="api_video_remove" data-id="<?=$video['id']?>"></div>
														<a href="<?=$video['url']?>" target="_blank"><?=$video['title']?></a>
													</div>
												<? endforeach; ?>
											<? endif; ?>
										</div>
										<div class="api_video_upload">
											<input type="text" name="VIDEOS" class="api-field" placeholder="<?=$fieldPlaceholder?>">
										</div>
									</div>
									<div class="api_video_info">
										<?=Loc::getMessage('API_REVIEWS_FORM_UPLOAD_VIDEO_INFO', array(
											 '#UPLOAD_VIDEO_LIMIT#' => $arParams['UPLOAD_VIDEO_LIMIT'],
										))?>
									</div>
								<? else: ?>
									<textarea name="<?=$FIELD?>" class="api-field" placeholder="<?=$fieldPlaceholder?>" data-autoresize></textarea>
								<? endif ?>
							</div>
						</div>
					<? endforeach; ?>

					<div class="api-guest-row api_row">
						<div class="api_label"><?=Loc::getMessage('API_REVIEWS_FORM_LABEL_INTRODUCE')?></div>

						<div class="api_controls">
							<div class="api-guest-form">
								<? if(in_array('GUEST_NAME', $arParams['DISPLAY_FIELDS'])): ?>
									<div class="api-guest-form-field">
										<?
										$guest_name_placeholder = ($USER->IsAuthorized() ? $USER->GetFormattedName() : $arParams['MESS_FIELD_PLACEHOLDER_GUEST_NAME']);
										if(in_array('GUEST_NAME', $arParams['REQUIRED_FIELDS']))
											$guest_name_placeholder .= ' *';
										?>
										<input type="text"
										       class="api-field"
										       placeholder="<?=$guest_name_placeholder?>"
										       name="GUEST_NAME"
										       value="">
									</div>
								<? endif ?>
								<? if(in_array('CITY', $arParams['DISPLAY_FIELDS']) && $arParams['CITY_VIEW']): ?>
									<div class="api-guest-form-field">
										<?
										$guest_city_placeholder = $arParams['MESS_FIELD_PLACEHOLDER_CITY'];
										if(in_array('CITY', $arParams['REQUIRED_FIELDS']))
											$guest_city_placeholder .= ' *';
										?>
										<input type="text"
										       class="api-field"
										       placeholder="<?=$guest_city_placeholder?>"
										       name="CITY">
									</div>
								<? endif ?>
								<? if(in_array('GUEST_EMAIL', $arParams['DISPLAY_FIELDS'])): ?>
									<div class="api-guest-form-field">
										<?
										$guest_email_placeholder = ($USER->IsAuthorized() ? $USER->GetEmail() : $arParams['MESS_FIELD_PLACEHOLDER_GUEST_EMAIL']);
										if(in_array('GUEST_EMAIL', $arParams['REQUIRED_FIELDS']))
											$guest_email_placeholder .= ' *';
										?>
										<input type="text"
										       class="api-field"
										       placeholder="<?=$guest_email_placeholder?>"
										       name="GUEST_EMAIL"
										       value="">
									</div>
								<? endif ?>
								<? if(in_array('GUEST_PHONE', $arParams['DISPLAY_FIELDS'])): ?>
									<div class="api-guest-form-field">
										<?
										$guest_phone_placeholder = $arParams['MESS_FIELD_PLACEHOLDER_GUEST_PHONE'];
										if(in_array('GUEST_PHONE', $arParams['REQUIRED_FIELDS']))
											$guest_phone_placeholder .= ' *';
										?>
										<input type="text"
										       class="api-field"
										       placeholder="<?=$guest_phone_placeholder?>"
										       name="GUEST_PHONE">
									</div>
								<? endif ?>
							</div>
						</div>
					</div>

					<? if($arParams['MESS_RULES_TEXT'] && $arParams['MESS_RULES_LINK']): ?>
						<div class="api_row api-rules">
							<div class="api_controls">
								<a href="<?=$arParams['MESS_RULES_LINK']?>" target="_blank"><?=$arParams['MESS_RULES_TEXT']?></a>
							</div>
						</div>
					<? endif ?>

					<? if($arParams['USE_EULA'] && $arParams['MESS_EULA']): ?>
						<div class="api_row api-rules">
							<div class="api_controls">
								<label class="api-rules-label">
									<input type="checkbox" name="EULA_ACCEPTED" value="Y" class="api-field" <?=$arResult['EULA_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
									<div class="api-rules-text"><?=$arParams['MESS_EULA']?></div>
								</label>
								<div class="api-rules-error api-eula-error"><?=$arParams['MESS_EULA_CONFIRM']?></div>
							</div>
						</div>
					<? endif ?>

					<? if($arParams['USE_PRIVACY'] && $arParams['MESS_PRIVACY']): ?>
						<div class="api_row api-rules">
							<div class="api_controls">
								<label class="api-rules-label">
									<input type="checkbox" name="PRIVACY_ACCEPTED" value="Y" class="api-field" <?=$arResult['PRIVACY_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
									<div class="api-rules-text">
										<? if($arParams['MESS_PRIVACY_LINK']): ?>
											<a rel="nofollow" href="<?=$arParams['MESS_PRIVACY_LINK']?>" target="_blank"><?=$arParams['MESS_PRIVACY']?></a>
										<? else: ?>
											<?=$arParams['MESS_PRIVACY']?>
										<? endif ?>
									</div>
								</label>
								<div class="api-rules-error api-privacy-error"><?=$arParams['MESS_PRIVACY_CONFIRM']?></div>
							</div>
						</div>
					<? endif ?>

					<div class="api_row api_buttons">
						<button class="api-button api-button-large api-form-submit api_button_block">
							<span class="api-icon"></span><span class="api-button-text"><?=Loc::getMessage('API_REVIEWS_FORM_SUBMIT_TEXT_DEFAULT')?></span>
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>

	<?
	ob_start();
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$.fn.apiReviewsForm({
				id:                   '<?=$modalId?>',
				USE_EULA:             '<?=CUtil::JSEscape($arParams['USE_EULA'])?>',
				USE_PRIVACY:          '<?=CUtil::JSEscape($arParams['USE_PRIVACY'])?>',
				message: {
					submit_text_default: '<?=Loc::getMessage('API_REVIEWS_FORM_SUBMIT_TEXT_DEFAULT')?>',
					submit_text_ajax:    '<?=Loc::getMessage('API_REVIEWS_FORM_SUBMIT_TEXT_AJAX')?>',
				}
			});

			$.fn.apiModal({
				id: '#<?=$modalId?>',
				width: 600
			});
		});
	</script>
	<?
	$htmlJs = ob_get_contents();
	ob_end_clean();

	Asset::getInstance()->addString($htmlJs, true, AssetLocation::AFTER_JS);
	?>
</div>
