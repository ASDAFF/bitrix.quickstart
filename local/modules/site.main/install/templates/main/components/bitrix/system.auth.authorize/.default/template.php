<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

ShowMessage($arParams['~AUTH_RESULT']);
ShowMessage($arResult['ERROR_MESSAGE']);
?>

<div class="system-auth-authorize system-auth-authorize-default">
	<div class="row">
		<div class="col-lg-4 col-md-5 col-sm-6">
			<form class="form form-auth" method="post" target="_top" action="<?=$arResult['AUTH_URL']?>" role="authorize">
				<input type="hidden" name="AUTH_FORM" value="Y"/>
				<input type="hidden" name="TYPE" value="AUTH"/>
				
				<?if ($arResult['BACKURL']) {
					?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>"/><?
				}?>
				
				<?foreach ($arResult['POST'] as $key => $value) {
					?><input type="hidden" name="<?=$key?>" value="<?=$value?>"/><?
				}?>
				
				<div class="form-group">
					<label class="control-label" for="auth-login"><?=GetMessage('AUTH_LOGIN')?>:</label>
					<input class="form-control" type="email" name="USER_LOGIN" id="auth-login" maxlength="255" value="<?=$arResult['LAST_LOGIN']?>"<?=$arResult['LAST_LOGIN'] ? '' : ' autofocus=""'?> required=""/>
				</div>
				
				<div class="form-group has-feedback">
					<label class="control-label" for="auth-password"><?=GetMessage('AUTH_PASSWORD')?>:</label>
					<?if ($arParams['NOT_SHOW_LINKS'] != 'Y') {
						?><a class="forgot-pass" href="<?=$arResult['AUTH_FORGOT_PASSWORD_URL']?>" rel="nofollow"><?=GetMessage('AUTH_FORGOT_PASSWORD_2')?></a><?
					}?>
					<input class="form-control" type="password" name="USER_PASSWORD" id="auth-password" maxlength="255" <?=$arResult['LAST_LOGIN'] ? 'autofocus=""' : ''?> required=""/>
					<span class="glyphicon glyphicon-eye-open form-control-feedback" title="<?=GetMessage('AUTH_SHOW_PASS')?>" data-hide-title="<?=GetMessage('AUTH_HIDE_PASS')?>"></span>
				</div>
				
				<?if ($arResult['STORE_PASSWORD'] == 'Y') {
					?>
					<div class="form-group">
						<label class="checkbox-inline">
							<input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y"/>
							<?=GetMessage('AUTH_REMEMBER_ME')?>
						</label>
					</div>
					<?
				}?>
				
				<?if ($arResult['CAPTCHA_CODE']) {
					?>
					<div class="form-group group-captcha">
						<label class="control-label required" for="auth-captcha"><?=GetMessage('AUTH_CAPTCHA_PROMT')?>:</label>
						<div class="row">
							<div class="col-md-4 col-xs-6">
								<input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>"/>
								<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" alt="captcha"/>
							</div>
							<div class="col-md-8 col-xs-6">
								<input class="form-control" name="captcha_word" id="auth-captcha" required="" value="" type="text"/>
							</div>
						</div>
					</div>
					<?
				}?>
				
				<div class="form-group form-toolbar">
					<div class="row">
						<div class="col-sm-6">
							<input class="form-control btn btn-default" type="submit" value="<?=GetMessage('AUTH_AUTHORIZE')?>"/>
						</div>
						<div class="col-sm-6">
							<span class="help-block"><?=getMessage('AUTH_REQUIRED')?></span>
						</div>
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
			
			<?if ($arParams['NOT_SHOW_LINKS'] != 'Y'
				&& $arResult['NEW_USER_REGISTRATION'] == 'Y'
				&& $arParams['AUTHORIZE_REGISTRATION'] != 'Y'
			) {
				?>
				<div class="form-group form-info">
					<noindex>
						<p><?=getMessage('AUTH_NO_ACC_QUESTION')?> <a href="<?=$arResult['AUTH_REGISTER_URL']?>" rel="nofollow"><?=getMessage('AUTH_NO_ACC_ACTION')?></a></p>
					</noindex>
				</div>
				<?
			}?>
			
		</div>
		
		<div class="col-lg-8 col-md-7 col-sm-6 hidden-xs">
			<?=GetMessage('TEMPLATE_REGISTER_INFO')?>
		</div>
	</div>
</div>