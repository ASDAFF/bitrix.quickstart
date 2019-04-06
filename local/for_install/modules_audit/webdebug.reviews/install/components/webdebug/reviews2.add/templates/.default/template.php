<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<?if($arResult['USER_AUTHORIZED'] || $arResult['INTERFACE']['ALLOW_UNREGISTERED']=='Y'):?>
	<div class="wdr2_add wdr2_add_<?=$arResult['FORM_INDEX'];?>">
		<div class="wdr2_form_wrapper"<?if($arParams['MINIMIZE_FORM']):?> style="display:none"<?endif?>>
			<div class="form_title"><?=GetMessage('WD_REVIEWS2_ADD_HEADER');?></div>
			<div class="wdr2_result"></div>
			<form name="<?=$arResult['FORM_NAME'];?>" id="<?=$arResult['FORM_NAME'];?>" target="<?=$arResult['IFRAME_NAME'];?>" action="<?=$arResult['FORM_ACTION'];?>" enctype="multipart/form-data" method="post">
				<div>
					<hr/>
					<?// Ratings ?>
					<?if(is_array($arResult['RATINGS']) && !empty($arResult['RATINGS'])):?>
						<div class="ratings">
							<table>
								<tbody>
									<?foreach($arResult['RATINGS'] as $arRating):?>
										<tr>
											<td class="rating_title"><?=$arRating['NAME'];?>:</td>
											<td class="rating_value"><?=WDR2_ShowRating(false, $arRating, $arResult['FORM_FIELD']);?></td>
										</tr>
									<?endforeach?>
								</tbody>
							</table>
						</div>
						<hr/>
					<?endif?>
					<?// Fields ?>
					<div class="fields">
						<?if(is_array($arResult['FIELDS']) && !empty($arResult['FIELDS'])):?>
							<?foreach($arResult['FIELDS'] as $arField):?>
								<?if(strlen($arField['PARAMS']['css_id'])==0){$arField['PARAMS']['css_id']='wdr2_'.ToLower($this->randString());}?>
								<div class="field field_<?=ToLower($arField['CODE']);?>">
									<?if($arField['PARAMS']['show_name']!='Y'):?>
										<label for="<?=$arField['PARAMS']['css_id'];?>"><?=$arField['NAME'];?><?if($arField['REQUIRED']=='Y'):?><span class="required">*</span><?endif?>:</label>
									<?endif?>
									<div class="input">
										<?=WDR2_ShowField(false, $arField, $arResult['FORM_FIELD'].'[FIELDS]');?>
									</div>
								</div>
							<?endforeach?>
						<?endif?>
						<?// Antibot ?>
						<div class="field wdr2_reqfield"><input type="text" name="<?=$arResult['ANTIBOT_FIELD_NAME'];?>" value="" /></div>
						<?// Captcha ?>
						<?if($arResult['USE_CAPTCHA']):?>
							<div class="field wdr2_captcha">
								<label for=""><?=GetMessage('WD_REVIEWS2_CAPTCHA_HEADER');?><?if($arField['REQUIRED']=='Y'):?><span class="required">*</span><?endif?>:</label>
								<div class="input">
									<table>
										<tbody>
											<tr>
												<td>
													<input type="text" name="captcha_word" value="" size="10" maxlength="5" />
													&nbsp;
												</td>
												<td>
													<div id="wdr2_captcha_<?=$arResult['FORM_INDEX'];?>"><small><?=GetMessage('WD_REVIEWS2_CAPTCHA_LOADING');?></small></div>
													<script type="text/javascript"><?=$arResult['FUNCTION_UPDATE_CAPTCHA'];?></script>
												</td>
												<td>
													&nbsp;
													<a href="javascript:<?=$arResult['FUNCTION_UPDATE_CAPTCHA'];?>"><?=GetMessage('WD_REVIEWS2_CAPTCHA_RELOAD');?></a>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?endif?>
					</div>
					<?// Submit button ?>
					<div class="submit">
						<br/>
						<input type="submit" value="<?=GetMessage('WD_REVIEWS2_SUBMIT');?>" />
					</div>
					<?// Additional form data ?>
					<input type="hidden" name="wdr2_interface" value="<?=$arResult['INTERFACE_ID'];?>" />
					<input type="hidden" name="wdr2_target" value="<?=$arParams['TARGET'];?>" />
					<input type="hidden" name="wdr2_form" value="<?=$arResult['FORM_INDEX'];?>" />
					<input type="hidden" name="wdr2_field" value="<?=$arResult['FORM_FIELD'];?>" />
					<input type="hidden" name="wdr2_site" value="<?=SITE_ID;?>" />
					<input type="hidden" name="wdr2_url" value="<?=urlencode($_SERVER['REQUEST_URI']);?>" />
					<?=bitrix_sessid_post('wd_reviews2_review_sessid');?>
				</div>
			</form>
		</div>
		<?if($arParams['MINIMIZE_FORM']):?>
			<a href="#r" id="<?=$arResult['FORM_NAME'];?>_add_btn" class="add_btn"><?=GetMessage('WD_REVIEWS2_ADD_BUTTON');?></a>
		<?endif?>
	</div>

	<script type="text/javascript">
	//<![CDATA[
	<?// Success function ?>
	function <?=$arResult['FUNCTION_JS_SUCCESS'];?>(HTML, iFrame) {
		var Result = $('.wdr2_add_<?=$arResult['FORM_INDEX'];?> .wdr2_result');
		Result.removeClass('wdr2_error').addClass('wdr2_success').html('<?=$arResult['INTERFACE']['SUCCESS_MESSAGE'];?>');
		$('html,body').animate({scrollTop: Result.offset().top - 50},250);
		$('.wdr2_add_<?=$arResult['FORM_INDEX'];?> form').remove();
		$('#<?=$arResult['FORM_NAME'];?> input[type=submit]').removeAttr('disabled');
	}
	<?// Error function ?>
	function <?=$arResult['FUNCTION_JS_ERROR'];?>(HTML, iFrame) {
		var Result = $('.wdr2_add_<?=$arResult['FORM_INDEX'];?> .wdr2_result');
		Result.removeClass('wdr2_success').addClass('wdr2_error').html(HTML);
		$('html,body').animate({scrollTop: Result.offset().top - 50},250);
		<?if($arResult['USE_CAPTCHA']):?>
			<?=$arResult['FUNCTION_UPDATE_CAPTCHA'];?>;
		<?endif?>
		$('#<?=$arResult['FORM_NAME'];?> input[type=submit]').removeAttr('disabled');
	}
	<?// Disable submit on form send ?>
	$('#<?=$arResult['FORM_NAME'];?>').submit(function(){
		$(this).find('input[type=submit]').attr('disabled','disabled');
	});
	<?if($arParams['MINIMIZE_FORM']):?>
	<?// Show form on start button click ?>
	$('#<?=$arResult['FORM_NAME'];?>_add_btn').click(function(Event){
		Event.preventDefault();
		$(this).remove();
		$('#<?=$arResult['FORM_NAME'];?>').parent().slideDown();
	});
	<?endif?>
	//]]>
	</script>
<?else:?>
	<p><?=GetMessage('WD_REVIEWS2_NEED_AUTH');?></p>
<?endif?>