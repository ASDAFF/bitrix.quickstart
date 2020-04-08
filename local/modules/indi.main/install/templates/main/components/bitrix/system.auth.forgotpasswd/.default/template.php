<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if ($arParams['~AUTH_RESULT']['TYPE'] == 'OK') {
	ShowNote('Контрольная строка, а также ваши регистрационные данные были высланы по Email. Пожалуйста, дождитесь прихода письма, так как контрольная строка изменяется при каждом запросе.');
	return;
} elseif ($arParams['~AUTH_RESULT']['TYPE'] == 'ERROR') {
	ShowError('Email не найден.');
}
?>

<div class="system-auth-forgotpasswd system-auth-forgotpasswd-default">
	<div class="row">
		<div class="col-md-8 col-lg-6">
			<p><?=GetMessage('AUTH_FORGOT_PASSWORD_1')?></p>
			<h2><?=GetMessage('AUTH_GET_CHECK_STRING')?></h2>
			
			<form class="form form-forgotpassword" method="post" action="<?=$arResult['AUTH_URL']?>" role="forgotpassword">
				<?if ($arResult['BACKURL']) {
					?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>"/><?
				}?>
				<input type="hidden" name="AUTH_FORM" value="Y"/>
				<input type="hidden" name="TYPE" value="SEND_PWD"/>
				
				<div class="form-group">
					<label class="control-label" for="forgotpassword-email">Email:</label>
					<input class="form-control" type="email" name="USER_EMAIL" id="forgotpassword-email" maxlength="50" autofocus="autofocus" required=""/>
				</div>
				<div class="form-group form-toolbar">
					<input class="btn btn-default" type="submit" value="<?=GetMessage('AUTH_SEND')?>"/>
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