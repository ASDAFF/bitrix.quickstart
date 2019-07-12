<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<div class="module-form-block-wr registraion-page">
	<?if($USER->IsAuthorized()){?>
		<p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>
	<?}else{?>
		<?if (count($arResult["ERRORS"]) > 0){
			foreach ($arResult["ERRORS"] as $key => $error)
				if (intval($key) == 0 && $key !== 0) 
					$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

			ShowError(implode("<br />", $arResult["ERRORS"]));
		}elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"){?>
			
		<?}?>
	<?}?>

	<?if( empty($arResult["ERRORS"]) && !empty($_POST["register_submit_button"]) && $arResult["USE_EMAIL_CONFIRMATION"]=="N"){
		LocalRedirect(SITE_DIR.'personal/');
	}elseif( empty($arResult["ERRORS"]) && !empty($_POST["register_submit_button"]) && $arResult["USE_EMAIL_CONFIRMATION"]=="Y"){?>
		<p><?echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT")?></p>
	<?}else{?>
		<div class="form-block border_block">
			<div class="wrap_md">
				<div class="main_info iblock">
					<div class="top">
						<?$APPLICATION->IncludeFile(SITE_DIR."include/register_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("REGISTER_INCLUDE_AREA"), ));?>
					</div>
					<script>
						$(document).ready(function(){
							$("form#registraion-page-form").validate
							({
								rules:{ emails: "email"},
								messages: {
									"captcha_word": {
										remote: '<?=GetMessage("VALIDATOR_CAPTCHA")?>'
									},
								},
								highlight: function( element ){
									$(element).parent().addClass('error');
								},
								unhighlight: function( element ){
									$(element).parent().removeClass('error');
								},
								submitHandler: function( form ){
										form.submit();
									if( $("form#registraion-page-form").valid() ){
										setTimeout(function() {
											//$(form).find('button[type="submit"]').attr("disabled", "disabled");
										}, 300);
									}
								},
								errorPlacement: function( error, element ){
									error.insertBefore(element);
								}
							});
							$("form[name=bx_auth_servicesform_inline]").validate(); 
						})
					</script>
					
					<form id="registraion-page-form" method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data" >					
						<?if($arResult["BACKURL"] <> ''):?>
							<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
						<?endif;?>
						<input type="hidden" name="register_submit_button" value="reg" />
						<?/*$arResult['SHOW_FIELDS'] = array(
							   'NAME',
							   'PERSONAL_PHONE',
							   'EMAIL',                                           
							   'PASSWORD',
							   'CONFIRM_PASSWORD',
							   //'PERSONAL_STREET',
								'LOGIN'
							);
						*/?>
						<?
						$arTmpField=$arFields=$arUFields=array();
						$arTmpField=array_combine($arResult['SHOW_FIELDS'], $arResult['SHOW_FIELDS']);
						unset($arTmpField["PASSWORD"]);
						unset($arTmpField["CONFIRM_PASSWORD"]);

						if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"){
							foreach($arParams["USER_PROPERTY"] as $name){
								$arUFields[$name]=$arResult["USER_PROPERTIES"]["DATA"][$name];
							}
						}

						if($arParams["SHOW_FIELDS"]){
							foreach($arParams["SHOW_FIELDS"] as $name){
								$arFields[$arTmpField[$name]]=$name;
							}
						}else{
							$arFields=$arTmpField;
						}
						$arFields["PASSWORD"]="PASSWORD";
						$arFields["CONFIRM_PASSWORD"]="CONFIRM_PASSWORD";
						$arFields["LOGIN"]="LOGIN";

						?>
						<?foreach ($arFields as $FIELD):?>
							<?if( $FIELD != "LOGIN" ){?>
								<div class="form-control bg">
									<div class="wrap_md">
										<div class="iblock label_block">
							<?}?>
										<?if( $FIELD != "LOGIN" ):?>
											<label for="input_<?=$FIELD;?>"><?=GetMessage("REGISTER_FIELD_".$FIELD)?> <?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="star">*</span><?endif;?></label>
										<?endif;?>
										<?if( array_key_exists( $FIELD, $arResult["ERRORS"] ) ):?>
											<?$class='class="error"'?>
										<?endif;?>
										
										<?switch ($FIELD){
											case "PASSWORD":?>
												<input size="30" type="password" id="input_<?=$FIELD;?>" name="REGISTER[<?=$FIELD?>]" required value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="password <?=(array_key_exists( $FIELD, $arResult["ERRORS"] ))? 'error': ''?>"  />
												
											<?break;
											case "CONFIRM_PASSWORD":?>
												<input size="30" type="password" id="input_<?=$FIELD;?>" name="REGISTER[<?=$FIELD?>]" required value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="confirm_password <?=(array_key_exists( $FIELD, $arResult["ERRORS"] ))? 'error': ''?>" />
											
											<?break;
											case "PERSONAL_GENDER":?>
												<select name="REGISTER[<?=$FIELD?>]" id="input_<?=$FIELD;?>">
													<option value=""><?=GetMessage("USER_DONT_KNOW")?></option>
													<option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_MALE")?></option>
													<option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
												</select>
												<?break;
											case "PERSONAL_COUNTRY":
											case "WORK_COUNTRY":?>
												<select name="REGISTER[<?=$FIELD?>]" id="input_<?=$FIELD;?>">
													<?foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value){?>
														<option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
													<?}?>
												</select>
												<?break;
											case "PERSONAL_PHOTO":
											case "WORK_LOGO":?>
												<input size="30" type="file" id="input_<?=$FIELD;?>" name="REGISTER_FILES_<?=$FIELD?>" />
												<?break;
											case "PERSONAL_NOTES":
											case "WORK_NOTES":?>
												<textarea cols="30" rows="5" id="input_<?=$FIELD;?>" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea>
												
											<?case "PERSONAL_STREET":?>
												<textarea cols="30" rows="5" id="input_<?=$FIELD;?>" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea>
												<?break;?>
											<?case "EMAIL":?>
												<input size="30" type="email" id="input_<?=$FIELD;?>" name="REGISTER[<?=$FIELD?>]" required value="<?=$arResult["VALUES"][$FIELD]?>" <?=$class?> id="emails"/>
											<?break;?>
											<?case "NAME":?>
												<input size="30" type="text" id="input_<?=$FIELD;?>" name="REGISTER[<?=$FIELD?>]" <?=($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y" ? "required": "");?> value="<?=htmlspecialcharsbx($_REQUEST["REGISTER"]["NAME"]);?>" <?=$class?>/>                              
											<?break;?>
											<?case "PERSONAL_PHONE":?>
												<input size="30" type="text" id="input_<?=$FIELD;?>" name="REGISTER[<?=$FIELD?>]" class="phone <?=(array_key_exists( $FIELD, $arResult["ERRORS"] ))? 'error': ''?>" <?=($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y" ? "required": "");?> value="<?=$arResult["VALUES"][$FIELD]?>" />
											<?break;?>	
											<?break;
											default:?>
												<?// hide login?>
												<input size="30" id="input_<?=$FIELD;?>" <?=$FIELD == "LOGIN" ? 'type="hidden" value="1"' : 'type="text"'?> name="REGISTER[<?=$FIELD?>]"  />
												<?if ($FIELD == "PERSONAL_BIRTHDAY"){?>
													<?$APPLICATION->IncludeComponent(
														'bitrix:main.calendar',
														'',
														array(
															'SHOW_INPUT' => 'N',
															'FORM_NAME' => 'regform',
															'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
															'SHOW_TIME' => 'N'
														),
														null,
														array("HIDE_ICONS"=>"Y")
													);?>
												<?}?>
												<?break;?>
										<?}?>
										<?if( $FIELD != "LOGIN" && array_key_exists( $FIELD, $arResult["ERRORS"] ) ):?>
											<label class="error"><?=GetMessage("REGISTER_FILL_IT")?></label>
										<?endif;?>
							<?if( $FIELD != "LOGIN" ){?>
										</div>
										<div class="iblock text_block">
											<?=GetMessage("REGISTER_FIELD_TEXT_".$FIELD);?>
										</div>
									</div>
								</div>
							<?}?>
						<?endforeach?>
						<?if($arUFields){?>
							<?foreach($arUFields as $arUField){?>
								<div class="r">				
									<label><?=$arUField["EDIT_FORM_LABEL"];?>:<?if ($arUField["MANDATORY"] == "Y"):?><span class="star">*</span><?endif;?></label>
									<?$APPLICATION->IncludeComponent(
									"bitrix:system.field.edit",
									$arUField["USER_TYPE"]["USER_TYPE_ID"],
									array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUField, "form_name" => "regform"), null, array("HIDE_ICONS"=>"Y"));?>
								</div>
							<?}?>
						<?}?>
						<?if ($arResult["USE_CAPTCHA"] == "Y"){?>
							<div class="form-control bg register-captcha captcha-row clearfix">
								<div class="iblock label_block">
									<label><span><?=GetMessage("REGISTER_CAPTCHA_PROMT")?>&nbsp;<span class="star">*</span></span></label>
									<div class="captcha_image">
										<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" border="0" />
										<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
										<div class="captcha_reload"><?=GetMessage("RELOAD")?></div>
									</div>
									<div class="captcha_input">
										<input type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required />
									</div>
								</div>
								<div class="iblock text_block"></div>
							</div>
						<?}?>
						<div class="but-r">
							<button class="button short" type="submit" name="register_submit_button1" value="<?=GetMessage("AUTH_REGISTER")?>">
								<span><?=GetMessage("REGISTER_REGISTER")?></span>
							</button>
							<?/*<div class="prompt"><span class="star">*</span> &nbsp;&mdash;&nbsp; <?=GetMessage("REQUIRED_FIELDS")?></div>*/?>
							<div class="clearboth"></div>
						</div>				
					</form>
				</div>
				<div class="social_block iblock">
					<?$APPLICATION->IncludeComponent(
						"bitrix:system.auth.form",
						"popup",
						array(
							"TITLE" => "Авторизация",
							"PROFILE_URL" => $arParams["PATH_TO_PERSONAL"],
							"SHOW_ERRORS" => "Y",
							"POPUP_AUTH" => "Y"
						)
					);?>
				</div>
			</div>
		</div>
	<?}?>
</div>