<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

ShowMessage($arParams["~AUTH_RESULT"]);

?>
<div class="page_wrapper">
	<div class="forgot_pass_left">
		<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
		<?
		if (strlen($arResult["BACKURL"]) > 0)
		{
		?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?
		}
		?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="SEND_PWD">


		<br/>
		<h4 class="auth_form_title"><?=GetMessage("AUTH_GET_CHECK_STRING")?></h4>
		<table class="data-table bx-forgotpass-table">
			<tbody>
				<tr>
					<td><label for="USER_LOGIN"><?=GetMessage("AUTH_LOGIN")?></label></td>
					<td>
						<input type="text" name="USER_LOGIN" id="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" />
					</td>
				</tr>
				<tr>
					<td></td>
					<td><div class="forgot_pass_or"><?=GetMessage("AUTH_OR")?></div></td>
				</tr>
				<tr>
					<td><label for="USER_EMAIL"><?=GetMessage("AUTH_EMAIL")?></label></td>
					<td>
						<input type="text" name="USER_EMAIL" id="USER_EMAIL" maxlength="255" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td>
						<input class="login_button" type="submit" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" />
					</td>
				</tr>
			</tfoot>
		</table>
		</form>
	</div>
	<div class="forgot_pass_right">
		<p><?=GetMessage("AUTH_FORGOT_PASSWORD_1")?></p>
		<h4 class="auth_form_title"><?=GetMessage("AUTH_AUTHORIZE")?></h4>
		<p><?=GetMessage("AUTH_REMEMBER")?><a href="<?=$arResult["AUTH_AUTH_URL"]?>"><?=GetMessage("AUTH_AUTH")?></a></p>
	</div>
	<div class="splitter"></div>

</div>
<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>
