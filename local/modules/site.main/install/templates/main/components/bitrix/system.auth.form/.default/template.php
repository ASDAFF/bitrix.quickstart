<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="system-auth-form system-auth-form-default">
	<?
	if ($arResult['FORM_TYPE'] == 'login') {
		?>
		<a class="fake open-login-popup" href="#login" data-toggle="modal" data-target="#login">Вход</a>
		<?if ($arResult['NEW_USER_REGISTRATION'] == 'Y') {
			?>
			| <a href="<?=$arResult['AUTH_REGISTER_URL']?>" rel="nofollow"><?=getMessage('AUTH_NO_ACC_ACTION')?></a>
			<?
		}?>
		
		<div class="modal fade bs-example-modal-sm" id="login">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Добро пожаловать</h4>
					</div>
					<div class="modal-body">
						<?
						if ($_REQUEST['AUTH_REASON'] == 'auth-form' && $arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']) {
							ShowMessage($arResult['ERROR_MESSAGE']);
							?>
							<script>
								$(function() {
									$('a[href="#login"]').trigger('click');
								});
							</script>
							<?
						}
						?>
						<form class="form form-login" method="post" target="_top" action="<?=$arResult['AUTH_URL']?>" role="login">
							<?
							if ($arResult['BACKURL']) {
								?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>"/><?
							}
							foreach ($arResult['POST'] as $key => $value) {
								?><input type="hidden" name="<?=$key?>" value="<?=$value?>"/><?
							}
							?>
							<input type="hidden" name="AUTH_FORM" value="Y"/>
							<input type="hidden" name="AUTH_REASON" value="auth-form"/>
							<input type="hidden" name="TYPE" value="AUTH"/>
							
							<div class="form-group form-group-sm">
								<label class="control-label" for="system-auth-form-login"><?=GetMessage('AUTH_LOGIN')?>:</label>
								<input class="form-control login-field" autofocus="autofocus" type="email" name="USER_LOGIN" id="system-auth-form-login" value="<?=$arResult['USER_LOGIN']?>" maxlength="50" tabindex="1" required="" />
							</div>
							<div class="form-group form-group-sm has-feedback">
								<label class="control-label" for="system-auth-form-password"><?=GetMessage('AUTH_PASSWORD')?>:</label>
								<a class="forgot-pass" href="<?=$arResult['AUTH_FORGOT_PASSWORD_URL']?>" rel="nofollow"><?=GetMessage('AUTH_FORGOT_PASSWORD_2')?></a>
								<input class="form-control" type="password" name="USER_PASSWORD" id="system-auth-form-password" maxlength="50" tabindex="2" required="" />
								
								<?if ($arResult['SECURE_AUTH']) {
									?>
									<noscript>
										<span class="glyphicon glyphicon-unlock form-control-feedback" title="<?=GetMessage('AUTH_NONSECURE_NOTE')?>"></span>
									</noscript>
									<script>
										document.write('<span class="glyphicon glyphicon-lock form-control-feedback" title="<?=GetMessage('AUTH_SECURE_NOTE')?>"></span>');
									</script>
									<?
								}?>
							</div>
							
							<?
							if ($arResult['STORE_PASSWORD'] == 'Y') {
								?>
								<div class="form-group form-group-sm">
									<label class="checkbox-inline" title="<?=GetMessage('AUTH_REMEMBER_ME')?>">
										<input type="checkbox" name="USER_REMEMBER" tabindex="3" value="Y" /> <?=GetMessage('AUTH_REMEMBER_SHORT')?>
									</label>
								</div>
								<?
							}
							
							if ($arResult['CAPTCHA_CODE']) {
								?>
								<div class="form-group form-group-sm group-captcha">
									<label class="control-label" for="system-auth-form-captcha">
										<?=GetMessage('AUTH_CAPTCHA_PROMT')?>:
									</label>
									<div class="row">
										<div class="col-md-4 col-xs-6">
											<img
												class="form-captcha-img"
												src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>"
												alt="captcha"
											/>
										</div>
										<div class="col-md-8 col-xs-6">
											<input
												class="form-control"
												type="text"
												name="captcha_word"
												id="system-auth-form-captcha"
												required=""
												value=""
											/>
										</div>
									</div>
									<input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>"/>
								</div>
								<?
							}?>
							
                            <div class="form-group form-group-sm form-toolbar">
								<input class="btn btn-default btn-sm btn-block" type="submit" value="<?=GetMessage('AUTH_LOGIN_BUTTON')?>"/>
							</div>

                            <div class="form-group form-group-sm form-toolbar">
                                Еще нет аккаунта? <a href="<?=$arResult["AUTH_REGISTER_URL"]?>">Создайте его</a>
                            </div>
							
							<?if ($arResult['AUTH_SERVICES']) {
								?>
								<div class="form-group form-group-sm form-socserv">
									<label class="control-label"><?=GetMessage('socserv_as_user_form')?></label>
									<div>
										<?
										$APPLICATION->IncludeComponent(
											'bitrix:socserv.auth.form',
											'icons', 
											array(
												'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
												'SUFFIX' => 'form',
											), 
											$component, 
											array(
												'HIDE_ICONS' => 'Y'
											)
										);
										?>
									</div>
								</div>
								<?
							}?>
						</form>
						
                        
                        
						<?if ($arResult['AUTH_SERVICES']) {
							$APPLICATION->IncludeComponent(
								'bitrix:socserv.auth.form',
								'',
								array(
									'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
									'AUTH_URL' => $arResult['AUTH_URL'],
									'POST' => $arResult['POST'],
									'POPUP' => 'Y',
									'SUFFIX' => 'form',
								),
								$component,
								array(
									'HIDE_ICONS' => 'Y'
								)
							);
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?
	} else {
		?>
		<form class="form form-logout" action="<?=$arResult['AUTH_URL']?>" role="logout">
			<p>
				<?=$arResult['USER_NAME']?> [<?=$arResult['USER_LOGIN']?>]
				<a href="<?=$arResult['PROFILE_URL']?>"><?=GetMessage('AUTH_PROFILE')?></a>
			</p>
			
			<div class="form-group form-group-sm form-toolbar">
				<?foreach ($arResult['GET'] as $key => $value) {
					?><input type="hidden" name="<?=$key?>" value="<?=$value?>"/><?
				}?>
				<input type="hidden" name="logout" value="yes"/>
				<input class="btn btn-default btn-sm" type="submit" value="<?=GetMessage('AUTH_LOGOUT_BUTTON')?>"/>
			</div>
		</form>
		<?
	}
	?>
</div>