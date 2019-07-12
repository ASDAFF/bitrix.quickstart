<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->SetTitle(GetMessage("AUTH_AUTH"));?>
<?
if(!empty( $_REQUEST["change_password"])){
	LocalRedirect(SITE_DIR.'auth/change-password/?change_password='.$_REQUEST["change_password"].'&lang='.$_REQUEST["lang"].'&USER_CHECKWORD='.$_REQUEST["USER_CHECKWORD"].'&USER_LOGIN='.$_REQUEST["USER_LOGIN"].'');
}
?>
<?if(!$USER->isAuthorized()):?>
	<div class="module-authorization">
		<?if($arResult['SHOW_ERRORS'] == 'Y'):?>
			<?ShowMessage($arResult['ERROR_MESSAGE']);?>
		<?endif;?>		
		<div class="authorization-cols">
			<div class="col authorization">
				<div class="auth-title"><?=GetMessage("ALLREADY_REGISTERED");?></div>
				<div class="form-block">
					<div class="form_wrapp">
						<form id="avtorization-form-page" name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=SITE_DIR?>auth/<?=!empty( $_REQUEST["backurl"] ) ? '?backurl='.$_REQUEST["backurl"] : ''?>">
							<?if($arResult["BACKURL"] <> ''):?><input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" /><?endif?>
							<?foreach($arResult["POST"] as $key => $value):?>
								<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
							<?endforeach;?>
							<input type="hidden" name="AUTH_FORM" value="Y" />
							<input type="hidden" name="TYPE" value="AUTH" />
							<div class="r form-control">
								<label><?=GetMessage("EMAIL")?> <span class="star">*</span></label>
								<input type="text"  name="USER_LOGIN" required maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="17" tabindex="7" />
								<?if($_POST["USER_LOGIN"]=='' && isset($_POST["USER_LOGIN"])){?><label class="error"><?=GetMessage("FIELD_REQUIRED")?></label><?}?>
							</div>
							<div class="r form-control">
								<label><?=GetMessage("AUTH_PASSWORD")?> <span class="star">*</span></label>
								<input type="password" class="password" name="USER_PASSWORD" required maxlength="50" size="17" tabindex="8" />
								<?if($_POST["USER_PASSWORD"]=='' && isset($_POST["USER_PASSWORD"])){?><label class="error"><?=GetMessage("FIELD_REQUIRED")?></label><?}?>
							</div>
							<?if ($arResult["CAPTCHA_CODE"]):?>
								<div class="form-control captcha-row clearfix">
									<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
									<div class="captcha_image">
										<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
										<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
										<div class="captcha_reload"></div>
									</div>
									<div class="captcha_input">
										<input type="text" name="captcha_word" maxlength="50" value="" />
									</div>
								</div>
							<?endif?>
							<div class="but-r">
								<div class="filter block">
									<a class="forgot" href="<?=SITE_DIR?>auth/forgot-password/<?=!empty( $_REQUEST["backurl"] ) ? '?backurl='.$_REQUEST["backurl"] : ''?>" tabindex="9"><?=GetMessage("FORGOT_PASSWORD")?></a>
									<div class="remember">
										<input id="remuser" type="checkbox" tabindex="11" />
										<label for="remuser" tabindex="11"><?=GetMessage("AUTH_REMEMBER_ME")?></label>
									</div>
									<div class="clearboth"></div>
								</div>
								<div class="buttons">
									<button type="submit" class="button vbig_btn wides" name="Login" tabindex="10"><span><?=GetMessage("AUTH_LOGIN_BUTTON")?></span></button>
								</div>
							</div>							
						</form>
					</div>
					<?if($arResult["AUTH_SERVICES"]):?>
						<div class="soc-avt">
							<?=GetMessage("LOGIN_AS")?>:
							<?$APPLICATION->IncludeComponent(
								"bitrix:socserv.auth.form",
								"icons",
								array(
									"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
									"AUTH_URL" => $arResult["AUTH_URL"],
									"POST" => $arResult["POST"],
									"POPUP" => "N",
									"SUFFIX" => "form",
								),
								$component,
								array("HIDE_ICONS" => "Y")
							);?>
						</div>
					<?endif;?>
					<div class="clearboth"></div>
				</div>
			</div>
			<div class="col registration">
				<div class="auth-title"><?=GetMessage("NEW_USER");?></div>
				<div class="form-block">
					<div class="form_wrapp">
						<!--noindex-->
							<a href="<?=SITE_DIR?>auth/registration/<?=!empty( $_REQUEST["backurl"] ) ? '?backurl='.$_REQUEST["backurl"] : ''?>" class="button transparent vbig_btn wides reg_new" rel="nofollow">
								<span><?=GetMessage("REGISTER")?></span>
							</a>
						<!--/noindex-->
						<div class="more_text_small">
							<?$APPLICATION->IncludeFile(SITE_DIR."include/top_auth.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("TOP_AUTH_REGISTER")));?>
						</div>
					</div>
				</div>					
			</div>
		</div>
	</div>
	<script type="text/javascript">
	if($(window).width() >= 600){
		$('.authorization-cols').equalize({children: '.col .auth-title', reset: true});
		$('.authorization-cols').equalize({children: '.col .form-block', reset: true}); 
	}
	
	$(document).ready(function(){
		$(window).resize();
		
		$(".authorization-cols .col.authorization .soc-avt .row a").click(function(){
			$(window).resize();
		});
		
		$("#avtorization-form-page").validate({
			rules: {
				USER_LOGIN: {
					email: true,
					required:true
				}
			}
		});
		
		$("form[name=bx_auth_servicesform]").validate(); 
	});
	</script>
<?endif;?>