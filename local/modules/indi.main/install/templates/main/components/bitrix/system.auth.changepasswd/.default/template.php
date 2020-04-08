<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
if ($arParams['~AUTH_RESULT']['TYPE'] == 'OK') {
	ShowMessage($arParams['~AUTH_RESULT']);
	return;
}
?>

<div class="system-auth-changepasswd system-auth-changepasswd-default">
	<div class="row">
		<div class="col-sm-6 col-md-5 col-lg-4">
			<?ShowMessage($arParams['~AUTH_RESULT'])?>
			
			<form class="form form-changepasswd" method="post" action="<?=$arResult['AUTH_FORM']?>" role="changepasswd">
				<?if ($arResult['BACKURL']) {
					?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>"/><?
				}?>
				<input type="hidden" name="AUTH_FORM" value="Y"/>
				<input type="hidden" name="TYPE" value="CHANGE_PWD"/>
				
				<div class="form-group">
					<label class="control-label" for="changepasswd-login"><?=GetMessage('AUTH_LOGIN')?>:</label>
					<input class="form-control" type="text" name="USER_LOGIN" id="changepasswd-login" maxlength="50" value="<?=$arResult['LAST_LOGIN']?>" autofocus="autofocus" required=""/>
				</div>
				<div class="form-group">
					<label class="control-label" for="changepasswd-checkword"><?=GetMessage('AUTH_CHECKWORD')?>:</label>
					<input class="form-control" type="text" name="USER_CHECKWORD" id="changepasswd-checkword" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" required=""/>
				</div>
				<div class="form-group has-feedback">
					<label class="control-label" for="changepasswd-password"><?=GetMessage('AUTH_NEW_PASSWORD_REQ')?>:</label>
					<input class="form-control" type="password" name="USER_PASSWORD" id="changepasswd-password" maxlength="50" value="<?=$arResult['USER_PASSWORD']?>" required="" autocomplete="off" />
					<span class="glyphicon glyphicon-eye-open form-control-feedback" title="<?=GetMessage('AUTH_SHOW_PASS')?>" data-hide-title="<?=GetMessage('AUTH_HIDE_PASS')?>"></span>
				</div>
				<div class="form-group form-toolbar">
					<div class="row">
						<div class="col-sm-6">
							<input type="hidden" name="USER_CONFIRM_PASSWORD" value="<?=$arResult['USER_CONFIRM_PASSWORD']?>"/>
							<input class="form-control btn btn-default" type="submit" value="<?=GetMessage('AUTH_CHANGE')?>"/>
						</div>
						<div class="col-sm-6">
							<span class="help-block">Все поля обязательны для заполнения</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<p>
						<a href="<?=$arResult['AUTH_AUTH_URL']?>"><?=GetMessage('AUTH_AUTH')?></a>
					</p>
				</div>
			</form>
		</div>
	</div>
</div>