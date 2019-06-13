<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$frame = $this->createFrame()->begin();?>
<?php 
	CJSCore::Init(array("jquery"));
	$form = $arResult['FORM'];

	$this->addExternalJs($this->GetFolder().'/lib/arcticmodal/jquery.arcticmodal-0.3.min.js');
	$APPLICATION->SetAdditionalCSS($this->GetFolder().'/lib/arcticmodal/jquery.arcticmodal-0.3.css');

	IncludeTemplateLangFile(__FILE__);

	if (!function_exists('getFieldDescription')) {
		function getFieldDescription($field){
			?>
			<span class="clientlab-form__description">
				<?php echo html_entity_decode($field['description']); ?>
			</span>
			<?php
		}
	}

	if (!function_exists('getFielderrorMsg')) {
		function getFielderrorMsg($field){
			?>
			<div class="clientlab-form__field__error js-clf-err-msg">
				<?php echo html_entity_decode($field['errMsg']); ?>
			</div>
			<?php
		}
	}


	if (!function_exists('getRequiredMark')) {
		function getRequiredMark($field){
			?>
			<?php if ($field['required']=="true"): ?>
				<i class="clientlab-form__required-mark">*</i>
			<?php endif ?>
			<?php
		}
	}

	if (!function_exists('GetUrlParamValueByString')) {
		function GetUrlParamValueByString($string){
			$output_array = array();
			preg_match('/^#.{1,}#$/', $string, $output_array);
			if (count($output_array)>0) {
				return $_REQUEST[trim(preg_replace('/#/', ' ', $string))];
			}else{
				return $string;
			}
		}
	}

?>

<?php if (!function_exists('showForm')): ?>
	<?php function showForm($arParams, $form){ ?>
		<?php 
			$formRowHtml = array(
				'start' => '<div class="clientlab-form__row">
							<div class="clientlab-form__field js-clf-field">',
				'end' => '</div><!-- /.clientlab-form__field -->
							</div><!-- /.clientlab-form__row -->'
			);
		?>
		
		<?php if (count($form)>0) {?>
		<div class="clientlab-form">
			<form name="<?php echo $arParams['FORM_NAME'] ?>" action="#" class="clientlab-form-<?php echo $form['form_name']; ?> js-clientlab-form" >
				<div class="clientlab-form__rows">

					<?php foreach ($form as $f => $field): ?>
							<?php switch ($field['type']) { 
									case "text": ?>
										<?php echo $formRowHtml['start']; ?>
										<label>
											<?php if ($field['label']!=''): ?>
											<span class="clientlab-form__field__label"><?php echo $field['label']; ?> <?php getRequiredMark($field); ?></span>
											<?php endif ?>
											<input 
												name="<?php echo $field['name']; ?>" 
												type="<?php echo $field['subtype'] ?>" 
												value="<?php echo $field['values'];?>" 
												placeholder="<?php echo $field['placeholder'];?>" 
												class="<?php echo $field['className']; ?>"
												maxlength="<?php echo $field['maxlength'] ?>" 
											/>
											<?php getFieldDescription($field); ?>
											<?php getFielderrorMsg($field); ?>
										</label>
										<?php echo $formRowHtml['end']; ?>
									<?php break; ?>

									<?php case "number": ?>
										<?php echo $formRowHtml['start']; ?>
										<label>
											<?php if ($field['label']!=''): ?>
											<span class="clientlab-form__field__label"><?php echo $field['label']; ?> <?php getRequiredMark($field); ?></span>
											<?php endif ?>
											<input 
												name="<?php echo $field['name']; ?>" 
												type="<?php echo $field['type'] ?>" 
												min="<?php echo $field['min'] ?>" 
												max="<?php echo $field['max'] ?>" 
												step="<?php echo $field['step'] ?>" 
												value="<?php echo $field['values'];?>" 
												placeholder="<?php echo $field['placeholder'];?>" 
												class="<?php echo $field['className']; ?>"
											/>
											<?php getFieldDescription($field); ?>
											<?php getFielderrorMsg($field); ?>
										</label>
										<?php echo $formRowHtml['end']; ?>
									<?php break; ?>

									<?php case "textarea": ?>
										<?php echo $formRowHtml['start']; ?>
										<label>
											<?php if ($field['label']!=''): ?>
											<span class="clientlab-form__field__label"><?php echo $field['label']; ?> <?php getRequiredMark($field); ?></span>
											<?php endif ?>
											<textarea 
												name="<?php echo $field['name']; ?>" 
												value="<?php echo $field['values'][0]['value'];?>" 
												placeholder="<?php echo $field['values'][0]['placeholder'];?>" 
												class="<?php echo $field['className']; ?>"
												maxlength="<?php echo $field['maxlength'] ?>" 
												></textarea>
											<?php getFieldDescription($field); ?>
											<?php getFielderrorMsg($field); ?>
										</label>
										<?php echo $formRowHtml['end']; ?>
									<?php break; ?>

									<?php case "select": ?>
										<?php echo $formRowHtml['start']; ?>
										<label>
											<?php if ($field['label']!=''): ?>
											<span class="clientlab-form__field__label"><?php echo $field['label']; ?> <?php getRequiredMark($field); ?></span>
											<?php endif ?>
											<select name="<?php echo $field['name']; ?>" 
												<?php if ($field['multiple']=="multiple"): ?>
													multiple
												<?php endif ?> 
												class="<?php echo $field['className']; ?>"
											>
												<?php foreach ($field['values'] as $key => $value): ?>
													<option 
														<?php if ($value['selected'] == true) { echo "selected" ;}?> 
														value="<?php echo $value['value']; ?>"
															><?php echo $value['label']; ?>
													</option>
												<?php endforeach ?>
											</select>
											<?php getFieldDescription($field); ?>
											<?php getFielderrorMsg($field); ?>
										</label>
										<?php echo $formRowHtml['end']; ?>
									<?php break; ?>

									<?php case "radio-group": ?>
											<?php echo $formRowHtml['start']; ?>
											<?php if ($field['label']!=''): ?>
											<span class="clientlab-form__field__label"><?php echo $field['label']; ?> <?php getRequiredMark($field); ?></span>
											<?php endif ?>
											<fieldset class="clientlab-form__fieldset <?php if($field['inline']=="true"){ echo "clientlab-form__fieldset--inline";} ?>">
											<?php foreach ($field['values'] as $key => $value): ?>
												<label class="clientlab-form--radio-field">
													<input 
														value="<?php echo $value['value']; ?>" 
														type="radio" 
														name="<?php echo $field['name'] ?>" 
														class="<?php echo $field['className']; ?>"
													/>
													<span><?php echo $value['label']; ?></span>
												</label>
											<?php endforeach ?>
											</fieldset>
											<?php getFieldDescription($field); ?>
											<?php getFielderrorMsg($field); ?>
											<?php echo $formRowHtml['end']; ?>
									<?php break; ?>

									<?php case "checkbox-group": ?>
											<?php echo $formRowHtml['start']; ?>
											<?php if ($field['label']!=''): ?>
											<span class="clientlab-form__field__label <?php if($field['inline']=="true"){echo "clientlab-form__field__label--inline";} ?>"><?php echo $field['label']; ?> <?php getRequiredMark($field); ?></span>
											<?php endif ?>
											<fieldset class="clientlab-form__fieldset <?php if($field['inline']=="true"){ echo "clientlab-form__fieldset--inline";} ?>">
											<?php foreach ($field['values'] as $key => $value): ?>
												<label class="clientlab-form--checkbox-field">
													<input 
														value="<?php echo $value['value']; ?>" 
														type="checkbox" 
														name="<?php echo $field['name'] ?>" 
														class="<?php echo $field['className']; ?>"
													/>
													<span><?php echo $value['label']; ?></span>
												</label>
											<?php endforeach ?>
											</fieldset>
											<?php getFieldDescription($field); ?>
											<?php getFielderrorMsg($field); ?>
											<?php echo $formRowHtml['end']; ?>
									<?php break; ?>

									<?php case "file": ?>
										<?php echo $formRowHtml['start']; ?>
										<?php if ($field['label']!=''): ?>
											<label>
												<span class="clientlab-form__field__label"><?php echo $field['label']; ?> <?php getRequiredMark($field); ?></span>
											</label>
										<?php endif ?>
											<div class="js-cf-attach-area <?php echo $field['className']; ?>" data-field-area-name="<?php echo $field['name']; ?>">
												<!-- Компонент будет вызван в component_epilog и втавлен в область через js -->
											</div><!-- /.js-cf-attach-area -->
											<?php getFieldDescription($field); ?>
											<?php getFielderrorMsg($field); ?>
											<?php echo $formRowHtml['end']; ?>
									<?php break; ?>

									<?php case "header": ?>
										<?php echo $formRowHtml['start']; ?>
										<div class="clientlab-form__header clientlab-form__header--<?php echo $field['subtype']; ?> <?php echo $field['className']; ?>">
											<span><?php echo $field['label']; ?></span>
										</div><!-- /.clientlab-form__header -->
										<?php echo $formRowHtml['end']; ?>
									<?php break; ?>

									<?php case "paragraph": ?>
										<?php echo $formRowHtml['start']; ?>
										<div class="clientlab-form__paragraph clientlab-form__paragraph--<?php echo $field['subtype']; ?> <?php echo $field['className']; ?>">
											<span><?php echo $field['label']; ?></span>
										</div><!-- /.clientlab-form__header -->
										<?php echo $formRowHtml['end']; ?>
									<?php break; ?>
							<?php } //end switch ?>


					<!-- HIDDEN FIELDS -->
					<?php if ($field['type'] == "hidden"): ?>
						<input 
							type="hidden" 
							name="<?php echo $field['name']; ?>" 
							value="<?php echo GetUrlParamValueByString($field['value']); ?>" 
							class="<?php echo $field['className']; ?>" 
						/>
					<?php endif ?>

					<!-- /. HIDDEN FIELDS -->
					<?php endforeach ?>


					<!-- ADDITIONAL HIDDEN FIELDS -->
					<?php if ($arParams['ADDITIONAL_HIDDEN_FIELDS']): ?>
						<?php foreach ($arParams['ADDITIONAL_HIDDEN_FIELDS'] as $f => $field): ?>
							<input 
								type="hidden" 
								name="<?php echo $field['NAME']; ?>" 
								value="<?php echo $field['VALUE']; ?>" 
								class="js-additional-hidden-field"
								data-label="<?php echo $field['LABEL']; ?>"
							/>
						<?php endforeach ?>
					<?php endif ?>

					<?php unset($f, $field); ?>
					<!-- /. ADDITIONAL HIDDEN FIELDS -->

					<div class="clientlab-form__row clientlab-form__action-area">
						<?php if ($arParams['USE_RECAPTCHA']=="Y"): ?>
							
							<div class="js-recaptcha-area js-clf-field">
								<?php if ($arParams['RE_SITE_KEY']==""): ?>
									<p style="color:red; padding: 20px">
										<?php echo GetMessage('ERR_EMPTY_RECAPTCHA_MSG'); ?>
									</p>
								<?php endif ?>
								<div class="g-recaptcha" data-sitekey="<?=$arParams['RE_SITE_KEY']?>"></div>
								<?php getFielderrorMsg(array("errMsg"=>$arParams['RECAPTCHA_ERR_MSG'])); ?>
							</div><!-- /.recaptcha-area -->
						<?php endif ?>

						<?php if ($arParams['AGREEMENT']!=''): ?>
						<div class="clientlab-form__policy-block js-clf-field">
							<label>
								<input class="js-clf-agree" type="checkbox" checked name="<?php echo $arParams['FORM_NAME'] ?>_agree" />
								<p><?php echo html_entity_decode($arParams['AGREEMENT']); ?></p>
								<?php getFielderrorMsg(array("errMsg"=>$arParams['AGREEMENT_ERR_MSG'])); ?>
							</label>
						</div><!-- /.policy-block -->
						<?php endif ?>
						<button class="clientlab-form__btn clientlab-form__btn--submit js-clf-submit-btn">
							<?php echo $arParams['SUBMIT_TEXT']; ?>
						</button>
					</div><!-- /.clientlab-form__row clientlab-form__action-area -->
				</div><!-- /.clientlab-form__rows -->
			</form>
		</div><!-- /.clientlab-form -->
	<?php }else{
		echo GetMessage('SETTING_THE_FORM_MSG');
	} ?>

	<?php } //end showForm ?>
<?php endif ?>




<?php if ($arParams['IS_MODAL_FORM']!="Y"): ?>
	<?php showForm($arParams, $form); ?>
<?php else: ?>
	

	<div style="display: none;">
		<div class="clientlab-form-modal js-clientlab-modal" data-form-name="<?php echo $arParams['FORM_NAME']; ?>" >
			<div class="clientlab-form__modal-close js-clf-modal-close"><?php echo GetMessage('CLOSE_BTN'); ?></div><!-- /.clientlab-form__modal-close -->
			<?php showForm($arParams, $form); ?>
		</div><!-- /.clientlab-modal -->
	</div>

	<button class="clientlab-form__modal-open-btn js-clf-open-modal" data-form-open-name="<?php echo $arParams['FORM_NAME']; ?>">
		<?php echo $arParams['MODAL_BTN_TEXT']; ?>
	</button>

<?php endif ?>


<?php if ($arParams['IS_THANKS_MODAL']=="Y"): ?>
	<div style="display: none;">
		<div class="clientlab-form-modal js-clientlab-modal" data-form-thanks-name="<?php echo $arParams['FORM_NAME']; ?>" >
			<div class="clientlab-form__modal-close js-clf-modal-close"><?php echo GetMessage('CLOSE_BTN'); ?></div><!-- /.clientlab-form__modal-close -->
			<h3><?php echo $arParams['THANKS_MESSAGE_TITLE']; ?></h3>
			<p><?php echo $arParams['THANKS_MESSAGE_TEXT']; ?></p>
		</div><!-- /.clientlab-modal -->
	</div>
<?php endif ?>


<script>
			if (typeof(arCLF) === "undefined") {
				 arCLF = [];
			}
			if (Array.isArray(arCLF)) {
				arCLF.push(
						{
							name: <?=CUtil::PhpToJSObject($arParams['FORM_NAME'])?>,
							arParams:<?=CUtil::PhpToJSObject($arParams)?>,
							fields:<?=CUtil::PhpToJSObject($form)?>
						}
					);
			}

			window.onload = function() {
				forms = new CLF(arCLF);
				forms.init();
				arCLF = '';//непоказывать пользователям настройки форм

				forms.subscribe(function(data){
					console.log('data', data);
					data.form.clearForm();

					if ($('[data-form-thanks-name="'+data.form.name+'"]').length>0) {
						$('[data-form-name="'+data.form.name+'"]').arcticmodal('close');
						$('[data-form-thanks-name="'+data.form.name+'"]').arcticmodal();
					}
				},forms.subscribtionsList.succesfull_send);


				forms.subscribe(function(data){
					$('.js-recaptcha-area .js-clf-err-msg').fadeIn();
				},forms.subscribtionsList.recaptcha_error);


				$(function(){
					if ($('.js-clf-open-modal').length > 0) {
						$('.js-clf-open-modal').click(function(){
							var formName = $(this).attr('data-form-open-name');
							$('[data-form-name="'+formName+'"]').arcticmodal();
						});
					}
				});


				$('.js-clf-modal-close').click(function(){
					$(this).parents('.js-clientlab-modal').arcticmodal('close');
				});


				BX.ready(function(){
					BX.addCustomEvent('OnFileUploadSuccess', function(res) {
						forms.publish(res, "OnFileUploadSuccess");
					});
				});

				/*forms.subscribe(function(data){
				console.log('invalid', data)
				},forms.subscribtionsList.unsuccessful_validation);

				forms.subscribe(function(data){
				console.log('valid', data)
				},forms.subscribtionsList.successful_validation);*/
			}
</script>
<?php if ($arParams['USE_RECAPTCHA']=="Y"): ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif ?>

<?$frame->beginStub();?>

<?$frame->end();?>