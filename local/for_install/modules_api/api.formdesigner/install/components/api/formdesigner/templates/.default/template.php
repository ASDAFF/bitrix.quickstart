<?
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

use Bitrix\Main\Loader,
	 Bitrix\Main\Page\Asset,
	 Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);


$theme        = 'off';
$color        = 'off';
$formId       = trim($arParams['UNIQUE_FORM_ID']);
$modalId      = trim($arParams['MODAL_ID']);
$jsSubmitText = trim($arParams['SUBMIT_BUTTON_TEXT']);
$jsSubmitAjax = trim($arParams['SUBMIT_BUTTON_AJAX']);

if($arParams['TEMPLATE_COLOR'])
	$color = $arParams['TEMPLATE_COLOR'];

if($arParams['TEMPLATE_THEME']) {
	$this->addExternalCss($templateFolder . '/themes/' . $arParams['TEMPLATE_THEME'] . '/style.css');
	$theme = $arParams['TEMPLATE_THEME'];
}

/*if($arParams['INPUTMASK_JS'])
{
	$this->addExternalJs($templateFolder .'/plugins/inputmask/jquery.inputmask.bundle.min.js');
	$this->addExternalJs($templateFolder .'/plugins/inputmask/extra/phone-codes/phone.js');
	//$this->addExternalJs($templateFolder .'/plugins/inputmask/extra/phone-codes/phone-ru.js');
}*/

ob_start();
?>
	<script type="text/javascript">
		"use strict";
		jQuery(document).ready(function ($) {

			var jsFormId = '#<?=$formId;?>';
			var jsModalId = '<?=$modalId;?>';
			var jsWrapId = '#afd_wrap_<?=$formId;?>';
			var jsTheme = '<?=$theme;?>';
			var jsSubmitText = '<?=$jsSubmitText;?>';
			var jsSubmitAjax = '<?=$jsSubmitAjax;?>';

			var wysiwyg_cfg = {};
			<?if($arParams['WYSIWYG_ON']):?>
			wysiwyg_cfg = {
				lang: '<?=LANGUAGE_ID?>',
				minHeight: 200,
				maxHeight: 600,
				//imageUpload: '/tmp/api_formdesigner/images',
				//fileUpload: '/tmp/api_formdesigner/files',
				imageResizable: true,
				imagePosition: true,
				linkSize: 250,
				convertLinks: false,
				linkify: true,
				linkNofollow: true,
				pasteLinkTarget: '_blank',
				placeholder: 'Enter a text...',
				script: true,
				structure: true,
				overrideStyles: true,
				toolbarFixed: false,
				//preClass: 'api-highlighted',
				preSpaces: 2,
				//videoContainerClass: 'video-container',
				//scrollTarget: '#my-scrollable-layer'
				buttons: ['format', 'bold', 'italic', 'deleted', 'lists', 'orderedlist', 'alignment', 'link', 'horizontalrule'], //'image', 'file'
				buttonsHide: ['html'],
				formatting: ['p', 'blockquote', 'pre', 'h2', 'h3', 'h4', 'h5'],
				plugins: ['bufferbuttons', 'underline', 'inlinestyle', 'alignment', 'counter', 'fontcolor', 'fontfamily', 'fontsize', 'table', 'video', 'scriptbuttons', 'fullscreen'], //'source','imagemanager','filemanager',
				callbacks: {
					click: function (e) {
						//console.log(e);
					},
					keyup: function (e) {
						//console.log(e);
					}
				}
			};
			<?endif?>

			var flatpickr_cfg = {
				enableTime: true,
				allowInput: true,
				time_24hr: true,
				dateFormat: 'd.m.Y H:i:S',
				//minDate: 'today',
				minuteIncrement: 10,
				locale: 'ru',
				plugins: [new confirmDatePlugin({})]
			};

			var validate_cfg = {
				framework: 'bootstrap4',
				row: {
					selector: '.afd-row',
					valid: 'afd-row-success',
					invalid: 'afd-row-danger'
				},
				err: {
					// The CSS class of each message element
					clazz: 'afd-error',

					// The error messages container
					container: ''
				}
			};

			<?if($arParams['USE_MODAL']):?>
			$.fn.apiModal({
				id: '<?=$modalId?>'
			});
			<?endif?>

			//---------- Form submit action ----------//
			$(jsFormId).on('submit', function (e) {
				var _this = this;

				<?if($arParams['USE_EULA']):?>
				var eula_accepted = $(_this).find('input[name=EULA_ACCEPTED]').prop('checked');
				if (!eula_accepted) {
					$.fn.apiAlert({
						content: '<?=CUtil::JSEscape($arParams['MESS_EULA_CONFIRM'])?>'
					});
					return false;
				}
				<?endif;?>

				<?if($arParams['USE_PRIVACY']):?>
				var privacy_accepted = $(_this).find('input[name=PRIVACY_ACCEPTED]').prop('checked');
				if (!privacy_accepted) {
					$.fn.apiAlert({
						content: '<?=CUtil::JSEscape($arParams['MESS_PRIVACY_CONFIRM'])?>'
					});
					return false;
				}
				<?endif;?>

				var submitButton = $(_this).find('[type="submit"]');
				submitButton.prop('disabled', true).find('span').html(jsSubmitAjax);

				$.ajax({
					type: 'POST',
					cache: false,
					data: $(_this).serializeArray(),
					success: function (data) {
						//console.log(data);

						var result = data.result || {};
						var params = data.params || {};

						var isOk = result.MESSAGE.SUCCESS || false;
						//console.log('isOk', isOk);

						//Form send success
						if (isOk) {

							//SEND_GOALS
							if (params.SEND_GOALS) {
								eval(result.GOALS_SETTINGS);
							}

							//JS_REDIRECT
							if (result.JS_REDIRECT) {
								eval(result.JS_REDIRECT);
							}

							$(jsModalId).find('.api_modal_content').css({'padding': 0})
						}
						else {
							$(jsWrapId).find('.afd-sys-alert:visible').html('').slideUp();
						}

						if (result.MESSAGE) {
							if (result.MESSAGE.HIDDEN) {

								var arMessage = result.MESSAGE.HIDDEN || {};
								for (var id in arMessage) {
									$(jsFormId + ' [data-field="' + id + '"]').addClass('afd-row-danger');

									if (params.SHOW_ERRORS && arMessage[id]) {
										$(jsFormId + ' [data-field="' + id + '"]').find('.afd-error').html(arMessage[id]).slideDown();
									}
								}

								if ($(jsFormId + ' .afd-row-danger:first').length) {
									$('html, body').animate({
										scrollTop: $(jsFormId + ' .afd-row-danger:first').offset().top - 30
									}, 200, function () {
										$(jsFormId + ' .afd-row-danger:first').addClass('afd-active');
									});
								}
							}
							else if (result.MESSAGE.SUCCESS) {
								var message = '<div class="afd-alert afd-alert-success"><span class="afd-icon"></span>';

								message += '<div class="afd-alert-title">' + result.MESSAGE.SUCCESS.join('<br>') + '</div>';
								if (params.MESS_SUCCESS_DESC) {
									message += '<div class="afd-alert-desc">' + params.MESS_SUCCESS_DESC + '</div>';
								}
								message += '<div class="afd-alert-bottom"><span class="afd-btn api_button api_button_mini" onclick="window.location.reload()">' + params.MESS_SUCCESS_BTN + '</span></div>';
								message += '</div>';

								$(jsWrapId).html(message);
								//$(jsFormId).replaceWith(message);

								if ($(jsWrapId).length) {
									$('html, body').animate({
										scrollTop: $(jsWrapId).offset().top - 30
									}, 200);
								}
							}
							else {
								var message = '';
								for (var id in result.MESSAGE) {
									var elements = result.MESSAGE[id];
									message += '<div class="afd-alert afd-alert-' + id.toString().toLowerCase() + '"><span class="afd-icon"></span><div class="afd-alert-title">' + elements.join('<br>') + '</div></div>';
								}

								if (message.length) {
									$(jsWrapId).find('.afd-sys-alert').html(message).slideDown();
								}

								$('html, body').animate({
									scrollTop: $(jsWrapId).offset().top - 30
								}, 200);
							}
						}

						refreshFields_<?=$formId;?>();

						submitButton.prop('disabled', false).find('span').html(jsSubmitText);
					}
				});

				e.preventDefault();
				return false;
			});

			//---------- User consent ----------//
			<?if($arParams['USER_CONSENT']):?>
			var obUserConset = <?=\Bitrix\Main\Web\Json::encode($arResult['DISPLAY_USER_CONSENT'])?>;

			$(jsFormId).on('click', '.afd-row-user-consent .afd-accept-label', function (e) {
				e.preventDefault();

				var label = this;
				var checkbox = $(this).find('input');
				var agreementId = $(this).data('id');

				var config = obUserConset[agreementId].CONFIG;
				var data = {
					'action': 'getText',
					'sessid': BX.bitrix_sessid(),
				};

				$.ajax({
					type: 'POST',
					url: '/bitrix/components/bitrix/main.userconsent.request/ajax.php',
					data: $.extend({}, data, config),
					error: function (jqXHR, textStatus, errorThrown) {
						alert('textStatus: ' + textStatus);
						alert('errorThrown: ' + errorThrown);
					},
					success: function (response) {
						if (!!response.text) {
							$.fn.apiAlert({
								type: 'confirm',
								title: '<?=Loc::getMessage('AFDC_USER_CONSENT_TITLE')?>',
								width: 600,
								content: '<textarea rows="50" readonly>' + response.text + '</textarea>',
								labels: {
									ok: '<?=Loc::getMessage('AFDC_USER_CONSENT_BTN_ACCEPT')?>',
									cancel: '<?=Loc::getMessage('AFDC_USER_CONSENT_BTN_REJECT')?>',
								},
								callback: {
									onConfirm: function (isConfirm) {
										if (isConfirm) {
											checkbox.prop('checked', true);
											$(label).find('.afd-error:visible').slideUp();
										}
										else {
											checkbox.prop('checked', false);
											$(label).find('.afd-error:hidden').slideDown();
										}
									}
								}
							});
						}
					}
				});
			});
			<?endif?>

			//---------- Highlight active field ----------//
			$(jsFormId).on('click change focus', '.afd-field', function () {

				refreshFields_<?=$formId;?>();

				$(this).parents('.afd-row').addClass('afd-active');
			});

			//---------- Validation required fields ----------//
			$(jsFormId).on('click change keyup', '.afd-row-required .afd-field', function () {
				if ($(this).hasClass('afd-field-multi')) {
					if (!$(this).find('input:checked').length) {
						$(this).parents('.afd-row').addClass('afd-row-danger');
						$(this).parents('.afd-row').find('.afd-error').slideDown();
					}
					else {
						$(this).parents('.afd-row').removeClass('afd-row-danger');
						$(this).parents('.afd-row').find('.afd-error').slideUp();
					}
				}
				else {
					if (!this.value) {
						$(this).parents('.afd-row').addClass('afd-row-danger');
						$(this).parents('.afd-row').find('.afd-error').slideDown();
					}
					else {
						$(this).parents('.afd-row').removeClass('afd-row-danger');
						$(this).parents('.afd-row').find('.afd-error').slideUp();
					}
				}
			});

			//---------- Refresh captcha ----------//
			$(jsFormId).on('click', '.afd-captcha-refresh', function (e) {
				e.preventDefault();

				var btn_refresh = $(this);
				var captcha_sid = $(jsFormId + '_afd_row_captcha_sid');

				$(btn_refresh).addClass('afd-animation-rotate');
				$.ajax({
					type: 'POST',
					cache: false,
					data: {
						'sessid': BX.bitrix_sessid(),
						'API_FD_ACTION': 'CAPTCHA_REFRESH',
						'UNIQUE_FORM_ID': '<?=$formId;?>',
					},
					success: function (response) {
						$(captcha_sid).find('input:hidden').val(response.captcha_sid);
						$(captcha_sid).find('img').attr('src', response.captcha_src);
						$(btn_refresh).removeClass('afd-animation-rotate');
					}
				});
			});

			//---------- Init functions ----------//
			//Theme helper
			function refreshTheme_<?=$formId;?>() {
				if (jsTheme == 'modern') {
					$(jsFormId + ' .afd-select-single option[value=""]').text('');
				}
			}

			//Highlight fields
			function refreshFields_<?=$formId;?>() {
				$(jsFormId + ' .afd-field').each(function () {

					if ($(this).hasClass('afd-field-multi')) {
						if (!$(this).find('input:checked').length)
							$(this).parents('.afd-row').removeClass('afd-active');
						else
							$(this).parents('.afd-row').addClass('afd-active');
					}
					else {
						if (!this.value)
							$(this).parents('.afd-row').removeClass('afd-active');
						else
							$(this).parents('.afd-row').addClass('afd-active');
					}
				});
			}

			function showPicture_<?=$formId;?>(data, _this) {

				if (data.picture) {
					$(_this)
					.parent()
					.find('.afd-show-picture-block')
					.fadeOut(100, function () {
						$(this).css({'width': data.width, 'height': data.height, 'marginTop': -(data.height / 2)})
						.html('<img src="' + data.picture + '" width="' + data.width + '" height="' + data.height + '" alt="" title="">')
						.fadeIn(100);
					});
				}
				else {
					$(_this).parent().find('.afd-show-picture-block').hide();
				}
			}

			function refreshPicture_<?=$formId;?>() {
				$(jsFormId + ' .afd-show-picture').each(function () {
					var _this = $(this);

					$('<div class="afd-show-picture-block"></div>').insertAfter(_this);

					var data1 = _this.find('option:selected').data();
					if (data1)
						showPicture_<?=$formId;?>(data1, _this);

					$(document).on('change', _this, function () {
						var data = _this.find('option:selected').data();
						showPicture_<?=$formId;?>(data, _this);
					});
				});
			}

			function refreshInputmask_<?=$formId;?>() {
				<?if($arParams['INPUTMASK_JS']):?>
				$(jsFormId + ' [data-inputmask]').inputmask();
				<?endif?>
			}

			function refreshDate_<?=$formId;?>() {
				$(jsFormId).find('.afd-type-date, .afd-type-datetime').each(function () {
					$(this).flatpickr(flatpickr_cfg);
				});
			}

			function refreshWysiwyg_<?=$formId;?>() {
				$(jsFormId).find('.afd-type-wysiwyg').each(function () {
					$(this).redactor(wysiwyg_cfg);
				});
			}

			function autoresizeTextarea_<?=$formId;?>() {
				$(jsFormId).find('[data-autoresize]').each(function () {
					var offset = this.offsetHeight - this.clientHeight;
					var resizeTextarea = function (el) {
						$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
					};
					$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
					resizeTextarea(this);
				});
			}

			function refreshHL_<?=$formId;?>() {

				$(jsFormId).find('.afd-hl-checkbox').each(function () {
					$(this).find('.afd-hl-item').on('click', function (e) {
						e.preventDefault();
						if (!$(this).is('.afd-hl-active')) {
							$(this).addClass('afd-hl-active').find(':checkbox').prop('checked', true);
						}
						else {
							$(this).removeClass('afd-hl-active').find(':checkbox').prop('checked', false);
						}
					});
				});

				$(jsFormId).find('.afd-hl-radio').each(function () {
					$(this).find('.afd-hl-item').on('click', function (e) {
						e.preventDefault();
						$(this).addClass('afd-hl-active').siblings('div').removeClass('afd-hl-active');
						$(this).find(':radio').prop('checked', true);
					});
				});
			}

			function refreshPlugins_<?=$formId;?>() {
				$(jsFormId).apiForm();
			}

			<?if($arParams['VALIDATE_ON']):?>
			$(jsFormId).formValidation(validate_cfg);
			<?endif?>

			//---------- Execute functions ----------//
			refreshFields_<?=$formId;?>();
			refreshPicture_<?=$formId;?>();
			refreshInputmask_<?=$formId;?>();
			refreshTheme_<?=$formId;?>();
			refreshDate_<?=$formId;?>();
			refreshHL_<?=$formId;?>();
			refreshPlugins_<?=$formId;?>();

			<?if($arParams['WYSIWYG_ON']):?>
			refreshWysiwyg_<?=$formId;?>();
			<?else:?>
			autoresizeTextarea_<?=$formId;?>();
			<?endif?>
		});
	</script>
	<style>
		<?if($arParams['FORM_WIDTH']):?>
		#afd_wrap_<?=$formId;?>{ max-width: <?=$arParams['FORM_WIDTH']?> }
		<?endif?>
		<?if($arParams['TEMPLATE_BG_COLOR'] && $arParams['TEMPLATE_BG_COLOR'] != 'false'):?>
		#afd_wrap_<?=$formId;?>{ background: <?=$arParams['TEMPLATE_BG_COLOR']?>; padding: 15px; -webkit-box-shadow: 0 1px 2px 0 #857f7f; -moz-box-shadow: 0 1px 2px 0 #857f7f; box-shadow: 0 1px 2px 0 #857f7f; }
		<?endif?>
	</style>
<?
$str = ob_get_contents();
ob_end_clean();
Asset::getInstance()->addString($str);
?>
<? if($arParams['USE_MODAL']): ?>
	<style>
		<?=$modalId?>
		.api-formdesigner{width: 100% !important; max-width: none !important;}
	</style>
	<? if($arParams['MODAL_BTN_TEXT']): ?>
	<button <?=($arParams['MODAL_BTN_ID'] ? 'id="' . $arParams['MODAL_BTN_ID'] . '"' : '')?>
		 class="<?=$arParams['MODAL_BTN_CLASS']?>"
		 onclick="jQuery.fn.apiModal('show',{id:'<?=$modalId?>'});">
		<span class="<?=$arParams['MODAL_BTN_SPAN_CLASS']?>"></span><?=$arParams['MODAL_BTN_TEXT']?>
	</button>
<? endif ?>
	<div id="<?=ltrim($modalId, '#')?>" class="api_modal">
		<div class="api_modal_dialog">
			<div class="api_modal_close"></div>
			<? if($arParams['MODAL_HEADER_TEXT']): ?>
				<div class="api_modal_header"><?=$arParams['MODAL_HEADER_TEXT']?></div>
			<? endif ?>
			<div class="api_modal_content">
				<? endif ?>

				<!--form start-->
				<div id="afd_wrap_<?=$formId;?>" class="api-formdesigner afd-theme-<?=$theme;?> afd-color-<?=$color;?>">
					<? if($arParams['SHOW_TITLE'] == 'Y' && $arParams['FORM_TITLE']): ?>
						<div class="afd-title"><?=$arParams['FORM_TITLE'];?></div>
					<? endif; ?>
					<div class="afd-sys-alert"></div>
					<form id="<?=$formId;?>"
					      name="<?=$formId;?>"
					      action="<?=POST_FORM_ACTION_URI;?>"
					      method="POST"
					      class="afd-form <?=$arParams['FORM_HORIZONTAL'] ? 'afd-form-horizontal': ''?>"
					      autocomplete="<?=$arParams['FORM_AUTOCOMPLETE']?>">
						<?=bitrix_sessid_post();?>
						<input type="text" name="ANTIBOT[NAME]" value="<?=$arResult['ANTIBOT']['NAME'];?>" autocomplete="off" class="afd-antibot">
						<input type="hidden" name="API_FD_AJAX" value="<?=$arParams['UNIQUE_FORM_ID'];?>">
						<input type="hidden" name="UNIQUE_FORM_ID" value="<?=$arParams['UNIQUE_FORM_ID'];?>">

						<?
						if(!empty($arResult['DISPLAY_PROPERTIES'])) {
							foreach($arResult['DISPLAY_PROPERTIES'] as $arProp) {
								$fieldClass = $fieldData = $form_row_class = $fieldError = $chooseOption = '';

								$propCode      = $arProp['CODE'];
								$fieldId       = ToLower($arParams['UNIQUE_FORM_ID'] . '_field_' . $propCode);
								$fieldMulti    = $arProp['MULTIPLE'] == 'Y';
								$fieldMultiCnt = ($fieldMulti && $arProp['MULTIPLE_CNT']) ? $arProp['MULTIPLE_CNT'] : 1;
								$fieldCode     = 'FIELDS[' . $propCode . ']' . ($fieldMulti ? '[]' : '');
								$fieldReq      = ($arProp['IS_REQUIRED'] == 'Y' ? '<span class="afd-asterisk">*</span>' : '');
								$fieldName     = $arProp['NAME'] . $fieldReq;

								//if($arProp['ERROR'] && $arParams['SHOW_ERRORS_BOTTOM'])
								//$fieldError = '<div class="afd-error">' . trim($arProp['ERROR']) . '</div>';

								$fieldError = '<div class="afd-error"></div>';

								if($arProp['IS_REQUIRED'] == 'Y')
									$form_row_class .= ' afd-row-required';


								if($arProp['IS_REQUIRED'] != 'Y' || ($arProp['LIST_TYPE'] == 'L' && !$fieldMulti))
									$chooseOption = '<option value="">' . $arParams['MESS_CHOOSE'] . '</option>';


								/*if($arParams['SHOW_ERRORS_IN_FIELD'] && $arProp['ERROR'])
									$form_row_class .= ' afd-row-danger';
								elseif($arProp['USER_VALUE'] && !$arProp['ERROR'])
									$form_row_class .= ' afd-row-success afd-active';*/


								//if($arProp['USER_TYPE'])
								//$form_row_class .= ' ' . ToLower($arProp['USER_TYPE']);


								//---------- Multiple select size ----------//
								if($fieldMulti || $arProp['ROW_COUNT'] > 1)
									$arProp['ROW_COUNT'] = (count($arProp['DISPLAY_VALUE']) >= 10) ? 10 : intval(count($arProp['DISPLAY_VALUE']));

								if($fieldMulti && $chooseOption)
									$arProp['ROW_COUNT']++;


								//---------- Single select size ----------//
								if(!$fieldMulti && $arProp['ROW_COUNT'] == 1)
									$arProp['ROW_COUNT'] = 1;


								//---------- size attribute ----------//
								$size = ' size="' . $arProp['ROW_COUNT'] . '"';


								//---------- data- attribute ----------//
								if(count($arProp['DATA'])) {
									foreach($arProp['DATA'] as $data)
										$fieldData .= ' ' . $data;

									unset($data);
								}


								//---------- class attribute ----------//
								$fieldClass .= ToLower('afd-field-' . $propCode);


								//---------- User settings in property ----------//
								$arSettings = $arProp['USER_TYPE_SETTINGS'];

								switch($arProp['PROPERTY_TYPE']) {
									case 'HIDDEN':
										{
											?>
											<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
											<input type="hidden"
											       name="<?=$fieldCode;?>"
											       id="<?=$fieldId;?>"
											       class="<?=$fieldClass;?>"
											       value="<?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?>"<?=$fieldData;?>>
										<? endfor; ?>
											<?
										}
										break;

									case 'DIVIDER':
										{
											?>
											<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
											<div id="<?=$fieldId;?>" class="uk-form-row api-fd-divider"><?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?></div>
										<? endfor; ?>
											<?
										}
										break;

									case 'N': //number
									case 'S': // textarea + text + DateTime
										{
											?>
											<? if($arProp['USER_TYPE'] == 'APIFD_PSList'): ?>
											<div class="afd-row <?=$form_row_class?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<? if($arSettings['VIEW'] == 'R'): ?>
														<div class="afd-control afd-field afd-field-multi afd-field-radio">
															<? foreach($arProp['DISPLAY_VALUE'] as $item): ?>
																<?
																$isChecked = false;
																if(($item['ID'] == $arProp['USER_VALUE']))
																	$isChecked = true;
																?>
																<label for="<?=$fieldId . '_' . $item['ID'];?>" class="api_radio <?=$isChecked ? 'api_active' : ''?>">
																	<input type="radio"
																	       name="<?=$fieldCode;?>"
																	       value="<?=$item['ID'];?>"
																	       class="<?=$fieldClass;?> afd-type-radio"
																		 <?=$isChecked ? 'checked=""' : ''?>
																		     id="<?=$fieldId . '_' . $item['ID'];?>" <?=$fieldData;?>>
																	<span class="afd-control-name"><?=$item['NAME'];?></span>
																</label>
															<? endforeach; ?>
														</div>
													<? else: ?>
														<div class="afd-control">
															<select name="<?=$fieldCode;?>"
															        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single"
															        id="<?=$fieldId;?>" <? //=$size?> <?=$fieldData;?>>
																<?=$chooseOption?>
																<? foreach($arProp['DISPLAY_VALUE'] as $item): ?>
																	<option value="<?=$item['ID'];?>"<? if(($item['ID'] == $arProp['USER_VALUE'])): ?> selected=""<? endif; ?>><?=$item['NAME'];?></option>
																<? endforeach; ?>
															</select>
														</div>
													<? endif ?>
													<?=$fieldError?>
												</div>
											</div>
										<? elseif($arProp['USER_TYPE'] == 'HTML' || $arProp['USER_TYPE'] == 'TEXT'): ?>
											<div class="afd-row <?=$form_row_class?> <?=($arParams['WYSIWYG_ON'] ? 'afd-row-wysiwyg' : '')?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
														<div class="afd-control">
															<textarea name="<?=$fieldCode;?>"
															          class="<?=$fieldClass;?> afd-field afd-type-textarea <?=($arProp['USER_TYPE'] == 'HTML' ? 'afd-type-wysiwyg' : '')?>"
															          data-autoresize
															          id="<?=$fieldId;?><?=$fieldMulti ? '_' . $i : '';?>"<?=$fieldData;?>><?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?></textarea>
														</div>
													<? endfor; ?>
													<?=$fieldError?>
												</div>
											</div>
										<? elseif($arProp['USER_TYPE'] == 'DateTime' || $arProp['USER_TYPE'] == 'Date'): ?>
											<div class="afd-row <?=$form_row_class?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
														<div class="afd-control">
															<input type="text"
															       name="<?=$fieldCode;?>"
															       id="<?=$fieldId;?><?=$fieldMulti ? '_' . $i : '';?>"
															       class="<?=$fieldClass;?> afd-field afd-type-text afd-type-<?=ToLower($arProp['USER_TYPE'])?>"
															       value="<?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?>"<?=$fieldData;?>>
															<i class="api_icon api_icon_calendar"></i>
															<? /* $APPLICATION->IncludeComponent(
																		"bitrix:main.calendar",
																		"",
																		Array(
																			"SHOW_INPUT"         => "N",
																			"FORM_NAME"          => $formId,
																			"INPUT_NAME"         => $fieldMulti ? $fieldId . '_' . $i : $fieldId,
																			"INPUT_NAME_FINISH"  => "",
																			"INPUT_VALUE"        => "",
																			"INPUT_VALUE_FINISH" => "",
																			"SHOW_TIME"          => ($arProp['USER_TYPE'] == 'DateTime' ? 'Y' : 'N'),
																			"HIDE_TIMEBAR"       => "N",
																		),
																		null,
																		Array('HIDE_ICONS' => 'Y')
																	); */ ?>
														</div>
													<? endfor; ?>
													<?=$fieldError?>
												</div>
											</div>
										<? elseif($arProp['USER_TYPE'] == 'UserID'): ?>
											<? if(!empty($arProp['DISPLAY_VALUE'])): ?>
												<div class="afd-row <?=$form_row_class?>" data-field="<?=$propCode;?>">
													<? if($fieldName): ?>
														<div class="afd-label"><?=$fieldName;?></div>
													<? endif; ?>
													<div class="afd-controls">
														<div class="afd-control <?=($fieldMulti ? 'afd-control-multiple' : '')?>">
															<? if($arProp['LIST_TYPE'] == 'L' && !$fieldMulti): ?>

																<select name="<?=$fieldCode;?>"
																        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single"
																        id="<?=$fieldId;?>" <?=$size?> <?=$fieldData;?>>
																	<?=$chooseOption?>
																	<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																		<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE']))): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
																	<? endforeach; ?>
																</select>
															<? elseif($arProp['LIST_TYPE'] == 'L' && $fieldMulti): ?>

																<select name="<?=$fieldCode;?>"
																        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-multiple"
																        id="<?=$fieldId;?>"
																        multiple="" <?=$size?> <?=$fieldData;?>>
																	<?=$chooseOption?>
																	<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																		<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE']))): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
																	<? endforeach; ?>
																</select>
															<? endif; ?>
														</div>
														<?=$fieldError?>
													</div>
												</div>
											<? endif; ?>
										<? elseif($arProp['USER_TYPE'] == 'directory'): ?>
											<div class="afd-row afd-row-static <?=$form_row_class?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<? if($arProp['DISPLAY_VALUE']): ?>
														<div class="afd-hl-list afd-hl-list-<?=ToLower($arProp['DISPLAY_TYPE'])?>  afd-hl-<?=$fieldMulti ? 'checkbox' : 'radio'?>">
															<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																<?
																$itemActive = false;
																if($fieldMulti) {
																	if(($v['UF_DEF'] == 1 && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($v['UF_XML_ID'], $arProp['USER_VALUE'])))
																		$itemActive = true;
																}
																else {
																	if(($v['UF_DEF'] == 1 && !$arResult['POST']) || ($arProp['USER_VALUE'] && $v['UF_XML_ID'] == $arProp['USER_VALUE']))
																		$itemActive = true;
																}
																?>
																<div class="afd-hl-item<?=$itemActive ? ' afd-hl-active' : ''?>">
																	<label>
																		<? if($fieldMulti): ?>
																			<input type="checkbox" name="<?=$fieldCode;?>" value="<?=$v['UF_XML_ID']?>"<?=$itemActive ? ' checked=""' : ''?>>
																		<? else: ?>
																			<input type="radio" name="<?=$fieldCode;?>" value="<?=$v['UF_XML_ID']?>"<?=$itemActive ? ' checked=""' : ''?>>
																		<? endif ?>
																		<? if($v['UF_FILE']): ?>
																			<span class="afd-hl-icon"><i style="background-image: url('<?=$v['UF_FILE']['SRC']?>');"></i></span>
																		<? endif ?>
																		<span class="afd-hl-name"><?=$v['UF_NAME']?></span>
																	</label>
																</div>
															<? endforeach; ?>
														</div>
													<? endif; ?>
													<?=$fieldError?>
												</div>
											</div>
										<? elseif($arProp['USER_TYPE'] && $arProp['GetPublicEditHTML']): ?>
											<div class="afd-row <?=$form_row_class?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<?
													//echo "<pre>"; print_r($arProp);echo "</pre>";
													//CIBlockPropertyMapYandex::GetPublicEditHTML();
													//CIBlockPropertyDate::GetPublicEditHTML();
													//Ycaweb\CIBlockPropertyMap2GIS::GetPublicEditHTML();
													echo call_user_func_array($arProp['GetPublicEditHTML'],
														 array(
																$arProp,
																array(
																	 'VALUE'       => $arProp['USER_VALUE'],
																	 'DESCRIPTION' => "",
																),
																array(
																	 'VALUE'       => $fieldCode,
																	 'DESCRIPTION' => '',
																	 'FORM_NAME'   => $formId,
																),
														 ));
													?>
												</div>
											</div>
										<? else: ?>
											<div class="afd-row <?=$form_row_class?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
														<div class="afd-control">
															<? if($arProp['ROW_COUNT'] > 1): ?>
																<textarea name="<?=$fieldCode;?>"
																          id="<?=$fieldId;?>"
																          class="<?=$fieldClass;?> afd-field afd-type-textarea"
																          data-autoresize
																          cols="<?=$arProp['COL_COUNT']?>"
																          rows="<?=$arProp['ROW_COUNT']?>"><?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?></textarea>
															<? else: ?>
																<input type="text"
																       name="<?=$fieldCode;?>"
																       id="<?=$fieldId;?>"
																       class="<?=$fieldClass;?> afd-field afd-type-text"
																       value="<?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?>"<?=$fieldData;?>>
															<? endif; ?>
														</div>
													<? endfor; ?>
													<?=$fieldError?>
												</div>
											</div>
										<? endif; ?>
											<?
										}
										break;

									// select + checkbox + radio
									case 'L':
										{
											?>
											<? if(!empty($arProp['DISPLAY_VALUE'])): ?>
											<div class="afd-row <?=$form_row_class?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<? if($arProp['LIST_TYPE'] == 'L' && !$fieldMulti): ?>
														<div class="afd-control">
															<select name="<?=$fieldCode;?>"
															        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single"
															        id="<?=$fieldId;?>" <?=$size?> <?=$fieldData;?>>
																<?=$chooseOption?>
																<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																	<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || $arProp['USER_VALUE'] == $k): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
																<? endforeach; ?>
															</select>
														</div>
													<? elseif($arProp['LIST_TYPE'] == 'L' && $fieldMulti): ?>
														<div class="afd-control <?=($fieldMulti ? 'afd-control-multiple' : '')?>">
															<select name="<?=$fieldCode;?>"
															        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-multiple"
															        id="<?=$fieldId;?>"
															        multiple="" <?=$size?> <?=$fieldData;?>>
																<?=$chooseOption?>
																<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																	<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE']))): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
																<? endforeach; ?>
															</select>
														</div>
													<? elseif($arProp['LIST_TYPE'] == 'C' && $fieldMulti): ?>
														<div class="afd-control afd-field afd-field-multi afd-field-checkbox">
															<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																<?
																$isChecked = false;
																if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE'])))
																	$isChecked = true;
																?>
																<label for="<?=$fieldId;?>_<?=$k;?>" class="api_checkbox <?=$isChecked ? 'api_active' : ''?>">
																	<input type="checkbox"
																	       name="<?=$fieldCode;?>"
																	       id="<?=$fieldId;?>_<?=$k;?>"
																	       class="afd-type-checkbox"
																		 <?=$isChecked ? 'checked=""' : ''?>
																		 <? if($v === reset($arProp['DISPLAY_VALUE'])): ?>
																			 data-validation-group="<?=$fieldId;?>"
																			 <?=$fieldData;?>
																		 <? endif; ?>
																		     value="<?=$k;?>"> <?=$v['VALUE'];?></label>
															<? endforeach; ?>
														</div>
													<? else: ?>
														<div class="afd-control afd-field afd-field-multi afd-field-radio">
															<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																<?
																$isChecked = false;
																if(($v['DEF'] == 'Y' && !$arResult['POST']) || $arProp['USER_VALUE'] == $k)
																	$isChecked = true;
																?>
																<label for="<?=$fieldId;?>_<?=$k;?>" class="api_radio <?=$isChecked ? 'api_active' : ''?>">
																	<input type="radio"
																	       name="<?=$fieldCode;?>"
																	       id="<?=$fieldId;?>_<?=$k;?>"
																	       class="afd-type-radio"
																		 <?=$isChecked ? 'checked=""' : ''?>
																		 <? if($v === reset($arProp['DISPLAY_VALUE']) && !$arParams['MESS_CHOOSE']): ?>
																			 data-validation-group="<?=$fieldId;?>"
																			 <?=$fieldData;?>
																		 <? endif; ?>
																		     value="<?=$k;?>"> <?=$v['VALUE'];?></label>
															<? endforeach; ?>
														</div>

													<? endif; ?>
													<?=$fieldError?>
												</div>
											</div>
										<? endif; ?>
											<?
										}
										break;

									case 'E': // Link to elements
									case 'G': // Link to section
										{
											?>
											<? if(!empty($arProp['DISPLAY_VALUE'])): ?>
											<div class="afd-row <?=$form_row_class?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<div class="afd-control <?=($fieldMulti ? 'afd-control-multiple' : '')?>">
														<? if($arProp['USER_TYPE'] == 'APIFD_ESList'): ?>
															<?
															$bShowPicture = $arSettings['SHOW_PICTURE'];
															?>
															<select name="<?=$fieldCode;?>"
															        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single <? if($bShowPicture): ?>afd-show-picture<? endif ?>"
															        id="<?=$fieldId;?>" <? //=$size?> <?=$fieldData;?>>
																<?=$chooseOption?>
																<? foreach($arProp['DISPLAY_VALUE'] as $k => $section): ?>
																	<optgroup label="<?=$section['NAME']?>">
																		<? foreach($section['ITEMS'] as $item): ?>
																			<option value="<?=$item['ID'];?>"
																				 <? if($item['PICTURE']): ?>
																					 data-picture="<?=$item['PICTURE']['SRC']?>"
																					 data-width="<?=$item['PICTURE']['WIDTH']?>"
																					 data-height="<?=$item['PICTURE']['HEIGHT']?>"
																				 <? endif; ?>
																				      <? if(($item['ID'] == $arProp['USER_VALUE'])): ?>selected=""<? endif; ?>><?=$item['NAME'];?></option>
																		<? endforeach; ?>
																	</optgroup>
																<? endforeach; ?>
															</select>

														<? else: ?>

															<? if($arProp['LIST_TYPE'] == 'L' && !$fieldMulti): ?>
																<select name="<?=$fieldCode;?>"
																        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single"
																        id="<?=$fieldId;?>" <? //=$size?> <?=$fieldData;?>>
																	<?=$chooseOption?>
																	<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																		<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || ($k == $arProp['USER_VALUE'])): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
																	<? endforeach; ?>
																</select>
															<? elseif($arProp['LIST_TYPE'] == 'L' && $fieldMulti): ?>
																<select name="<?=$fieldCode;?>"
																        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-multiple"
																        id="<?=$fieldId;?>"
																        multiple="" <?=$size?> <?=$fieldData;?>>
																	<?=$chooseOption?>
																	<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
																		<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE']))): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
																	<? endforeach; ?>
																</select>
															<? endif; ?>
														<? endif ?>
													</div>
													<?=$fieldError?>
												</div>
											</div>
										<? endif; ?>
											<?
										}
										break;


									case 'F':
										{
											?>
											<div class="afd-row afd-row-type-upload <?=$form_row_class?>" data-field="<?=$propCode;?>">
												<? if($fieldName): ?>
													<div class="afd-label"><?=$fieldName;?></div>
												<? endif; ?>
												<div class="afd-controls">
													<?=$fieldError?>
													<div class="api_upload" id="<?=$fieldId;?>">
														<ul class="api_file_list">
															<? if($arProp['DISPLAY_VALUE']): ?>
																<? foreach($arProp['DISPLAY_VALUE'] as $file): ?>
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
															<span class="api_upload_drop_text"><?=Loc::getMessage('AFD_AJAX_UPLOAD_DROP')?></span>
															<input id="<?=$fieldId;?>_file"
															       class="api_upload_file <?=$fieldClass;?>"
															       type="file"
															       name="<?=$fieldCode;?>"
																 <?=($fieldMulti ? 'multiple=""' : '')?>
																 <?=$fieldData;?>>
														</div>
														<div class="api_upload_info">
															<?=Loc::getMessage('AFD_AJAX_UPLOAD_INFO', array(
																 '#UPLOAD_FILE_SIZE#'  => $arParams['UPLOAD_FILE_SIZE'],
																 '#UPLOAD_FILE_LIMIT#' => $arParams['UPLOAD_FILE_LIMIT'],
																 '#FILE_TYPE#'         => $arProp['FILE_TYPE'],
															))?>
														</div>
													</div>
												</div>
												<script type="text/javascript">
													jQuery(document).ready(function ($) {
														$('#<?=$fieldId;?>').apiUpload({
															fileName: '<?=$arProp['CODE'];?>',
															maxFiles: <?=$arParams['UPLOAD_FILE_LIMIT'];?>,
															maxFileSize: <?=$arParams['UPLOAD_MAX_FILESIZE'];?>,
															extFilter: '<?=$arProp['FILE_TYPE']?>',
															extraData: {
																'sessid': BX.bitrix_sessid(),
																'API_FD_ACTION': 'FILE_UPLOAD',
																'UNIQUE_FORM_ID': '<?=$arParams['UNIQUE_FORM_ID'];?>',
															},
															errors: {
																onFileSizeError: '<?=Loc::getMessage('AFD_AJAX_UPLOAD_onFileSizeError')?>',
																onFileTypeError: '<?=Loc::getMessage('AFD_AJAX_UPLOAD_onFileTypeError')?>',
																onFileExtError: '<?=Loc::getMessage('AFD_AJAX_UPLOAD_onFileExtError')?>',
																onFilesMaxError: '<?=Loc::getMessage('AFD_AJAX_UPLOAD_onFilesMaxError')?>',
															},
															callback: {
																onError: function (node, errors) {
																	var mess = '';
																	for (var i in errors) {
																		mess += errors[i] + "<br>";
																	}
																	$.fn.apiAlert({
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
																		'API_FD_ACTION': 'FILE_DELETE',
																		'UNIQUE_FORM_ID': '<?=$arParams['UNIQUE_FORM_ID'];?>',
																		'FILE_NAME': '<?=$arProp['CODE'];?>',
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
													})
												</script>
											</div>
											<?
										}
								}
							}
						}
						?>

						<? if($arParams['USE_BX_CAPTCHA']): ?>
							<div id="<?=$formId?>_afd_row_captcha_sid" class="afd-row afd-row-static afd-row-captcha_sid">
								<div class="afd-label"><?=Loc::getMessage('AFD_AJAX_FIELD_CAPTCHA_SID')?></div>
								<div class="afd-controls">
									<input type="hidden" name="CAPTCHA[SID]" value="<?=$arResult['CAPTCHA_CODE']?>">
									<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>"
									     width="180" height="40" alt="<?=Loc::getMessage('AFD_AJAX_FIELD_CAPTCHA_LOADING')?>">
									<span class="afd-captcha-refresh afd-icon-refresh" title="<?=Loc::getMessage('AFD_AJAX_FIELD_CAPTCHA_REFRESH')?>"></span>
								</div>
							</div>
							<div id="<?=$formId?>_afd_row_captcha_word" class="afd-row afd-row-required afd-row-captcha_word" data-field="CAPTCHA">
								<div class="afd-label"><?=Loc::getMessage('AFD_AJAX_FIELD_CAPTCHA_WORD')?>
									<span class="afd-asterisk">*</span></div>
								<div class="afd-controls">
									<div class="afd-control">
										<input type="text" name="CAPTCHA[WORD]" maxlength="50" value="" autocomplete="off"
										       class="afd-field afd-field-captcha-word afd-type-text">
									</div>
									<div class="afd-error"></div>
								</div>
							</div>
						<? endif ?>

						<? if($arParams['USE_EULA'] && $arParams['MESS_EULA']): ?>
							<div class="afd-row afd-row-eula afd-row-accept">
								<div class="afd-label"></div>
								<div class="afd-controls">
									<label class="afd-accept-label">
										<input type="checkbox" name="EULA_ACCEPTED" value="Y" class="api-field" <?=$arResult['EULA_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
										<div class="afd-accept-text"><?=$arParams['MESS_EULA']?></div>
									</label>
								</div>
							</div>
						<? endif ?>

						<? if($arParams['USE_PRIVACY'] && $arParams['MESS_PRIVACY']): ?>
							<div class="afd-row afd-row-privacy afd-row-accept">
								<div class="afd-label"></div>
								<div class="afd-controls">
									<label class="afd-accept-label">
										<input type="checkbox" name="PRIVACY_ACCEPTED" value="Y" class="api-field" <?=$arResult['PRIVACY_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
										<div class="afd-accept-text">
											<? if($arParams['MESS_PRIVACY_LINK']): ?>
												<a rel="nofollow" href="<?=$arParams['MESS_PRIVACY_LINK']?>" target="_blank"><?=$arParams['MESS_PRIVACY']?></a>
											<? else: ?>
												<?=$arParams['MESS_PRIVACY']?>
											<? endif ?>
										</div>
									</label>
								</div>
							</div>
						<? endif ?>

						<? if($arParams['USER_CONSENT']): ?>
							<div class="afd-row afd-row-user-consent afd-row-accept">
								<div class="afd-label"></div>
								<div class="afd-controls">
									<? foreach($arResult['DISPLAY_USER_CONSENT'] as $agreementId => $arAgreement): ?>
										<div class="afd-control <?=$arAgreement['ERROR'] ? 'afd-row-danger' : ''?>" data-field="USER_CONSENT_<?=$agreementId?>">
											<div class="afd-accept-label" data-id="<?=$agreementId?>">
												<input type="checkbox"
												       name="USER_CONSENT[]"
												       value="<?=$agreementId?>"
													 <?=($arParams['USER_CONSENT_IS_CHECKED'] == 'Y' || $arAgreement['USER_VALUE'] == $agreementId) ? 'checked=""' : ''?>>
												<div class="afd-accept-text"><?=$arAgreement['LABEL_TEXT'];?></div>
												<div class="afd-error"></div>
											</div>
										</div>
									<? endforeach; ?>
								</div>
							</div>
							<? /*$APPLICATION->IncludeComponent(
								 "bitrix:main.userconsent.request",
								 "",
								 array(
										"ID" => $arParams["USER_CONSENT_ID"],
										"IS_CHECKED" => $arParams["USER_CONSENT_IS_CHECKED"],
										"AUTO_SAVE" => "Y",
										"IS_LOADED" => $arParams["USER_CONSENT_IS_LOADED"],
										"REPLACE" => array(
											 'button_caption' => 'Subscribe!',
											 'fields' => array('FIELDS[EMAIL]')
										),
								 )
							);*/ ?>
						<? endif; ?>

						<div class="afd-row">
							<div class="afd-label"></div>
							<div class="afd-controls">
								<button type="submit"
								        name="API_FD_SUBMIT"
								        value="Y"
								        class="<?=$arParams['SUBMIT_BUTTON_CLASS'];?><?=$BUTTON_SIZE?>">
									<span><?=$arParams['SUBMIT_BUTTON_TEXT'];?></span></button>
							</div>
						</div>
					</form>
				</div>
				<!--form end-->

				<? if($arParams['USE_MODAL']): ?>
			</div><!--api_modal_content-->

			<? if($arParams['MODAL_FOOTER_TEXT']): ?>
				<div class="api_modal_footer"><?=$arParams['MODAL_FOOTER_TEXT']?></div>
			<? endif ?>
		</div><!--api_modal_dialog-->
	</div><!--api_modal-->
<? endif ?>