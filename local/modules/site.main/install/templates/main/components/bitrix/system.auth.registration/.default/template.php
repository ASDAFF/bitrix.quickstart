<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>



<div class="system-auth-registration system-auth-registration-default">
	<?
	ShowMessage($arParams['~AUTH_RESULT']);
	
	if ($arResult['USE_EMAIL_CONFIRMATION'] === 'Y'
		&& is_array($arParams['AUTH_RESULT'])
		&& $arParams['AUTH_RESULT']['TYPE'] === 'OK'
	) {
		ShowNote(GetMessage('AUTH_EMAIL_SENT'));
	} else {
		if ($arResult['USE_EMAIL_CONFIRMATION'] === 'Y') {
			ShowNote(GetMessage('AUTH_EMAIL_WILL_BE_SENT'));
		}
		
		ShowError($arResult['ERROR_MESSAGE']);
		?>
		
		<h2><?=GetMessage('AUTH_REGISTER')?></h2>
		<div class="row">
			<div class="col-lg-4 col-md-5 col-sm-6">
				<div class="form form-registration">
					<form method="post" action="<?=$arResult['AUTH_URL']?>" role="registration">
						<?if ($arResult['BACKURL']) {
							?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>"/><?
						}?>
						<input type="hidden" name="AUTH_FORM" value="Y"/>
						<input type="hidden" name="TYPE" value="REGISTRATION"/>
						
						<?/*
						<div class="form-group">
							<label class="control-label" for="system-auth-registration-name"><?=GetMessage('AUTH_NAME')?>:</label>
							<input class="form-control" type="text" name="USER_NAME" id="system-auth-registration-name" value="<?=$arResult['USER_NAME']?>" maxlength="50" autofocus=""/>
						</div>
						<div class="form-group">
							<label class="control-label" for="system-auth-registration-lastname"><?=GetMessage('AUTH_LAST_NAME')?>:</label>
							<input class="form-control" type="text" name="USER_LAST_NAME" id="system-auth-registration-lastname" value="<?=$arResult['USER_LAST_NAME']?>" maxlength="50"/>
						</div>
						*/?>
						<div class="form-group">
							<label class="control-label required" for="system-auth-registration-email"><?=GetMessage('AUTH_EMAIL')?>:</label>
							<input class="form-control" type="email" autofocus="autofocus" name="USER_EMAIL" id="system-auth-registration-email" value="<?=$arResult['USER_EMAIL']?>" maxlength="255" required=""/>
						</div>
						<div class="form-group has-feedback">
							<label class="control-label required" for="system-auth-registration-password">Придумайте пароль (не менее 6 символов):</label>
							<input class="form-control" type="password" name="USER_PASSWORD" id="system-auth-registration-password" value="<?=$arResult['USER_PASSWORD']?>" maxlength="50" required=""/>
							<?if ($arResult['SECURE_AUTH']) {
								?>
								<noscript>
									<span class="glyphicon glyphicon-unlock form-control-feedback" title="<?=GetMessage('AUTH_NONSECURE_NOTE')?>"></span>
								</noscript>
								<script>
									document.write('<span class="glyphicon glyphicon-lock form-control-feedback" title="<?=GetMessage('AUTH_SECURE_NOTE')?>"></span>');
								</script>
								<?
							} else {
								?><span class="glyphicon glyphicon-eye-open form-control-feedback" title="<?=GetMessage('AUTH_SHOW_PASS')?>" data-hide-title="<?=GetMessage('AUTH_HIDE_PASS')?>"></span><?
							}?>
						</div>
						
						<?if($arResult['USER_PROPERTIES']['SHOW'] == 'Y') {
							foreach ($arResult['USER_PROPERTIES']['DATA'] as $userField) {
								?>
								<div class="form-group">
									<label class="control-label<?=$userField['MANDATORY'] == 'Y' ? ' required' : ''?>" for="system-auth-registration-<?=$userField['ID']?>"><?=$userField['EDIT_FORM_LABEL']?>:</label>
									<?$APPLICATION->IncludeComponent(
										'bitrix:system.field.edit',
										$userField['USER_TYPE']['USER_TYPE_ID'],
										array(
											'bVarsFromForm' => $arResult['bVarsFromForm'],
											'arUserField' => $userField,
											'domID' => 'system-auth-registration-' . $userField['ID'],
										),
										null,
										array(
											'HIDE_ICONS' => 'Y'
										)
									)?>
								</div>
								<?
							}
						}?>
						
						<?if ($arResult['USE_CAPTCHA'] == 'Y') {
							?>
							<div class="form-group group-captcha">
								<label class="control-label required" for="system-auth-registration-captcha">
									<?=GetMessage('CAPTCHA_REGF_PROMT')?>:
								</label>
								<div class="row">
									<div class="col-xs-6">
										<img
											class="form-captcha-img"
											src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>"
											alt="captcha"
										/>
									</div>
									<div class="col-xs-6">
										<input
											class="form-control"
											type="text"
											name="captcha_word"
											id="system-auth-registration-captcha"
											required=""
											value=""
										/>
									</div>
								</div>
								<input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>"/>
							</div>
							<?
						}?>
						
                        <div class="form-group form-info help-block">
                            <?=GetMessage('AUTH_AGREE')?>
                        </div>
                        
                        <div class="form-group form-toolbar row">
							<div class="col-sm-6">
                                <input type="hidden" name="USER_LOGIN" value="<?=$arResult['USER_LOGIN']?>"/>
							    <input type="hidden" name="USER_CONFIRM_PASSWORD" value="<?=$arResult['USER_CONFIRM_PASSWORD']?>"/>
							    <input class="btn btn-default" type="submit" value="<?=GetMessage('AUTH_SUBMIT')?>"/>
                            </div>
                            <div class="col-sm-6 help-block">
                                Все поля обязательны для заполнения
                            </div>
						</div>
						
					</form>
					
					<?if ($arResult['AUTH_SERVICES']) {
						?>
						<div class="form-group form-socserv">
							<label class="control-label"><?=getMessage('AUTH_SOCSERV')?>:</label>
							<div>
								<?$APPLICATION->IncludeComponent(
									'bitrix:socserv.auth.form',
									'',
									array(
										'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
										'CURRENT_SERVICE' => $arResult['CURRENT_SERVICE'],
										'AUTH_URL' => $arResult['AUTH_URL'],
										'POST' => $arResult['POST'],
										'SHOW_TITLES' => 'N',
										'FOR_SPLIT' => 'Y',
									), 
									$component,
									array(
										'HIDE_ICONS' => 'Y',
									)
								)?>
							</div>
						</div>
						<?
					}?>
					
					<div class="form-group form-info">
						<p>
							<a href="<?=$arResult['AUTH_AUTH_URL']?>" rel="nofollow"><?=GetMessage('AUTH_AUTH')?></a>
						</p>
					</div>
				</div>
			</div>
			
			<div class="col-lg-8 col-md-7 col-sm-6 hidden-xs">
				<?=GetMessage('TEMPLATE_REGISTER_INFO')?>
			</div>
		</div>
		<?
	}?>
</div>