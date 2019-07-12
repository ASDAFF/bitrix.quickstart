<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$frame = $this->createFrame()->begin('')?>
<?
$bLeftAndRight = false;
if(is_array($arResult["QUESTIONS"])){
	foreach($arResult["QUESTIONS"] as $arQuestion){
		if($arQuestion["STRUCTURE"][0]["FIELD_PARAM"] == 'left'){
			$bLeftAndRight = true;
			break;
		}
	}
}
?>
<div class="form inline <?=$arResult["arForm"]["SID"]?>">
	<!--noindex-->
	<div class="form_head">
		<?if($arResult["isFormTitle"] == "Y"):?>
			<h4><?=$arResult["FORM_TITLE"]?></h4>
		<?endif;?>
		<?if($arResult["isFormDescription"] == "Y"):?>
			<div class="form_desc"><?=$arResult["FORM_DESCRIPTION"]?></div>
		<?endif;?>
	</div>
	<?if($arResult["isFormErrors"] == "Y" || strlen($arResult["FORM_NOTE"])):?>
		<div class="form_result <?=($arResult["isFormErrors"] == "Y" ? 'error' : 'success')?>">
			<?if($arResult["isFormErrors"] == "Y"):?>
				<?=$arResult["FORM_ERRORS_TEXT"]?>
			<?else:?>
				<?$successNoteFile = SITE_DIR."include/form/success_{$arResult["arForm"]["SID"]}.php";?>
				<?if(file_exists($_SERVER["DOCUMENT_ROOT"].$successNoteFile)):?>
				<?$APPLICATION->IncludeFile($successNoteFile, array(), array("MODE" => "html", "NAME" => "Form success note"));?>
				<?else:?>
					<?=GetMessage("FORM_SUCCESS");?>
				<?endif;?>
			<?endif;?>
		</div>
	<?endif;?>
	<?=$arResult["FORM_HEADER"]?>
	<?=bitrix_sessid_post();?>
	<div class="form_body">
		<?if(is_array($arResult["QUESTIONS"])):?>
			<?if(!$bLeftAndRight):?>
				<?foreach($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):?>
					<?COptimus::drawFormField($FIELD_SID, $arQuestion);?>
				<?endforeach;?>
			<?else:?>
				<div class="form_left">
					<?foreach($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):?>
						<?if($arQuestion["STRUCTURE"][0]["FIELD_PARAM"] == 'left'):?>
							<?COptimus::drawFormField($FIELD_SID, $arQuestion);?>
						<?endif;?>
					<?endforeach;?>
				</div>
				<div class="form_right">
					<?foreach($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):?>
						<?if($arQuestion["STRUCTURE"][0]["FIELD_PARAM"] != 'left'):?>
							<?COptimus::drawFormField($FIELD_SID, $arQuestion);?>
						<?endif;?>
					<?endforeach;?>
				</div>
			<?endif;?>
		<?endif;?>
		<div class="clearboth"></div>
		<?if($arResult["isUseCaptcha"] == "Y"):?>
			<div class="form-control captcha-row clearfix">
				<label><span><?=GetMessage("FORM_CAPRCHE_TITLE")?>&nbsp;<span class="star">*</span></span></label>
				<div class="captcha_image">
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"])?>" border="0" />
					<input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"])?>" />
					<div class="captcha_reload"></div>
				</div>
				<div class="captcha_input">
					<input type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required />
				</div>
			</div>
		<?endif;?>
		<div class="clearboth"></div>
	</div>
	<div class="form_footer">
		<button type="submit" class="button medium" value="submit" name="web_form_submit" ><span><?=$arResult["arForm"]["BUTTON"]?></span></button>
		<button type="reset" class="button medium transparent" value="reset" name="web_form_reset" ><span><?=GetMessage('FORM_RESET')?></span></button>
		<script type="text/javascript">
		$(document).ready(function(){
			$('form[name="<?=$arResult["arForm"]["VARNAME"]?>"]').validate({
				highlight: function( element ){
					$(element).parent().addClass('error');
				},
				unhighlight: function( element ){
					$(element).parent().removeClass('error');
				},
				submitHandler: function( form ){
					if( $('form[name="<?=$arResult["arForm"]["VARNAME"]?>"]').valid() ){
						form.submit();
						setTimeout(function() {
							$(form).find('button[type="submit"]').attr("disabled", "disabled");
						}, 300);
					}
				},
				errorPlacement: function( error, element ){
					error.insertBefore(element);
				}
			});
			
			if(arOptimusOptions['THEME']['PHONE_MASK'].length){
				var base_mask = arOptimusOptions['THEME']['PHONE_MASK'].replace( /(\d)/g, '_' );
				$('form[name=<?=$arResult["arForm"]["VARNAME"]?>] input.phone, form[name=<?=$arResult["arForm"]["VARNAME"]?>] input[data-sid=PHONE]').inputmask('mask', {'mask': arOptimusOptions['THEME']['PHONE_MASK'] });
				$('form[name=<?=$arResult["arForm"]["VARNAME"]?>] input.phone, form[name=<?=$arResult["arForm"]["VARNAME"]?>] input[data-sid=PHONE]').blur(function(){
					if( $(this).val() == base_mask || $(this).val() == '' ){
						if( $(this).hasClass('required') ){
							$(this).parent().find('label.error').html(BX.message('JS_REQUIRED'));
						}
					}
				});
			}
		});
		</script>
	</div>
	<?=$arResult["FORM_FOOTER"]?>
	<!--/noindex-->
</div>
<?$frame->end()?>