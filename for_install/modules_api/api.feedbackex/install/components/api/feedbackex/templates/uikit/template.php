<?

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Page\AssetLocation;

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
 * @var CUserTypeManager         $USER_FIELD_MANAGER
 */

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

$FORM_ID           = trim($arParams['API_FEX_FORM_ID']);
$FORM_AUTOCOMPLETE = $arParams['FORM_AUTOCOMPLETE'] ? 'on' : 'off';

$INPUT_ROW_CLASS      = 'uk-form-row';
$INPUT_LABEL_CLASS    = 'uk-form-label';
$INPUT_CONTROLS_CLASS = 'uk-form-controls';
$FIELD_SIZE           = $arParams['FIELD_SIZE'] ? ' uk-form-' . $arParams['FIELD_SIZE'] : '';
$BUTTON_SIZE          = $arParams['FIELD_SIZE'] ? ' uk-button-' . $arParams['FIELD_SIZE'] : '';
$FIELD_NAME_POSITION  = $arParams['FIELD_NAME_POSITION'] ? ' uk-form-' . $arParams['FIELD_NAME_POSITION'] : ' uk-form-horizontal';

if($arParams['HIDE_FIELD_NAME'])
	$FIELD_NAME_POSITION = ' uk-form-stacked';

?>
<? if($arParams['USE_MODAL']): ?>

	<? if($arParams['MODAL_BTN_TEXT']): ?>
	<button <?=($arParams['MODAL_BTN_ID'] ? 'id="' . $arParams['MODAL_BTN_ID'] . '"' : '')?>
		 class="<?=$arParams['MODAL_BTN_CLASS']?>"
		 onclick="jQuery.fn.apiModal('show',{id:'<?=$arParams['MODAL_ID']?>'});">
		<span class="<?=$arParams['MODAL_BTN_SPAN_CLASS']?>"></span><?=$arParams['MODAL_BTN_TEXT']?>
	</button>
<? endif ?>

	<div id="<?=ltrim($arParams['MODAL_ID'], '#')?>" class="api_modal">
		<div class="api_modal_dialog">
			<div class="api_modal_close"></div>
			<? if($arParams['MODAL_HEADER_TEXT']): ?>
				<div class="api_modal_header"><?=$arParams['MODAL_HEADER_TEXT']?></div>
			<? endif ?>
			<div class="api_modal_content">
				<? endif ?>

				<!--form start-->
				<div id="API_FEX_<?=$FORM_ID?>" class="<?=trim($arParams['FORM_CLASS'] . ' api-feedbackex')?>">
					<div class="theme-uikit theme-<?=$arParams['THEME'];?> color-<?=$arParams['COLOR'];?>">
						<form id="<?=$FORM_ID?>"
						      class="uk-form <?=$FIELD_NAME_POSITION?>"
						      name="api_feedbackex_form"
						      enctype="multipart/form-data"
						      method="POST"
						      action="<?=POST_FORM_ACTION_URI;?>"
						      autocomplete="<?=$FORM_AUTOCOMPLETE?>">

							<input type="hidden" name="API_FEX_FORM_ID" value="<?=$FORM_ID?>">
							<input type="hidden" name="API_FEX_SUBMIT_ID" value="<?=$FORM_ID?>">
							<input type="text" name="ANTIBOT[NAME]" value="<?=$arResult['ANTIBOT']['NAME'];?>" class="api-antibot">

							<?=$arResult['FORM_TITLE']?>
							<? if($arParams['DISPLAY_FIELDS']): ?>
								<? foreach($arParams['DISPLAY_FIELDS'] as $FIELD): ?>
									<?
									$arField = $arParams['FORM_FIELDS'][ $FIELD ];

									$INPUT_NAME        = !empty($arParams[ 'LANG_' . $FIELD ]) ? $arParams[ 'LANG_' . $FIELD ] : $arField['NAME'];
									$INPUT_PLACEHOLDER = ($arParams['USE_PLACEHOLDER'] || $arParams['HIDE_FIELD_NAME']) ? ' placeholder="' . $INPUT_NAME . (($arParams["REQUIRED_FIELDS"] && in_array($FIELD, $arParams["REQUIRED_FIELDS"])) ? ' *' : '') . '"' : '';
									$INPUT_ASTERISK    = ':<span class="api-asterisk">*</span>';

									$INPUT_ROW_ID = $FORM_ID . '_ROW_' . $FIELD;
									$INPUT_ID     = $FORM_ID . '_FIELD_' . $FIELD;
									$INPUT_CLASS  = 'api_' . ToLower($arField['TYPE']);


									if($arParams["REQUIRED_FIELDS"] && in_array($FIELD, $arParams["REQUIRED_FIELDS"])) {
										$INPUT_CLASS .= ' required';
									}
									else
										$INPUT_ASTERISK = ':';


									$INPUT_CLASS = trim($INPUT_CLASS . ' ' . $FIELD_SIZE);
									$INPUT_NAME  = $arParams['HIDE_ASTERISK'] ? $INPUT_NAME : $INPUT_NAME . $INPUT_ASTERISK;
									?>
									<div id="<?=$INPUT_ROW_ID?>" class="<?=$INPUT_ROW_CLASS;?>">
										<? if(!$arParams['HIDE_FIELD_NAME']): ?>
											<label class="<?=$INPUT_LABEL_CLASS?>">
												<span class="api-label"><?=$INPUT_NAME?></span>
											</label>
										<? endif; ?>
										<div class="<?=$INPUT_CONTROLS_CLASS?>">
											<?
											if($arField['TYPE'] == 'TEXTAREA'): ?>
												<textarea name="FIELDS[<?=$FIELD?>]"
												          id="<?=$INPUT_ID?>" <?=$INPUT_PLACEHOLDER;?>
												          rows="<?=$arParams['FORM_TEXTAREA_ROWS']?>"
												          class="<?=$INPUT_CLASS;?>"><?=$arResult['FIELDS'][ $FIELD ]?></textarea>
											<? elseif($arField['TYPE'] == 'PASSWORD'): ?>
												<input type="password"
												       id="<?=$INPUT_ID?>"
												       name="FIELDS[<?=$FIELD?>]" <?=$INPUT_PLACEHOLDER;?>
												       value="<?=$arResult['FIELDS'][ $FIELD ]?>"
												       class="<?=$INPUT_CLASS;?>">
											<? elseif($arField['TYPE'] == 'SELECT'): ?>
												<select name="FIELDS[<?=$FIELD?>]" id="<?=$INPUT_ID?>" <?=$INPUT_PLACEHOLDER;?> class="<?=$INPUT_CLASS;?>">
													<option value=""><?=Loc::getMessage('API_FEX_TPL_OPTION_SELECT')?></option>
													<? if($arField['VALUES']): ?>
														<? foreach($arField['VALUES'] as $key => $value): ?>
															<option value="<?=$value?>"><?=$value?></option>
														<? endforeach; ?>
													<? endif ?>
												</select>
											<? elseif($arField['TYPE'] == 'CHECKBOX'): ?>
													<? if($arField['VALUES']): ?>
														<? foreach($arField['VALUES'] as $key => $value): ?>
														<label>
															<input type="checkbox" name="FIELDS[<?=$FIELD?>][]" value="<?=$value?>">
															<span><?=$value?></span>
														</label>
														<? endforeach; ?>
													<? endif ?>
											<? elseif($arField['TYPE'] == 'RADIO'): ?>
													<? if($arField['VALUES']): ?>
														<? foreach($arField['VALUES'] as $key => $value): ?>
														<label>
															<input type="radio" name="FIELDS[<?=$FIELD?>]" value="<?=$value?>">
															<span><?=$value?></span>
														</label>
														<? endforeach; ?>
													<? endif ?>
											<? else: ?>
												<input type="text"
												       id="<?=$INPUT_ID?>"
												       name="FIELDS[<?=$FIELD?>]" <?=$INPUT_PLACEHOLDER;?>
												       value="<?=$arResult['FIELDS'][ $FIELD ]?>"
												       class="<?=$INPUT_CLASS;?>">
											<? endif ?>
											<div class="api-field-error"></div>
										</div>
									</div>
								<? endforeach; ?>
							<? endif ?>

							<? if($arParams['USE_EULA'] && $arParams['MESS_EULA']): ?>
								<div class="uk-form-row api-rules">
									<div class="uk-form-controls">
										<label class="api-rules-label">
											<input type="checkbox" name="EULA_ACCEPTED" value="Y" class="api-field" <?=$arResult['EULA_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
											<div class="api-rules-title"><?=$arParams['MESS_EULA']?></div>
										</label>
										<div class="api-rules-error api-eula-error"><?=$arParams['MESS_EULA_CONFIRM']?></div>
									</div>
								</div>
							<? endif ?>

							<? if($arParams['USE_PRIVACY'] && $arParams['MESS_PRIVACY']): ?>
								<div class="uk-form-row api-rules">
									<div class="uk-form-controls">
										<label class="api-rules-label">
											<input type="checkbox" name="PRIVACY_ACCEPTED" value="Y" class="api-field" <?=$arResult['PRIVACY_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
											<div class="api-rules-title">
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

							<div id="<?=$FORM_ID?>_ROW_BUTTON" class="<?=$INPUT_ROW_CLASS?> group-button">
								<? if(!$arParams['HIDE_FIELD_NAME']): ?>
									<label class="<?=$INPUT_LABEL_CLASS?>">&nbsp;</label>
								<? endif; ?>
								<div class="<?=$INPUT_CONTROLS_CLASS?>">
									<button id="API_FEX_FORM_SUBMIT_<?=$FORM_ID?>"
									        type="submit"
									        name="API_FEX_SUBMIT_BUTTON"
									        class="<?=$arParams['FORM_SUBMIT_CLASS'];?><?=$BUTTON_SIZE?>"
									        style="<?=$arParams['FORM_SUBMIT_STYLE'];?>"
									        value="<?=$FORM_ID?>"><i class="api-icon-load"></i> <?=$arParams['FORM_SUBMIT_VALUE'];?>
									</button>
									<div class="api-field-warning"></div>
								</div>
							</div>
						</form>
					</div>
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

<?
ob_start();
?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {

			$.fn.apiFeedbackex({
				wrapperId: '#API_FEX_<?=$FORM_ID?>',
				formId: '#<?=$FORM_ID?>',
				params: {
					siteId: '<?=SITE_ID?>',
					sessid: '<?=bitrix_sessid()?>',
				},
				use_eula: '<?=$arParams['USE_EULA']?>',
				use_privacy: '<?=$arParams['USE_PRIVACY']?>',
			});

			<?if($arParams['USE_MODAL']):?>
			$.fn.apiModal({
				id: '<?=$arParams['MODAL_ID']?>'
			});
			<?endif?>

			<?if($arParams['USE_FLATPICKR']):?>
			var flatpickr_date_cfg = {
				enableTime: false,
				allowInput: true,
				time_24hr: true,
				dateFormat: 'd.m.Y',
				//minDate: 'today',
				minuteIncrement: 10,
				locale: 'ru',
				plugins: [new confirmDatePlugin({})]
			};
			$('#<?=$FORM_ID?>').find('.api_date').flatpickr(flatpickr_date_cfg);

			var flatpickr_datetime_cfg = {
				enableTime: true,
				allowInput: true,
				time_24hr: true,
				dateFormat: 'd.m.Y H:i:S',
				//minDate: 'today',
				minuteIncrement: 10,
				locale: 'ru',
				plugins: [new confirmDatePlugin({})]
			};
			$('#<?=$FORM_ID?>').find('.api_date_time').flatpickr(flatpickr_datetime_cfg);
			<?endif?>

		});
	</script>
<?
$script = ob_get_contents();
ob_end_clean();
Asset::getInstance()->addString($script, true, AssetLocation::AFTER_JS);
?>