<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<section class="step-identity">
	<div class="row">
		<div class="col-sm-6 col-md-5">
			<div class="panel panel-default">
				<article class="panel-body">
					<h2><?=GetMessage('SOA_2REG')?></h2>
					<?if ($arResult['IDENTITY']['SHOW_OLD_FORM']) {
						?>
						<form class="form form-auth" method="post" action="" role="authorize">
							<?=bitrix_sessid_post()?>
							<?/*foreach ((array) $arResult['DATA'] as $key => $value) {
								?><input type="hidden" name="<?=$key?>" value="<?=$value?>"/><?
							}*/?>
							
							<div class="form-group">
								<label class="control-label" for="order-auth-login"><?=GetMessage('SOA_LOGIN')?>:</label>
								<input class="form-control" type="text" name="USER_LOGIN" id="order-auth-login" maxlength="30" value="<?=$arResult['DATA']['USER_LOGIN']?>" autofocus="autofocus" required=""/>
							</div>
							<div class="form-group has-feedback">
								<label class="control-label" for="order-auth-password"><?=GetMessage('SOA_PASSWORD')?>:</label>
								<?if ($arParams['PATH_TO_PERSONAL']) {
									?><a class="forgot-pass" href="<?=$arParams['PATH_TO_PERSONAL']?>?forgot_password=yes&back_url=<?=urlencode($APPLICATION->GetCurPageParam())?>" rel="nofollow"><?=GetMessage('SOA_FORGET_PASSWORD')?></a><?
								}?>
								<input class="form-control" type="password" name="USER_PASSWORD" id="order-auth-password" value="<?=$arResult['DATA']['USER_PASSWORD']?>" required=""/>
								<span class="glyphicon glyphicon-eye-open form-control-feedback" title="<?=GetMessage('SOA_SHOW_PASS')?>" data-hide-title="<?=GetMessage('SOA_HIDE_PASS')?>"></span>
							</div>
							<?if ($arResult['IDENTITY']['STORE_PASSWORD']) {
								?>
								<div class="form-group">
									<label class="checkbox-inline">
										<input type="checkbox" name="USER_REMEMBER" value="Y"<?=$arResult['DATA']['USER_REMEMBER'] == 'Y' ? ' checked=""' : ''?>/>
										<?=GetMessage('SOA_REMEMBER_ME')?>
									</label>
								</div>
								<?
							}?>
							
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group form-toolbar">
										<input class="form-control btn btn-default" type="submit" value="<?=GetMessage('SOA_DO_LOGIN')?>"/>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group form-toolbar">
										<span class="help-block"><?=GetMessage('SOA_ALL_REQUIRED')?></span>
									</div>
								</div>
							</div>
							
							<?if ($arResult['AUTH_SERVICES']) {
								?>
								<div class="form-group form-socserv">
									<label class="control-label"><?=GetMessage('SOA_THROUGH_EXTERNAL')?>:</label>
									<div>
										<?$APPLICATION->IncludeComponent(
											'bitrix:socserv.auth.form',
											'',
											array(
												'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
												'CURRENT_SERVICE' => $arResult['CURRENT_SERVICE'],
												'AUTH_URL' => $arResult['AUTH_URL'],
												'POST' => $arResult['DATA'],
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
							
							<input type="hidden" name="action" value="return"/>
						</form>
						<?
					}?>
					
					<?if ($arResult['IDENTITY']['ACTION'] == 'return') {
						ShowError(implode("\n", $arResult['ERRORS']));
						ShowNote(implode("\n", $arResult['NOTES']));
					}?>
				</article>
			</div>
		</div>
		<div class="col-sm-6 col-md-7">
			<div class="panel panel-default">
				<article class="panel-body">
					<h2><?=GetMessage('SOA_2NEW')?></h2>
					<?if ($arResult['IDENTITY']['SHOW_NEW_FORM']) {
						?>
						<form class="form form-registration" method="post" action="" role="registration">
							<?=bitrix_sessid_post()?>
							<?/*foreach ((array) $arResult['DATA'] as $key => $value) {
								?><input type="hidden" name="<?=$key?>" value="<?=$value?>"/><?
							}*/?>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label required" for="order-registration-email"><?=GetMessage('SOA_EMAIL')?>:</label>
										<input class="form-control" type="email" name="NEW_EMAIL" id="order-registration-email" maxlength="30" value="<?=$arResult['DATA']['NEW_EMAIL']?>" required=""/>
									</div>
									<div class="form-group has-feedback">
										<label class="control-label required" for="order-registration-password"><?=GetMessage('SOA_NEW_PASSWORD')?>:</label>
										<input class="form-control" type="password" name="NEW_PASSWORD" id="order-registration-password" value="<?=$arResult['DATA']['NEW_PASSWORD']?>" required=""/>
										<span class="glyphicon glyphicon-eye-open form-control-feedback" title="<?=GetMessage('SOA_SHOW_PASS')?>" data-hide-title="<?=GetMessage('SOA_HIDE_PASS')?>"></span>
									</div>
									<div class="form-group">
										<label class="control-label" for="order-registration-name"><?=GetMessage('SOA_NAME')?>:</label>
										<input class="form-control" type="text" name="NEW_NAME" id="order-registration-name" maxlength="30" value="<?=$arResult['DATA']['NEW_NAME']?>"/>
									</div>
									<?if ($arResult['IDENTITY']['SPAM_REQUEST']) {
										?>
										<div class="form-group">
											<label class="checkbox-inline">
												<input type="checkbox" name="UF_SPAM" value="Y"<?=$arResult['DATA']['UF_SPAM'] == 'Y' ? ' checked=""' : ''?>/>
												<?=GetMessage('SOA_WANT_SPAM')?>
											</label>
										</div>
										<?
									}?>
								</div>
								<div class="col-md-6">
									<?=GetMessage('TEMPLATE_REGISTER_INFO')?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-5">
									<div class="form-group form-toolbar">
										<input class="form-control btn btn-default" type="submit" value="<?=GetMessage('SOA_DO_REGISTER')?>"/>
									</div>
								</div>
								<div class="col-md-7">
									<div class="form-group form-toolbar">
										<span class="help-block"><span class="required"></span><?=GetMessage('SOA_REQUIRED_FIELDS')?></span>
									</div>
								</div>
							</div>
							<input type="hidden" name="action" value="register"/>
						</form>
						<?
					}?>
					
					<?if ($arResult['IDENTITY']['ACTION'] == 'register') {
						ShowError(implode("\n", $arResult['ERRORS']));
						ShowNote(implode("\n", $arResult['NOTES']));
					}?>
				</article>
			</div>
		</div>
	</div>
</section>