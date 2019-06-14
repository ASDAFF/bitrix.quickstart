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
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

$FORM_ID           = trim($arParams['UNIQUE_FORM_ID']);
$template_style    = $FORM_ID;
$FORM_AUTOCOMPLETE = $arParams['FORM_AUTOCOMPLETE'] ? 'on' : 'off';

$INPUT_ROW_CLASS      = 'uk-form-row';
$INPUT_LABEL_CLASS    = 'uk-form-label';
$INPUT_CONTROLS_CLASS = 'uk-form-controls';
$FIELD_SIZE           = $arParams['FIELD_SIZE'] ? ' uk-form-' . $arParams['FIELD_SIZE'] : '';
$BUTTON_SIZE          = $arParams['FIELD_SIZE'] ? ' uk-button-' . $arParams['FIELD_SIZE'] : '';
$FIELD_NAME_POSITION  = $arParams['FIELD_NAME_POSITION'] ? ' uk-form-' . $arParams['FIELD_NAME_POSITION'] : ' uk-form-horizontal';

if($arParams['HIDE_FIELD_NAME'])
	$FIELD_NAME_POSITION = ' uk-form-stacked';

$arParams['TEMPLATE_STYLE'] = trim($arParams['TEMPLATE_STYLE']);
if($arParams['TEMPLATE_STYLE'])
{
	$APPLICATION->SetAdditionalCSS($templateFolder . '/' . $arParams['TEMPLATE_STYLE'] . '.css');
	$template_style = $arParams['TEMPLATE_STYLE'];
}

$arResult['FORM_SUBMIT_VALUE'] = (strlen($arParams['FORM_SUBMIT_VALUE']) > 0) ? htmlspecialcharsback($arParams['FORM_SUBMIT_VALUE']) : htmlspecialcharsback(GetMessage("MFT_SUBMIT"));

if($arParams['TITLE_DISPLAY'])
	$arResult['FORM_TITLE'] = '<h' . $arParams['FORM_TITLE_LEVEL'] . ' class="ap-mf-form-title">' . $arParams['FORM_TITLE'] . '</h' . $arParams['FORM_TITLE_LEVEL'] . '>';

?>
<script type="text/javascript">

	jQuery(document).ready(function ($) {

		<?=$arResult['JS_SETTINGS']?>


		///////////////// onAjaxSuccess /////////////////
		BX.addCustomEvent('onAjaxSuccess', function () {

			<?if($arParams['INCLUDE_ICHECK']):?>
			<?=$arResult['ICHECK_SETTINGS']?>
			<?endif;?>

			<?if($arParams['INCLUDE_CHOSEN']):?>
			<?=$arResult['CHOSEN_SETTINGS']?>
			<?endif;?>

			<?if($arParams['INCLUDE_STEPPER']):?>
			if ($("#<?= $FORM_ID ?> .stepper-wrap").length <= 0)
				$("#<?= $FORM_ID ?> input.stepper").stepper();
			<?endif;?>

			<?if($arParams['INCLUDE_INPUTMASK']):?>
			$("#<?= $FORM_ID ?> [data-inputmask]").inputmask();
			<?endif;?>

		});
		///////////////////////////////////////////////////


		$('#UUID-<?= $FORM_ID ?>').on('change keyup', function () {
			if (!$(this).length)
				$(this).val('');
		});

		<? if($arParams['INCLUDE_VALIDATION']): ?>
		$('#<?=$FORM_ID?>').validate({
			submit: {
				settings: {
					trigger: 'click',
					inputContainer: '.uk-form-controls',
					form: 'form#<?=$FORM_ID?>',
					button: '#API_MF_SUBMIT_BUTTON_<?=$FORM_ID?>',
					<?if($arParams['SCROLL_TO_FORM_IF_MESSAGES']):?>
					scrollToError: {
						offset: -100,
						duration: 500
					}
					<?endif?>
				},
				callback: {
					onSubmit: function (form, e) {

						var bSubmit = false;
						if ($('#<?=$FORM_ID?> .group-agreement').length) {
							if ($('#API_MF_AGREEMENT_<?= $FORM_ID ?>').is(':checked') == false) {
								alert('<?=$arParams['AGREEMENT_ERROR']?>');
							} else {
								bSubmit = true;
							}
						} else {
							bSubmit = true;
						}

						if (bSubmit) {
							var api_mf_ajax_form = BX('<?=$FORM_ID?>');
							BX.showWait();
							BX.ajax.submit(api_mf_ajax_form,function(data){
								$('#API-MF-AJAX-<?=$FORM_ID?>').html(data);
								BX.closeWait();
								BX.onCustomEvent(api_mf_ajax_form, 'onAjaxSuccess');
							});
						}

						e.preventDefault();
					}
				},
			},
			dynamic: {
				settings: {
					trigger: "focusout"
				},
				callback: {
					onSuccess: function (node, input, keyCode) {
						if ($(input).val()) {
							$(input).removeClass('uk-form-danger').addClass('uk-form-success');
						}
					},
					onError: function (node, input, keyCode, error) {
						$(input).removeClass('uk-form-success').addClass('uk-form-danger');
					}
				}
			},
			messages: {
				<?
				if(!empty($arParams['VALIDATION_MESSAGES']))
				{
					foreach($arParams['VALIDATION_MESSAGES'] as $val)
					{
						if($val === end($arParams['VALIDATION_MESSAGES']))
							echo htmlspecialcharsback($val);
						else
							echo htmlspecialcharsback($val) . ',' . "\n";
					}
					unset($val);
				}
				?>
			}
		});
		<? else: ?>
		$('#API_MF_SUBMIT_BUTTON_<?=$FORM_ID?>').on('click', function () {

			var bSubmit = false;
			if ($('#<?=$FORM_ID?> .group-agreement').length) {
				if ($('#API_MF_AGREEMENT_<?= $FORM_ID ?>').is(':checked') == false) {
					alert('<?=$arParams['AGREEMENT_ERROR']?>');
					return false;
				} else {
					bSubmit = true;
				}
			} else {
				bSubmit = true;
			}

			if (bSubmit) {
				var api_mf_ajax_form = BX('<?=$FORM_ID?>');
				BX.showWait();
				BX.ajax.submit(api_mf_ajax_form,function(data){
					$('#API-MF-AJAX-<?=$FORM_ID?>').html(data);
					BX.closeWait();
					BX.onCustomEvent(api_mf_ajax_form, 'onAjaxSuccess');
				});
			}
		});
		<? endif; ?>


		<?if($arParams['INCLUDE_STEPPER']):?>
		$("#<?= $FORM_ID ?> input.stepper").stepper();
		<?endif;?>

		<?if($arParams['INCLUDE_INPUTMASK']):?>
		$("#<?= $FORM_ID ?> [data-inputmask]").inputmask();
		<?endif;?>

		<? if($arParams['INCLUDE_PLACEHOLDER']): ?>
		$('#<?= $FORM_ID ?> input, #<?= $FORM_ID ?> textarea').placeholder();
		<? endif; ?>

		<? if($arParams['INCLUDE_AUTOSIZE']): ?>
		window.jQuery.fn.autosize = function () {
			return autosize(this);
		};
		$('#<?= $FORM_ID ?> textarea').autosize();
		<? endif; ?>

		<? if($arParams['INCLUDE_FORM_STYLER']): ?>
		$('#<?= $FORM_ID ?> input[type="checkbox"], #<?= $FORM_ID ?> input[type="radio"]').styler();
		<? endif; ?>

		<? if($arParams['SCROLL_TO_FORM_IF_MESSAGES'] && $arResult['MESSAGE']): ?>
		$('html, body').animate({
			scrollTop: $("#API-MF-<?= $FORM_ID ?>").offset().top
		}, <?=$arParams['SCROLL_TO_FORM_SPEED'];?>);
		<? endif; ?>
	});

	function fileStringClone_<?=$FORM_ID?>(name,max) {
		var length = $('#<?= $FORM_ID ?> .group-' + name + ' .api-mf-file').length;
		var api_mf_file = $('#<?= $FORM_ID ?> .group-' + name + ' .api-mf-file:last');

		if(length < max)
		{
			api_mf_file
				 .clone(true)
				 .insertAfter(api_mf_file)
				 .find('button').attr('onclick', '$(\'#<?= $FORM_ID ?>-' + name + '-' + length + '\').click();')
				 .parents('.api-mf-file').find('span')
				 .attr('id', '<?= $FORM_ID ?>-' + name + '-VALUE-' + length)
				 .text('<?= GetMessage('MSG_FILE_NOT_SELECT');?>')
				 .parents('.api-mf-file').find('input')
				 .attr('id', '<?= $FORM_ID ?>-' + name + '-' + length)
				 .attr('onchange', '$(\'#<?= $FORM_ID ?>-' + name + '-VALUE-' + length + '\').text(this.value);');
		}
		else
		{
			api_mf_file.next().hide();
		}
	}

</script>
<div class="<?=$arParams['FORM_CLASS']?> api-mainfeedback tpl-modal tpl-modal-<?=$template_style;?>">
	<? if($arParams['INCLUDE_CSSMODAL'] && $arParams['MODAL_HEADER_HTML']): ?>
		<?=$arParams['MODAL_BUTTON_HTML']?>
		<?=$arParams['MODAL_HEADER_HTML']?>
	<? endif ?>
	<div id="API-MF-<?=$FORM_ID?>">
		<?=$arResult['FORM_TITLE']?>

		<? if($_POST["is_api_mf_ajax"] != "Y"): ?>
		<form id="<?=$FORM_ID?>"
		      class="uk-form <?=$FIELD_NAME_POSITION?>"
		      name="<?=$FORM_ID?>"
		      enctype="multipart/form-data"
		      method="POST"
		      action="<?=POST_FORM_ACTION_URI;?>"
		      autocomplete="<?=$FORM_AUTOCOMPLETE?>">
			<?=bitrix_sessid_post()?>

			<input type="hidden" name="UNIQUE_FORM_ID" value="<?=$FORM_ID?>">
			<input type="hidden" name="API_MF_HIDDEN_SUBMIT" value="<?=$FORM_ID?>">
			<input type="hidden" name="API_MF_HIDDEN_PARAMS" value="<?=base64_encode(serialize($arResult['DEFAULT_PARAMS']))?>">
			<input type="hidden" name="is_api_mf_ajax" id="is_api_mf_ajax" value="Y">

			<? if($arParams['USE_HIDDEN_PROTECTION']): ?>
				<input type="text"
				       name="ANTIBOT[NAME]"
				       value="<?=$arResult['ANTIBOT']['NAME'];?>"
				       class="api-mf-antibot">
			<? endif; ?>


			<div id="API-MF-AJAX-<?=$FORM_ID?>" class="<?=$INPUT_ROW_CLASS;?>">
				<? else: ?>
					<?
					$APPLICATION->RestartBuffer();
					?>
					<script type="text/javascript" src="/bitrix/js/main/jquery/jquery-2.1.3.min.min.js"></script>
				<? endif; ?>
				<?
				$bHideForm = false;
				if($arResult['MESSAGE']['SUCCESS'])
				{
					//GOALS_SETTINGS
				if($arResult['GOALS_SETTINGS']){
					?>
					<script type="text/javascript">
						<?=$arResult['GOALS_SETTINGS']?>
					</script>
				<?
				}

				//HIDE_FORM_AFTER_SEND
				if($arParams['HIDE_FORM_AFTER_SEND'])
				{
				$bHideForm = true;
				?>
					<script type="text/javascript">
						top.BX("API-MF-BUTTONS-<?= $FORM_ID ?>").remove();
						top.BX("API-MF-AGREEMENT-<?= $FORM_ID ?>").remove();
					</script>
				<?
				}

				//USE_AGREEMENT
				if($arParams['USE_AGREEMENT'])
				{
				?>
					<script type="text/javascript">
						top.BX('API_MF_AGREEMENT_<?= $FORM_ID ?>').checked = false;
					</script>
					<?
				}
				}
				?>
				<? if($arResult['JS_REDIRECT']): ?>
					<script type="text/javascript">
						<?=$arResult['JS_REDIRECT']?>
					</script>
				<? endif ?>


				<? if($arResult['MESSAGE']['WARNING'] || $arResult['MESSAGE']['SUCCESS']): ?>
					<?
					if($arResult['MESSAGE']['SUCCESS'])
						$arMessages['SUCCESS'] = $arResult['MESSAGE']['SUCCESS'];
					else
						$arMessages['WARNING'] = $arResult['MESSAGE']['WARNING'];
					?>
					<? foreach($arMessages as $messCode => $arMessVal): ?>
						<div class="uk-alert uk-alert-<?=ToLower($messCode);?>">
							<span></span>
							<div><?=implode('<br>', $arMessVal);?></div>
						</div>
					<? endforeach; ?>
				<? endif; ?>

				<? if($arParams['OK_TEXT_AFTER'] && $arResult['MESSAGE']['SUCCESS']): ?>
					<div class="api-ok-text-after uk-form-row"><?=$arParams['OK_TEXT_AFTER'];?></div>
				<? endif; ?>


				<? if(!$bHideForm): ?>
					<? if($arParams['FORM_TEXT_BEFORE']): ?>
						<div class="api-mf-form-text-before"><?=$arParams['FORM_TEXT_BEFORE'];?></div>
					<? endif; ?>
					<div class="<?=$INPUT_ROW_CLASS?>" id="API-MF-FIELDS-<?=$FORM_ID?>">
						<?
						//EXECUTE ONLY HIDDEN CUSTOM FIELDS
						if(!empty($arParams['CUSTOM_FIELDS'])):?>
							<?
							foreach($arParams['CUSTOM_FIELDS'] as $fk => $FIELD)
							{
								$arFields = explode('@', $FIELD);
								$type     = trim(end(explode('=', $arFields[2])));
								$name     = trim(end(explode('=', $arFields[3])));

								if($type == "hidden")
								{
									?>
									<input type="hidden"
									       name="<?=$name?>"
									       value="<?=$arResult[ $name ]?>"
									       class="api-mf-hidden-input">
									<?
								}
							}
							unset($FIELD);
							?>
						<? endif; ?>

						<? if(count($arParams['BRANCH_FIELDS'])>0 && $arParams["BRANCH_ACTIVE"] == "Y"): ?>
							<?
							$INPUT_ASTERISK       = '<span class="api-mf-asterisk">*</span>';
							$INPUT_CLASS          = '';
							$INPUT_MESSAGE_DANGER = '';
							$DEFAULT_OPTION_TEXT = ($arParams['HIDE_FIELD_NAME'] ? $arParams["BRANCH_BLOCK_NAME"] : ($arParams['DEFAULT_OPTION_TEXT'] ? $arParams['DEFAULT_OPTION_TEXT'] : $arParams["BRANCH_BLOCK_NAME"]));

							if($arResult['MESSAGE']['DANGER'])
							{
								if($arResult['MESSAGE']['DANGER']['BRANCH'])
								{
									$INPUT_CLASS .= ' uk-form-danger';
									$INPUT_MESSAGE_DANGER = '<div class="error-list"><ul><li>' . $arResult['MESSAGE']['DANGER']['BRANCH'] . '</li></ul></div>';
								}
								elseif(empty($arParams["REQUIRED_FIELDS"]) || in_array('BRANCH', $arParams["REQUIRED_FIELDS"]))
								{
									$INPUT_CLASS .= ' uk-form-success';
								}
							}

							$INPUT_CLASS = trim($INPUT_CLASS);
							?>
							<div class="<?=$INPUT_ROW_CLASS;?>">
								<? if(!$arParams['HIDE_FIELD_NAME']): ?>
									<label class="<?=$INPUT_LABEL_CLASS?>"><?=$arParams["BRANCH_BLOCK_NAME"]?><?=$INPUT_ASTERISK?></label>
								<? endif; ?>
								<div class="<?=$INPUT_CONTROLS_CLASS?>">
									<select name="branch" class="<?=$INPUT_CLASS;?>"  data-placeholder="<?=$DEFAULT_OPTION_TEXT?>">
										<? if($DEFAULT_OPTION_TEXT):?>
											<option value=""><?=(!$arParams['INCLUDE_CHOSEN'] ? $DEFAULT_OPTION_TEXT : '' )?></option>
										<?endif; ?>
										<? foreach($arParams['BRANCH_FIELDS'] as $branchId => $arBranchFields): ?>
											<? $arBranch = explode('###', $arBranchFields); ?>
											<? if(count($arBranch)): ?>
												<option value="<?=$branchId?>"
													 <? if(strlen($arResult['BRANCH_NAME']) && (int)$arResult['BRANCH_NAME'] == $branchId): ?> selected="selected"<? endif; ?>><?=$arBranch[0]?></option>
											<? endif ?>
										<? endforeach ?>
									</select>
									<?=$INPUT_MESSAGE_DANGER?>
								</div>
							</div>
							<? if($arParams["MSG_PRIORITY"] == "Y"): ?>
								<div class="<?=$INPUT_ROW_CLASS;?>">
									<? if(!$arParams['HIDE_FIELD_NAME']): ?>
										<label class="<?=$INPUT_LABEL_CLASS?>"><?=$arParams["MSG_PRIORITY_BLOCK_NAME"];?></label>
									<? endif; ?>
									<div class="<?=$INPUT_CONTROLS_CLASS?>">
										<select name="msg_priority">
											<option value="5 (Lowest)"<? if($arResult['MSG_PRIORITY'] == '5 (Lowest)'): ?> selected="selected"<? endif; ?>><?=GetMessage("MSG_PRIORITY_5")?></option>
											<option value="3 (Normal)"<? if($arResult['MSG_PRIORITY'] == '3 (Normal)' || !$_POST['msg_priority']): ?> selected="selected"<? endif; ?>><?=GetMessage("MSG_PRIORITY_3")?></option>
											<option value="1 (Highest)"<? if($arResult['MSG_PRIORITY'] == '1 (Highest)'): ?> selected="selected"<? endif; ?>><?=GetMessage("MSG_PRIORITY_1")?></option>
										</select>
									</div>
								</div>
							<? endif ?>
						<? endif ?>

						<?
						//EXECUTE DISPLAY_FIELDS
						if(count($arParams['DISPLAY_FIELDS']) > 0)
						{
							foreach($arParams['DISPLAY_FIELDS'] as $FIELD)
							{
								$INPUT_NAME           = !empty($arParams[ 'USER_' . $FIELD ]) ? $arParams[ 'USER_' . $FIELD ] : GetMessage('MFP_' . $FIELD);
								$INPUT_PLACEHOLDER    = ($arParams['INCLUDE_PLACEHOLDER'] || $arParams['HIDE_FIELD_NAME']) ? ' placeholder="' . $INPUT_NAME . ((empty($arParams["REQUIRED_FIELDS"]) || in_array($FIELD, $arParams["REQUIRED_FIELDS"])) ? ' *' : '') . '"' : '';
								$INPUT_ASTERISK       = ':<span class="api-mf-asterisk">*</span>';
								$INPUT_CLASS          = '';
								$INPUT_MESSAGE_DANGER = '';

								if($arResult['MESSAGE']['DANGER'])
								{
									if($arResult['MESSAGE']['DANGER'][ $FIELD ])
									{
										$INPUT_CLASS .= ' uk-form-danger';
										$INPUT_MESSAGE_DANGER = '<div class="error-list"><ul><li>' . $arResult['MESSAGE']['DANGER'][ $FIELD ] . '</li></ul></div>';
									}
									elseif(empty($arParams["REQUIRED_FIELDS"]) || in_array($FIELD, $arParams["REQUIRED_FIELDS"]))
									{
										$INPUT_CLASS .= ' uk-form-success';
									}
								}

								if(empty($arParams["REQUIRED_FIELDS"]) || in_array($FIELD, $arParams["REQUIRED_FIELDS"]))
								{
									$INPUT_CLASS .= ' required';
								}
								else
									$INPUT_ASTERISK = ':';


								$INPUT_CLASS = trim($INPUT_CLASS);
								$INPUT_NAME  = $arParams['HIDE_ASTERISK'] ? $INPUT_NAME : $INPUT_NAME . $INPUT_ASTERISK;

								if($FIELD != 'AUTHOR_MESSAGE' && $FIELD != 'AUTHOR_NOTES')
								{
									?>
									<div class="<?=$INPUT_ROW_CLASS;?>">
										<? if(!$arParams['HIDE_FIELD_NAME']): ?>
											<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
										<? endif; ?>
										<div class="<?=$INPUT_CONTROLS_CLASS?>">
											<input type="text"
											       name="<?=ToLower($FIELD)?>"
											       value="<?=$arResult[ $FIELD ]?>"
												 <?=$INPUT_PLACEHOLDER;?>
												     class="<?=$INPUT_CLASS;?>">
											<?=$INPUT_MESSAGE_DANGER;?>
										</div>
									</div>
									<?
								}
							}
							unset($FIELD);
						}
						?>

						<?

						//EXECUTE CUSTOM FIELDS
						if(!empty($arParams['CUSTOM_FIELDS'])):?>
							<?
							$count = 0;
							foreach($arParams['CUSTOM_FIELDS'] as $fk => $FIELD)
							{
								$count++;
								$arFields             = explode('@', $FIELD);
								$INPUT_NAME           = trim($arFields[0]);
								$INPUT_PLACEHOLDER    = ($arParams['INCLUDE_PLACEHOLDER'] || $arParams['HIDE_FIELD_NAME']) ? ' placeholder="' . $INPUT_NAME . (in_array("required", $arFields) ? ' *' : '') . '"' : '';
								$INPUT_ASTERISK       = ':<span class="api-mf-asterisk">*</span>';
								$INPUT_CLASS          = '';
								$INPUT_MESSAGE_DANGER = '';
								$DEFAULT_OPTION_TEXT = ($arParams['HIDE_FIELD_NAME'] ? $INPUT_NAME : ($arParams['DEFAULT_OPTION_TEXT'] ? $arParams['DEFAULT_OPTION_TEXT'] : $INPUT_NAME));
								unset($arFields[0]);

								if($arResult['MESSAGE']['DANGER'])
								{
									if($arResult['MESSAGE']['DANGER'][ $fk ])
									{
										$INPUT_CLASS .= ' uk-form-danger';
										$INPUT_MESSAGE_DANGER = '<div class="error-list"><ul><li>' . $arResult['MESSAGE']['DANGER'][ $fk ] . '</li></ul></div>';
									}
									elseif(in_array("required", $arFields))
									{
										$INPUT_CLASS .= ' uk-form-success';
									}
								}

								if(in_array("required", $arFields))
									$INPUT_CLASS .= ' required';
								else
									$INPUT_ASTERISK = ':';


								$group     = 0;
								$ATTR_DATA = $ATTR_CLASS = $class = '';


								//v3.4.0
								$optional = ($arParams['INCLUDE_ICHECK'] && in_array('optional',$arFields));
								$inline   = (in_array('inline',$arFields) ? 'api-inline-block' : '');

								$rowId      =  'form_row_' . $FORM_ID . '_' . $fk;
								$optionalId =  'optional_' . $FORM_ID . '_' . $fk;

								$rowClass      =  'form_row_' . $FORM_ID . '_' . $fk;
								$optionalClass =  'optional_' . $FORM_ID . '_' . $fk;
								//\\v3.4.0


								foreach($arFields as $arField)
								{
									$arData = array();
									if(substr($arField, 0, 5) == "data-")
									{
										$arField = htmlspecialcharsback($arField);
										$arData  = explode('=', $arField, 2);
										$ATTR_DATA .= ' ' . trim($arData[0]) . '="' . trim($arData[1]) . '"';
									}
									elseif(substr($arField, 0, 5) == "class")
									{
										$arData = explode('=', $arField);
										$class  = trim($arData[1]);
									}
									elseif(substr($arField, 0, 11) == "placeholder")
									{
										$arData = explode('=', $arField);
										if($ATTR_PLACEHOLDER = trim($arData[1]))
											$INPUT_PLACEHOLDER = ' placeholder="' . $ATTR_PLACEHOLDER . '"';
									}
									elseif(substr($arField, 0, 5) == "group")
									{
										$arData = explode('=', $arField);
										if($arData[1])
											$group = intval($arData[1]);
									}
								}
								unset($arData);
								unset($arField);


								$ATTR_CLASS = 'class="' . trim($class . $INPUT_CLASS . $FIELD_SIZE) . '"';
								$INPUT_NAME = $arParams['HIDE_ASTERISK'] ? $INPUT_NAME : $INPUT_NAME . $INPUT_ASTERISK;

								//Group fields
								if($arParams['GROUP'][ $group ] && $count == 1)
									echo '<div class="uk-form-row group' . $group . '">';


								switch($arFields[1])
								{
									case "select":

										$size     = 0;
										$values   = array();
										$optgroup = false;
										$name     = trim(end(explode('=', $arFields[2])));
										$multiple = in_array("multiple", $arFields) ? ' multiple="multiple"' : false;

										foreach($arFields as $arField)
										{
											if(substr($arField, 0, 7) == "values=")
											{
												if(strpos($arField, '##') === false)
													$values = explode("#", substr($arField, 7));
												else
												{
													$optgroup = true;
													$values   = explode("##", substr($arField, 7));
												}
											}

											if(substr($arField, 0, 4) == "size")
											{
												$size = (int)end(explode("=", $arField));
											}
										}

										if(!$size)
										{
											if($multiple)
											{
												if(!empty($values))
													$size = count($values);

												if($size > 20)
													$size = round($size / 2, 0, PHP_ROUND_HALF_UP);

												$size += 1;
											}
											else
												$size = 1;
										}

										unset($arField);
										?>
									<div class="<?=$INPUT_ROW_CLASS?>">
										<? if(!$arParams['HIDE_FIELD_NAME']): ?>
										<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
									<? endif; ?>
										<div class="<?=$INPUT_CONTROLS_CLASS?>">
											<select name="<?=$multiple ? $name . '[]' : $name;?>"
											        data-<?=trim($INPUT_PLACEHOLDER)?>
											        size="<?=$size?>" <?=$multiple?> <?=$ATTR_CLASS?> <?=$ATTR_DATA?>>
												<? if($DEFAULT_OPTION_TEXT):?>
													<option value=""><?=(!$arParams['INCLUDE_CHOSEN'] ? $DEFAULT_OPTION_TEXT : '' )?></option>
												<?endif; ?>
												<?
												if($optgroup)
												{
												foreach($values as $k2 => $v2)
												{
												if(strpos($v2, '#') === false)
												{
												?>
												<optgroup label="<?=$v2;?>"><?
													}
													else
													{
														$arValues = explode('#', $v2);
														foreach($arValues as $val)
														{
															if(is_array($arResult[ $name ]) && in_array($val, $arResult[ $name ]))
																$selected = true;
															elseif($arResult[ $name ] === $val)
																$selected = true;
															else
																$selected = false;
															?>
															<option<? if($selected): ?> selected<? endif; ?>
																 value="<?=$val;?>"><?=$val;?></option>
															<?
															if($val == end($arValues))
																echo '</optgroup>';
														}
													}
													}
													}
													else
													{
														foreach($values as $k1 => $v)
														{
															if(is_array($arResult[ $name ]) && in_array($v, $arResult[ $name ]))
																$selected = true;
															elseif($arResult[ $name ] === $v)
																$selected = true;
															else
																$selected = false;
															?>
															<option<? if($selected): ?> selected<? endif; ?>
															value="<?=$v?>"><?=$v?></option><?
														}
													}
													?>
											</select>
											<?=$INPUT_MESSAGE_DANGER?>
										</div>
										</div><?
										break;

									case "input":

										$value  = '';
										$type = trim(end(explode('=', $arFields[2])));
										$name = trim(end(explode('=', $arFields[3])));

										$values = array();
										foreach($arFields as $arField)
										{
											//For select, checkbox, radio
											if(substr($arField, 0, 7) == "values=")
												$values = explode("#", substr($arField, 7));

											//For text
											if(substr($arField, 0, 6) == "value=")
												$value = substr($arField, 6);
										}
										unset($arField);

										if($type == "checkbox")
										{
											$ATTR_CLASS = str_replace('uk-form-large', '', $ATTR_CLASS);
											?>
											<div class="<?=$INPUT_ROW_CLASS?><? if($value):?> one-checkbox<?endif; ?>"  id="<?=$rowId?>">
												<? if(!$arParams['HIDE_FIELD_NAME']):?>
													<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
												<?endif; ?>
												<div class="<?=$INPUT_CONTROLS_CLASS?> <?=$values ? $INPUT_CLASS : ''?> uk-form-controls-text<? if($values):?> group-checkbox<?endif; ?>">
													<? if($values):?>
														<? if($arParams['HIDE_FIELD_NAME']):?>
															<label><?=$INPUT_NAME?></label>
														<?endif; ?>
														<? foreach($values as $k2 => $v):?>
															<label for="<?=$FORM_ID?>_<?=$name?>_<?=$fk?>_<?=$k2?>" class="<?=$inline?>">
																<input id="<?=$FORM_ID?>_<?=$name?>_<?=$fk?>_<?=$k2?>"
																       type="checkbox"
																       name="<?=$name?>[]"
																       value="<?=$v?>"
																	 <? if(is_array($arResult[ $name ]) && in_array($v, $arResult[ $name ])): ?> checked="checked"<?endif ?>
																	 <?=$ATTR_CLASS?>
																	 <?=$ATTR_DATA?>>
																<?=$v?>
															</label>
														<?endforeach ?>
														<?
													elseif($value):?>
														<label for="<?=$FORM_ID?>_<?=$name?>_<?=$fk?>">
															<? if($arParams['HIDE_FIELD_NAME']):?><?=$INPUT_NAME?><?endif?>
															<input id="<?=$FORM_ID?>_<?=$name?>_<?=$fk?>"
															       type="checkbox"
															       name="<?=$name?>"
															       value="<?=$value?>"
																 <? if($arResult[ $name ] == $value): ?> checked="checked"<?endif ?>
																 <?=$ATTR_CLASS?>
																 <?=$ATTR_DATA?>>
														</label>
													<?endif; ?>
													<?=$INPUT_MESSAGE_DANGER?>
												</div>
											</div>
											<?
										}
										elseif($type == "radio")
										{
											$ATTR_CLASS = str_replace('uk-form-large', '', $ATTR_CLASS);
											?>
											<div class="<?=$INPUT_ROW_CLASS?>" id="<?=$rowId?>">
												<? if(!$arParams['HIDE_FIELD_NAME']):?>
													<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
												<?endif; ?>
												<div class="<?=$INPUT_CONTROLS_CLASS?> <?=$INPUT_CLASS?> uk-form-controls-text group-radio">
													<? if($arParams['HIDE_FIELD_NAME']):?>
														<label class="<?=$INPUT_LABEL_CLASS?> api-inline-block"><?=$INPUT_NAME?></label>
													<?endif; ?>
													<?if($optional):?>
														<script>
															jQuery(document).ready(function ($) {
																$('#<?=$optionalId?>').on('ifChanged', function(event){
																	if($('#<?=$rowId?> .uk-form-optional').is(':visible'))
																	{
																		$('#<?=$rowId?> input').iCheck('uncheck');
																		$('#<?=$rowId?> .uk-form-optional').hide(200);
																	}
																	else
																		$('#<?=$rowId?> .uk-form-optional').show(200);
																});
															});
														</script>
														<div class="optional-checkbox<? if($arParams['HIDE_FIELD_NAME']):?> api-inline-block<?endif; ?>">
															<input type="checkbox" id="<?=$optionalId?>"<?if(strlen($arResult[ $name ])>0):?> checked<?endif?>>
														</div>
													<?endif?>

													<div class="uk-form-optional" <?if($optional && strlen($arResult[ $name ])==0):?>  style="display:none"<?endif?>>
														<? foreach($values as $k3 => $v):?>
															<label for="<?=$FORM_ID?>_radio_<?=$fk?>_<?=$k3?>" class="<?=$inline?>">
																<input id="<?=$FORM_ID?>_radio_<?=$fk?>_<?=$k3?>"
																       type="radio"
																       name="<?=$name?>"
																       value="<?=$v?>"
																	 <? if($arResult[ $name ] == $v): ?> checked="checked"<?endif ?>
																	 <?=$ATTR_CLASS?>
																	 <?=$ATTR_DATA?>>
																<?=$v?>
															</label>
														<?endforeach ?>
													</div>
													<?=$INPUT_MESSAGE_DANGER?>
												</div>
											</div>
											<?
										}
										elseif($type == "date" || $type == "datetime")
										{
											$size          = 1;
											$bDateMultiple = false;
											$bShowTime     = ($type == "datetime") ? true : false;

											foreach($arFields as $arField)
											{
												if(substr($arField, 0, 4) == "size")
												{
													$arrExp = explode("=", $arField);
													if(intval($arrExp[1]) >= 2)
													{
														$size          = $arrExp[1];
														$bDateMultiple = true;
													}
												}
											}
											unset($arField);
											?>
											<div class="<?=$INPUT_ROW_CLASS?>">
												<? if(!$arParams['HIDE_FIELD_NAME']):?>
													<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
												<?endif; ?>
												<div class="<?=$INPUT_CONTROLS_CLASS?><?=($bDateMultiple ? ' group-' . $type : '')?>">
													<? for($i = 0; $i < $size; $i++):?>
														<? $INPUT_ID = $FORM_ID . '_' . $name . '_' . $fk . '_' . $i; ?>
														<div class="uk-form-controls-condensed">
															<input type="text"
															       id="<?=$INPUT_ID?>"
															       name="<?=($bDateMultiple) ? $name . '[]' : $name;?>"
															       value="<?=($bDateMultiple) ? $arResult[ $name ][ $i ] : $arResult[ $name ]?>"
																 <?=$INPUT_PLACEHOLDER?>
																 <?=$ATTR_CLASS?>
																 <?=$ATTR_DATA?>>
															<? $APPLICATION->IncludeComponent(
																 "bitrix:main.calendar",
																 "",
																 Array(
																		"SHOW_INPUT"         => "N",
																		"FORM_NAME"          => "api_feedback_form",
																		"INPUT_NAME"         => $INPUT_ID,
																		"INPUT_NAME_FINISH"  => "",
																		"INPUT_VALUE"        => "",
																		"INPUT_VALUE_FINISH" => "",
																		"SHOW_TIME"          => "Y",
																		"HIDE_TIMEBAR"       => $bShowTime ? 'N' : 'Y',
																 ),
																 null,
																 Array('HIDE_ICONS' => 'Y')
															); ?>
														</div>
													<?endfor; ?>
													<?=$INPUT_MESSAGE_DANGER?>
												</div>
											</div>
											<?
										}
										elseif($type == "coupon")
										{
											$button_value = '';
											foreach($arFields as $arField)
											{
												if(substr($arField, 0, 12) == "button_value")
												{
													$button_value = str_replace('button_value=', '', $arField);
												}
											}
											unset($arField);
											?>
											<div class="<?=$INPUT_ROW_CLASS?> api-mf-coupon">
												<? if(!$arParams['HIDE_FIELD_NAME']):?>
													<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
												<?endif; ?>
												<div class="<?=$INPUT_CONTROLS_CLASS?>">
													<div class="uk-form-controls-condensed">
														<input type="text"
														       readonly=""
														       id="UUID-<?=$FORM_ID?>"
														       name="<?=$name?>"
														       value="<?=$arResult[ $name ]?>"
															 <?=$INPUT_PLACEHOLDER?>
															 <?=$ATTR_CLASS?>
															 <?=$ATTR_DATA?>>
														<? if(!$arResult[ $name ]):?>
															<? if($button_value && $arResult['UUID']):?>
																<button type="button"
																        onclick="$('#UUID-<?=$FORM_ID?>').val('<?=$arResult['UUID'];?>'); $(this).detach();"
																        class="uk-button <?=$BUTTON_SIZE?> api-mf-button-uuid"><?=$button_value;?></button>
															<?endif; ?>
														<?endif; ?>
														<?=$INPUT_MESSAGE_DANGER?>
													</div>
												</div>
											</div>
											<?
										}
										elseif($type == "file")
										{
											$size     = 0;
											$max      = $arParams['MAX_FILE_UPLOADS'];
											$multiple = in_array("multiple", $arFields) ? ' multiple="multiple"' : false;

											foreach($arFields as $arField)
											{
												if(substr($arField, 0, 4) == "size")
												{
													$size = (int)end(explode("=", $arField));
												}

												if(substr($arField, 0, 3) == "max")
												{
													$max_val = (int)end(explode("=", $arField));
													if($max_val)
														$max = $max_val;
												}
											}

											if(!$size)
											{
												if($multiple)
												{
													if(!empty($values))
														$size = count($values);

													if($size > 20)
														$size = round($size / 2, 0, PHP_ROUND_HALF_UP);

													$size += 1;
												}
												else
													$size = 1;
											}
											unset($arField);
											?>
											<div class="<?=$INPUT_ROW_CLASS . ' group-' . $name?>">
												<? for($i = 0; $i < $size; $i++):?>
													<?
													$inputID   = ($FORM_ID . '-' . $name . '-' . $i);
													$inputName = ($FORM_ID . '-' . $name . '-value-' . $i);

													$INPUT_CLASS          = '';
													$INPUT_MESSAGE_DANGER = '';
													if($arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'])
													{
														if($arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'][ $i ])
														{
															$INPUT_MESSAGE_DANGER = '<div class="error-list"><ul><li>' . $arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'][ $i ] . '</li></ul></div>';
															$INPUT_CLASS .= ' uk-form-danger';
														}
													}
													?>
													<div class="api-mf-file uk-form-controls-condensed">
														<? if(!$arParams['HIDE_FIELD_NAME']):?>
															<label class="<?=$INPUT_LABEL_CLASS?>"></label>
														<?endif; ?>
														<div class="api-mf-file-wrap <?=$INPUT_CONTROLS_CLASS?>">
															<button type="button"
															        class="uk-button uk-button-mini<?=$INPUT_CLASS;?>"
															        onclick="$('#<?=$inputID?>').click()"><?=$arParams['CHOOSE_FILE_TEXT']?></button>
															<span id="<?=$inputName?>" class="api-mf-file-name"><?=GetMessage('MSG_FILE_NOT_SELECT');?></span>
															<input id="<?=$inputID?>"
															       type="file"
															       name="UPLOAD_FILES[]"
															       onchange="$('#<?=$inputName?>').text(this.value);"
																 <?=$ATTR_CLASS?>>
															<?=$INPUT_MESSAGE_DANGER;?>
														</div>
													</div>
												<?endfor ?>
												<?
												unset($inputID, $inputName);
												?>
												<? if($multiple):?>
													<div class="uk-form-controls-condensed">
														<? if(!$arParams['HIDE_FIELD_NAME']):?>
															<label class="<?=$INPUT_LABEL_CLASS?>"></label>
														<?endif; ?>
														<div class="api-mf-file-wrap <?=$INPUT_CONTROLS_CLASS?>">
															<span onclick="fileStringClone_<?=$FORM_ID?>('<?=$name?>',<?=$max?>);" class="api-clone-file-field"><?=GetMessage('ADD_MORE')?></span>
														</div>
													</div>
												<?endif; ?>
												<? if($arParams['SHOW_ATTACHMENT_EXTENSIONS']):?>
													<div class="api-mf-file-string uk-form-controls-condensed">
														<? if(!$arParams['HIDE_FIELD_NAME']):?>
															<label class="<?=$INPUT_LABEL_CLASS?>"> </label>
														<?endif; ?>
														<div class="api-mf-file-wrap api-mf-file-ext <?=$INPUT_CONTROLS_CLASS?>"><?=$arParams['FILE_EXTENSIONS'];?></div>
													</div>
												<?endif; ?>
												<?
												$INPUT_MESSAGE_DANGER = '';
												if($arResult['MESSAGE']['DANGER']['UPLOAD']['MESS']):?>
													<div class="uk-form-controls-condensed">
														<? if(!$arParams['HIDE_FIELD_NAME']):?>
															<label class="<?=$INPUT_LABEL_CLASS?>"> </label>
														<?endif; ?>
														<div class="<?=$INPUT_CONTROLS_CLASS?>">
															<div class="error-list">
																<ul>
																	<? foreach($arResult['MESSAGE']['DANGER']['UPLOAD']['MESS'] as $mess):?>
																		<li><?=$mess?></li>
																	<?endforeach; ?>
																</ul>
															</div>
														</div>
													</div>
												<?endif; ?>
											</div>
											<?
										}
										elseif($type == "hidden")
										{
											/*?>
											<input type="hidden"
													 name="<?=$name?>"
													 value="<?=$arResult[$name]?>"
													 class="api-mf-hidden-input" <?=$ATTR_DATA?>>
											<?*/
										}
										elseif($type == "stepper")
										{
											?>
											<div class="<?=$INPUT_ROW_CLASS?>">
												<? if(!$arParams['HIDE_FIELD_NAME']): ?>
													<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
												<? endif; ?>
												<div class="<?=$INPUT_CONTROLS_CLASS?>">
													<input type="text"
													       name="<?=$name?>"
													       value="<?=$arResult[ $name ]?>"
														 <?=$INPUT_PLACEHOLDER?>
														 <?=$ATTR_CLASS;?>
														 <?=$ATTR_DATA?>>
													<?=$INPUT_MESSAGE_DANGER;?>
												</div>
											</div>
											<?
										}
										elseif($type == "password")
										{
											?>
											<div class="<?=$INPUT_ROW_CLASS?>">
												<? if(!$arParams['HIDE_FIELD_NAME']):?>
													<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
												<?endif; ?>
												<div class="<?=$INPUT_CONTROLS_CLASS?>">
													<input type="<?=$type?>"
													       name="<?=$name?>"
													       value="<?=$arResult[ $name ]?>"
														 <?=$INPUT_PLACEHOLDER?>
														 <?=$ATTR_CLASS;?>
														 <?=$ATTR_DATA?>>
													<?=$INPUT_MESSAGE_DANGER;?>
												</div>
											</div>
											<?
										}
										else
										{
											?>
											<div class="<?=$INPUT_ROW_CLASS?>">
												<? if(!$arParams['HIDE_FIELD_NAME']):?>
													<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
												<?endif; ?>
												<div class="<?=$INPUT_CONTROLS_CLASS?>">
													<input type="<?=$type?>"
													       name="<?=$name?>"
													       value="<?=$arResult[ $name ]?>"
														 <?=$INPUT_PLACEHOLDER?>
														 <?=$ATTR_CLASS;?>
														 <?=$ATTR_DATA?>>
													<?=$INPUT_MESSAGE_DANGER;?>
												</div>
											</div>
											<?
										}
										break;

									case "textarea":

										$cols = $rows = '';
										$name = trim(end(explode('=', $arFields[2])));

										foreach($arFields as $arField)
										{
											$arData = array();
											if(substr($arField, 0, 4) == "cols")
											{
												$arData = explode('=', $arField);
												$cols   = trim($arData[0]) . '="' . trim($arData[1]) . '"';
											}
											elseif(substr($arField, 0, 4) == "rows")
											{
												$arData = explode('=', $arField);
												$rows   = trim($arData[0]) . '="' . trim($arData[1]) . '"';
											}
										}
										unset($arData);
										unset($arField);
										?>
										<div class="<?=$INPUT_ROW_CLASS?>">
											<? if(!$arParams['HIDE_FIELD_NAME']): ?>
												<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
											<? endif; ?>
											<div class="<?=$INPUT_CONTROLS_CLASS?>">
												<textarea name="<?=$name?>"
													 <?=$cols?>
													 <?=$rows?>
													 <?=$INPUT_PLACEHOLDER?>
													 <?=$ATTR_CLASS;?>
													 <?=$ATTR_DATA?>><?=$arResult[ $name ]?></textarea>
												<?=$INPUT_MESSAGE_DANGER;?>
											</div>
										</div>
										<?
										break;

									default:
									{
										if(is_string($INPUT_NAME) && strlen($INPUT_NAME))
											echo '<div class="' . $INPUT_ROW_CLASS . ' api-mf-separator">' . htmlspecialcharsback(rtrim($INPUT_NAME, ':')) . '</div>';
									}
								}

								//Group fields
								if($arParams['GROUP'][ $group ] && $count == $arParams['GROUP'][ $group ])
								{
									echo '</div>';
									$count = 0;
								}
							}
							?>
						<? endif; ?>

						<?
						//EXECUTE DISPLAY_FIELDS
						if(count($arParams['DISPLAY_FIELDS']) > 0)
						{
							foreach($arParams['DISPLAY_FIELDS'] as $FIELD)
							{
								$INPUT_NAME           = !empty($arParams[ 'USER_' . $FIELD ]) ? $arParams[ 'USER_' . $FIELD ] : GetMessage('MFP_' . $FIELD);
								$INPUT_PLACEHOLDER    = $arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $INPUT_NAME . ((empty($arParams["REQUIRED_FIELDS"]) || in_array($FIELD, $arParams["REQUIRED_FIELDS"])) ? ' *' : '') . '"' : '';
								$INPUT_ASTERISK       = ':<span class="api-mf-asterisk">*</span>';
								$INPUT_CLASS          = '';
								$INPUT_MESSAGE_DANGER = '';

								if($arResult['MESSAGE']['DANGER'])
								{
									if($arResult['MESSAGE']['DANGER'][ $FIELD ])
									{
										$INPUT_CLASS .= ' uk-form-danger';
										$INPUT_MESSAGE_DANGER = '<div class="error-list"><ul><li>' . $arResult['MESSAGE']['DANGER'][ $FIELD ] . '</li></ul></div>';
									}
									elseif(empty($arParams["REQUIRED_FIELDS"]) || in_array($FIELD, $arParams["REQUIRED_FIELDS"]))
									{
										$INPUT_CLASS .= ' uk-form-success';
									}
								}

								if(empty($arParams["REQUIRED_FIELDS"]) || in_array($FIELD, $arParams["REQUIRED_FIELDS"]))
								{
									$INPUT_CLASS .= ' required';
								}
								else
									$INPUT_ASTERISK = ':';


								$INPUT_CLASS = trim($INPUT_CLASS);
								$INPUT_NAME  = $arParams['HIDE_ASTERISK'] ? $INPUT_NAME : $INPUT_NAME . $INPUT_ASTERISK;

								if($FIELD == 'AUTHOR_MESSAGE' || $FIELD == 'AUTHOR_NOTES')
								{
									?>
									<div class="<?=$INPUT_ROW_CLASS;?>">
										<? if(!$arParams['HIDE_FIELD_NAME']): ?>
											<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
										<? endif; ?>
										<div class="<?=$INPUT_CONTROLS_CLASS?>">
										<textarea name="<?=ToLower($FIELD)?>"
											 <?=$INPUT_PLACEHOLDER;?>
											        class="<?=$INPUT_CLASS;?>"><?=$arResult[ $FIELD ]?></textarea>
											<?=$INPUT_MESSAGE_DANGER;?>
										</div>
									</div>
									<?
								}
							}
							unset($FIELD);
						}
						?>

						<? if($arParams['SHOW_FILES'] && $arParams['COUNT_INPUT_FILE']): ?>
							<div class="<?=$INPUT_ROW_CLASS?>">
								<? for($i = 0; $i < $arParams['COUNT_INPUT_FILE']; $i++): ?>
									<?
									$INPUT_CLASS          = '';
									$INPUT_MESSAGE_DANGER = '';
									if($arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'])
									{
										if($arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'][ $i ])
										{
											$INPUT_MESSAGE_DANGER = '<div class="error-list"><ul><li>' . $arResult['MESSAGE']['DANGER']['UPLOAD']['FILES'][ $i ] . '</li></ul></div>';
											$INPUT_CLASS .= ' uk-form-danger';
										}
									}
									?>
									<div class="api-mf-file-string uk-form-controls-condensed">
										<? if(!$arParams['HIDE_FIELD_NAME']): ?>
											<label class="<?=$INPUT_LABEL_CLASS?>"><?=$arParams['FILE_DESCRIPTION'][ $i ];?></label>
										<? endif; ?>
										<div class="api-mf-file-wrap <?=$INPUT_CONTROLS_CLASS?>">
											<button type="button"
											        class="uk-button uk-button-mini<?=$INPUT_CLASS;?>"
											        onclick="$('#<?=$FORM_ID?>_FILE_<?=$i?>').click()"><?=$arParams['CHOOSE_FILE_TEXT']?></button>
											<span id="<?=$FORM_ID?>_FILE_NAME_<?=$i?>"
											      class="api-mf-file-name">
												<?=GetMessage('MSG_FILE_NOT_SELECT')?>
											</span>
											<input id="<?=$FORM_ID?>_FILE_<?=$i?>"
											       type="file"
											       name="UPLOAD_FILES[]"
											       onchange="$('#<?=$FORM_ID?>_FILE_NAME_<?=$i?>').text(this.value);"
												 <? if($arParams['SET_ATTACHMENT_REQUIRED']): ?> class="required"<? endif; ?>>
											<?=$INPUT_MESSAGE_DANGER;?>
										</div>
									</div>
								<? endfor; ?>
								<? if($arParams['SHOW_ATTACHMENT_EXTENSIONS']): ?>
									<div class="api-mf-file-string uk-form-controls-condensed">
										<? if(!$arParams['HIDE_FIELD_NAME']): ?>
											<label class="<?=$INPUT_LABEL_CLASS?>"> </label>
										<? endif; ?>
										<div class="api-mf-file-wrap api-mf-file-ext <?=$INPUT_CONTROLS_CLASS?>"><?=$arParams['FILE_EXTENSIONS'];?></div>
									</div>
								<? endif; ?>
								<?
								$INPUT_MESSAGE_DANGER = '';
								if($arResult['MESSAGE']['DANGER']['UPLOAD']['MESS']):?>
									<div class="uk-form-controls-condensed">
										<? if(!$arParams['HIDE_FIELD_NAME']): ?>
											<label class="<?=$INPUT_LABEL_CLASS?>"> </label>
										<? endif; ?>
										<div class="<?=$INPUT_CONTROLS_CLASS?>">
											<div class="error-list">
												<ul>
													<? foreach($arResult['MESSAGE']['DANGER']['UPLOAD']['MESS'] as $mess): ?>
														<li><?=$mess?></li>
													<? endforeach; ?>
												</ul>
											</div>
										</div>
									</div>
								<? endif; ?>
							</div>
						<? endif; ?>

						<? if($arParams['USE_CAPTCHA']): ?>
							<?
							$INPUT_CLASS          = '';
							$INPUT_MESSAGE_DANGER = '';
							$INPUT_ASTERISK       = ':<span class="api-mf-asterisk">*</span>';

							if($arResult['MESSAGE']['DANGER'])
							{
								if($arResult['MESSAGE']['DANGER']['BITRIX_CAPTCHA'])
								{
									$INPUT_CLASS .= ' uk-form-danger';
									$INPUT_MESSAGE_DANGER = '<div class="error-list"><ul><li>' . $arResult['MESSAGE']['DANGER']['BITRIX_CAPTCHA'] . '</li></ul></div>';
								}
								else
								{
									$INPUT_CLASS .= ' uk-form-success';
								}
							}
							$INPUT_CLASS .= ' required';

							$INPUT_CLASS = trim($INPUT_CLASS);
							$INPUT_NAME  = $arParams['HIDE_ASTERISK'] ? GetMessage('MFT_CAPTCHA_CODE') : GetMessage('MFT_CAPTCHA_CODE') . $INPUT_ASTERISK;
							?>
							<div class="api-mf-captcha <?=$INPUT_ROW_CLASS?>">
								<? if(!$arParams['HIDE_FIELD_NAME']): ?>
									<label class="<?=$INPUT_LABEL_CLASS?>"><?=$INPUT_NAME?></label>
								<? endif; ?>
								<div class="<?=$INPUT_CONTROLS_CLASS?>">
									<div class="api-mf-captcha-inner">
										<input type="hidden" name="captcha_sid" value="<?=$arResult['capCode']?>">
										<input type="text"
										       name="captcha_word"
										       size="30"
										       maxlength="45"
										       value=""
										       autocomplete="off"
										       class="<?=$INPUT_CLASS;?>">
										<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['capCode']?>"
										     width="180"
										     height="40"
										     alt="CAPTCHA CODE">
									</div>
									<?=$INPUT_MESSAGE_DANGER;?>
								</div>
							</div>
						<? endif; ?>
					</div>
					<? if($arParams['FORM_TEXT_AFTER']): ?>
						<div class="api-mf-form-text-after"><?=$arParams['FORM_TEXT_AFTER'];?></div>
					<? endif; ?>
				<? endif; ?>


				<?
				if($_POST["is_api_mf_ajax"] != "Y")
				{
				?>
			</div><!--API-MF-AJAX-->
			<? if($arParams['USE_AGREEMENT']):?>
				<div id="API-MF-AGREEMENT-<?=$FORM_ID?>" class="<?=$INPUT_ROW_CLASS?> group-agreement">
					<? if(!$arParams['HIDE_FIELD_NAME']):?>
						<label class="<?=$INPUT_LABEL_CLASS?>">&nbsp;</label>
					<?endif; ?>
					<div class="<?=$INPUT_CONTROLS_CLASS?>">
						<input id="API_MF_AGREEMENT_<?=$FORM_ID?>" type="checkbox" name="API_MF_AGREEMENT" value="Y"<? if($_REQUEST['API_MF_AGREEMENT'] == 'Y'):?> checked=""<?endif; ?>>&nbsp;<!--
							--><a href="<?=$arParams['AGREEMENT_LINK']?>" target="_blank"><?=$arParams['AGREEMENT_TEXT']?></a>
					</div>
				</div>
			<?endif; ?>
			<div class="<?=$INPUT_ROW_CLASS?>" id="API-MF-BUTTONS-<?=$FORM_ID?>">
				<? if($arParams['BUTTON_TEXT_BEFORE']): ?>
					<div class="api-mf-button-text-before <?=$INPUT_ROW_CLASS?>"><?=$arParams['BUTTON_TEXT_BEFORE'];?></div>
				<? endif; ?>
				<div class="<?=$INPUT_ROW_CLASS?> group-button">
					<? if(!$arParams['HIDE_FIELD_NAME']): ?>
						<label class="<?=$INPUT_LABEL_CLASS?>">&nbsp;</label>
					<? endif; ?>
					<div class="<?=$INPUT_CONTROLS_CLASS?>">
						<button id="API_MF_SUBMIT_BUTTON_<?=$FORM_ID?>"
						        type="submit"
						        name="API_MF_SUBMIT_BUTTON"
						        class="<?=$arParams['FORM_SUBMIT_CLASS'];?><?=$BUTTON_SIZE?>"
						        style="<?=$arParams['FORM_SUBMIT_STYLE'];?>"
						        value="Y"><?=$arResult['FORM_SUBMIT_VALUE'];?></button>
					</div>
				</div>
			</div>
		</form>
	<?
	}
	else
	{
		die();
	}
	?>
	</div>
	<? if($arParams['INCLUDE_CSSMODAL'] && $arParams['MODAL_FOOTER_HTML']): ?>
		<?=$arParams['MODAL_FOOTER_HTML']?>
	<? endif; ?>
</div>